<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Requests\Stock\StoreStockRequest;
use App\Http\Requests\Stock\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Services\StockService;
use App\Models\Stock;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StockController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    public function __construct(protected StockService $service) {}

    public function store(StoreStockRequest $request)
    {
        try {
            $stock = $this->service->create(array_merge($request->validated(), ['created_by' => $request->user()->id]));
            return $this->created(new StockResource($stock));
        } catch (BusinessException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function index(Request $request)
    {
        $stocks = $this->service->list(
            $request->only(['search', 'page'])
        );

        return $this->success([
            'items' => StockResource::collection($stocks->items()),
            'meta' => [
                'total'        => $stocks->total(),
                'per_page'     => $stocks->perPage(),
                'current_page' => $stocks->currentPage(),
                'last_page'    => $stocks->lastPage(),
            ],
        ]);
    }

    public function show(Stock $stock)
    {
        $this->authorize('view', $stock);

        return $this->success(new StockResource($stock));
    }

    public function update(UpdateStockRequest $request, Stock $stock)
    {
        $this->authorize('create', Stock::class);

        $this->service->update($stock, $request->validated());

        return $this->success(new StockResource($stock), 'Stock updated');
    }

    public function destroy(Stock $stock)
    {
        $this->authorize('create', Stock::class);

        $this->service->delete($stock);

        return $this->success(null, 'Stock deleted');
    }

    public function count()
    {
        $data = Stock::count();

        return $this->success($data, 'Stocks count retrieved');
    }
}
