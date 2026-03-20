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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockMovementController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    public function __construct(protected StockMovementService $service) {}


    public function index(Request $request)
    {
        // On récupère tous les filtres possibles, y compris le produit
        $filters = $request->only(['movement', 'start', 'end', 'stock_product_id']);

        $movements = $this->service->list($filters);

        return $this->success(
            StockMovementResource::collection($movements),
            'Liste des mouvements récupérée avec succès'
        );
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
}
