<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Resources\StockMovementResource;
use App\Models\Stock;
use App\Services\StockMovementService;
use App\Http\Requests\StockMovementRequest;
use App\Models\StockMovement;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockMovementController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    public function __construct(protected StockMovementService $service) {}

    public function index(Request $request, Stock $stock) // On a besoin du stockId
    {
        // On récupère et on valide les filtres
        $filters = $request->only([
            'type',           // 'ENTREE' ou 'SORTIE'
            'start_date',
            'end_date',
            'product_id',    // L'ID du produit sélectionné
        ]);

        // On ajoute l'ID du stock aux filtres pour que le service sache où chercher
        $filters['stock_id'] = $stock->id;

        $movements = $this->service->list($filters);

        log::info('StockMovement index - résultats : ', ['count' => $movements->total()]);

        // Retourner directement la pagination sans l'envelopper
        return StockMovementResource::collection($movements)
            ->response()
            ->getData(true);
    }

    public function store(StockMovementRequest $request, Stock $stock)
    {
        return DB::transaction(function () use ($request, $stock) {
            $this->authorize('create', $stock);

            Log::info('StockMovement store : ', $request->all());

            // 1. Création du mouvement
            $movement = $this->service->create(
                array_merge($request->validated(), ['stock_id' => $stock->id])
            );

            // --- SECTION LOGIQUE DE STOCK (Incrémentation) ---
            $movementValue = $movement->movement instanceof \App\Enums\StockMovementType
                ? $movement->movement->value
                : $movement->movement;

            $isEntry = in_array(strtoupper($movementValue), ['ENTREE', 'IN']);
            $change = $isEntry ? $movement->quantity : -$movement->quantity;

            StockProduct::where('id', $movement->stock_product_id)
                ->increment('quantity', $change);

            // --- CRUCIAL : Recharger les relations ET les médias avant de retourner ---
            // On utilise 'media' qui est la relation par défaut de Spatie
            $movement->load(['stockProduct', 'creator', 'validator', 'media']);

            return $this->created(
                new StockMovementResource($movement),
                'Mouvement créé et stock mis à jour'
            );
        });
    }

    public function show(StockMovement $movement)
    {
        $this->authorize('view', $movement);

        return $this->success(
            new StockMovementResource($movement->load(['stockProduct', 'creator', 'validator'])),
            'Détails du mouvement'
        );
    }

    public function update(StockMovementRequest $request, StockMovement $movement)
    {
        $this->authorize('validate', $movement);

        $updated = $this->service->update($movement, $request->validated());

        return $this->success(
            new StockMovementResource($updated->load(['stockProduct', 'creator', 'validator'])),
            'Mouvement mis à jour'
        );
    }

    public function destroy(StockMovement $movement)
    {
        $this->authorize('validate', $movement);

        $this->service->delete($movement);

        return $this->success(null, 'Mouvement supprimé', 204);
    }

    public function validateMovement(StockMovement $movement)
    {
        // 1. Vérifier si l'utilisateur a le droit de valider (via ta Policy)
        $this->authorize('validate', $movement);

        // 2. Vérifier si ce n'est pas déjà validé pour éviter les doublons
        if ($movement->validated_at) {
            return $this->error('Ce mouvement a déjà été validé.', 422);
        }

        // 3. Mettre à jour les informations de validation
        $movement->update([
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        // 4. Recharger les relations pour la ressource
        $movement->load(['stockProduct.product', 'stockProduct.stock', 'creator', 'validator']);

        return $this->success(
            new StockMovementResource($movement),
            'Mouvement validé avec succès'
        );
    }
}
