<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockUserRequest;
use App\Http\Resources\StockUserResource;
use App\Models\Stock;
use App\Models\StockUser;
use App\Services\StockUserService;
use App\Traits\ApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockUserController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    protected StockUserService $service;

    public function __construct(StockUserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Stock $stock)
    {
        // Log::info("StockUserController index pour le Stock ID: {$stock->id}");

        $filters['stock_id'] = $stock->id;

        $stockUsers = $this->service->list($filters);

        return $this->success(
            StockUserResource::collection($stockUsers),
            "Liste des utilisateurs du stock : {$stock->name}"
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockUserRequest $request)
    {
        $stock = Stock::findOrFail($request->stock_id);
        $this->authorize('assignUser', $stock);

        $stockUser = $this->service->create($request->validated());

        $result = $stockUser->load(['stock', 'user']);
        return $this->created(
            new StockUserResource($result),
            'Affectation créée'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(StockUser $stockUser)
    {
        return $this->success(
            new StockUserResource($stockUser->load(['stock', 'user'])),
            'Détails de l’affectation'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockUserRequest $request, StockUser $stockUser)
    {
        $this->authorize('update', $stockUser);

        $updated = $this->service->update($stockUser, $request->validated());

        return $this->success(
            new StockUserResource($updated->load(['stock', 'user'])),
            'Affectation mise à jour'
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock, StockUser $stockUser)
    {
        $this->authorize('delete', $stockUser);

        $stockUser->delete();

        return $this->success(null, 'Affectation supprimée');
    }
}
