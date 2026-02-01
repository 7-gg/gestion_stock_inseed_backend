<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockProduct;
use App\Http\Requests\StockProductRequest;
use App\Http\Resources\StockProductResource;
use App\Services\StockProductService;
use App\Traits\ApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

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
        $filters = $request->only(['provider']);
        $stockProducts = $this->service->list($stock, $filters);
        return $this->success(
            StockProductResource::collection($stockProducts),
            'Liste des produits du stock'
        );
    }

    /** * Ajouter un produit dans un stock */
    public function store(StockProductRequest $request, Stock $stock)
    {
        $this->authorize('assignProduct', $stock);
        // il faut ajouter $stock->id a $request->validated()
        $stockProduct = $this->service->create($stock, $request->validated());
        return $this->created(
            new StockProductResource($stockProduct->load(['product', 'stock'])),
            'Produit ajouté au stock'
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

    /** * Supprimer un produit d’un stock */
    public function destroy(Stock $stock, StockProduct $stockProduct)
    {
        $this->authorize('delete', $stockProduct);
        $this->service->delete($stockProduct);
        return $this->success(null, 'Produit retiré du stock', 204);
    }
}
