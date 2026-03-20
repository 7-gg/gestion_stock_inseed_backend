<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Http\Requests\StockProductRequest;
use App\Http\Resources\StockProductResource;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockProductService;
use App\Traits\ApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockProductController extends Controller
{
    use ApiResponses, AuthorizesRequests;
    protected StockProductService $service;

    public function __construct(StockProductService $service)
    {
        $this->service = $service;
    }

    /** * Liste des produits d’un stock */
    public function index(Request $request, Stock $stock)
    {
        // 1. Définir les relations nécessaires pour éviter les requêtes en boucle (Eager Loading)
        // On charge le produit avec sa catégorie et son unité + les mouvements
        $relations = [
            'product.category',
            'product.unit',
            'movements' => function ($query) {
                $query->latest()->limit(10); // Optionnel: ne charger que les 10 derniers mouvements
            }
        ];

        $filters = $request->only(['provider', 'search', 'category_id']);

        // 2. Récupérer les produits via le service en incluant les relations
        // Ton service devrait idéalement accepter les relations à charger
        $stockProducts = $this->service->list($stock, $filters, $relations);

        return $this->success(
            StockProductResource::collection($stockProducts),
            'Liste des produits du stock récupérée'
        );
    }

    /**
     * Ajouter un produit dans un stock
     */
    public function store(StockProductRequest $request, Stock $stock)
    {
        $this->authorize('assignProduct', $stock);

        $stockProduct = DB::transaction(function () use ($request, $stock) {
            $validatedData = $request->validated();
            $productId = $validatedData['product_id'];

            // 1. On cherche si le produit a déjà existé dans ce stock (même supprimé)
            $existing = StockProduct::withTrashed()
                ->where('stock_id', $stock->id)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                if (!$existing->trashed()) {
                    // Le produit est déjà actif, on stoppe tout pour éviter les doublons
                    throw new \Exception("Ce produit est déjà présent dans ce stock.");
                }

                // 2. RESTAURATION : Le produit était supprimé, on le réactive
                $existing->restore();

                // On met à jour avec les nouvelles valeurs de la requête
                $existing->update([
                    'quantity'         => $validatedData['quantity'] ?? 0,
                    'minimum_quantity' => $validatedData['minimum_quantity'] ?? 0,
                    'provider'         => $validatedData['provider'] ?? null,
                ]);

                $stockProduct = $existing;
            } else {
                // 3. CRÉATION CLASSIQUE : Si aucune trace passée n'existe
                $data = array_merge($validatedData, ['stock_id' => $stock->id]);
                $stockProduct = $this->service->create($stock, $data);
            }

            // 4. Création du mouvement initial (si quantité > 0)
            // Note : On le fait aussi pour une restauration pour tracer l'apport de stock
            if ($stockProduct->quantity > 0) {
                $stockProduct->movements()->create([
                    'type'       => 'ENTREE',
                    'quantity'   => $stockProduct->quantity,
                    'label'      => $existing ? 'Restauration produit / Réajustement' : 'Stock initial',
                    'created_by' => Auth::id(),
                    'stock_id'   => $stock->id,
                    'date'       => now(),
                ]);
            }

            return $stockProduct;
        });

        return $this->created(
            new StockProductResource($stockProduct->load(['product.category', 'product.unit', 'stock'])),
            'Produit ajouté au stock avec succès'
        );
    }

    /** * Afficher un produit d’un stock */
    public function show(Stock $stock, StockProduct $stockProduct)
    {
        return $this->success(
            new StockProductResource($stockProduct->load(['product', 'stock'])),
            'Détails du produit du stock'
        );
    }

    /** * Mettre à jour un produit d’un stock */
    public function update(StockProductRequest $request, Stock $stock, StockProduct $stockProduct)
    {
        $this->authorize('update', $stockProduct);
        $updated = $this->service->update($stockProduct, $request->validated());
        return $this->success(
            new StockProductResource($updated->load(['product', 'stock'])),
            'Produit du stock mis à jour'
        );
    }

    public function destroy(Stock $stock, Product $product)
    {
        Log::info("Tentative de retrait du produit du stock", [
            'product_id' => $product->id,
            'stock_id' => $stock->id
        ]);

        try {
            // 1. On récupère l'instance spécifique
            $stockProduct = StockProduct::where('stock_id', $stock->id)
                ->where('product_id', $product->id)
                ->first();

            // 2. Vérification d'existence
            if (!$stockProduct) {
                return $this->error("Ce produit n'existe pas dans ce stock", 404);
            }

            // --- NOUVELLE VÉRIFICATION ---
            // 3. Vérifier s'il y a des mouvements liés (historique)
            if ($stockProduct->movements()->exists()) {
                return $this->error(
                    "Impossible de retirer ce produit car il possède un historique de mouvements.",
                    422 // Code 422: Unprocessable Entity (erreur de logique métier)
                );
            }

            // 4. Suppression (Soft Delete car présent dans le modèle)
            $stockProduct->delete();

            return $this->success(null, 'Produit retiré du stock avec succès', 200);
        } catch (\Exception $e) {
            Log::error("Erreur suppression StockProduct: " . $e->getMessage());
            return $this->error("Erreur technique lors de la suppression", 500);
        }
    }
}
