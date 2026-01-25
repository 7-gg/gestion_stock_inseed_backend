<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Requests\StockMovement\StoreStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\Stock;
use App\Services\StockMovementService;
use App\Exceptions\BusinessException;
use App\Http\Requests\StockMovementRequest;
use App\Models\StockMovement;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StockMovementController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    public function __construct(protected StockMovementService $service) {}


    public function index(Request $request, StockProduct $stockProduct)
    {
        $filters = $request->only(['movement', 'start', 'end']);
        $movements = $this->service->list($stockProduct, $filters);

        return $this->success(
            StockMovementResource::collection($movements),
            'Liste des mouvements du stock'
        );
    }

    public function store(StockMovementRequest $request)
    {
        $this->authorize('create', [$request->user(), $request->stock_product_id]);

        $movement = $this->service->create($request->validated());

        return $this->created(
            new StockMovementResource($movement->load(['stockProduct', 'creator', 'validator'])),
            'Mouvement créé'
        );
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

    /**
     * Liste des mouvements d’un produit spécifique dans un stock
     */
    public function productMovements(Request $request, StockProduct $stockProduct)
    {
        $filters = $request->only(['movement', 'start', 'end']);
        $movements = $this->service->list($stockProduct, $filters);

        return $this->success(
            StockMovementResource::collection($movements),
            'Mouvements du produit dans le stock'
        );
    }
}
