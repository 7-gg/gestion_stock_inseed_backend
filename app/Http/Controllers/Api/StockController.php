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
use App\Exports\RestockExport;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
        $y = $this->success([
            'items' => StockResource::collection($stocks->items()),
            'meta' => [
                'total'        => $stocks->total(),
                'per_page'     => $stocks->perPage(),
                'current_page' => $stocks->currentPage(),
                'last_page'    => $stocks->lastPage(),
            ],
        ]);
        // Log::info($y);
        return $y;
    }

    public function show(int $id) // On reçoit l'ID (int) et non le modèle (Stock)
    {
        // On appelle la méthode find du service qui contient maintenant le withCount
        $stock = $this->service->find($id);

        if (!$stock) {
            return $this->error("Stock non trouvé", 404);
        }

        // On vérifie l'autorisation sur l'objet récupéré
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

    public function exportRestock()
    {
        // On récupère TOUS les produits en alerte (pas de groupBy ici)
        $products = StockProduct::query()
            ->where(function ($q) {
                $q->where('quantity', '<=', 'minimum_quantity');
            })
            ->with(['product', 'stock'])
            ->get();

        Log::info($products);

        return Excel::download(
            new RestockExport($products),
            'etat_reapprovisionnement_' . now()->format('d_m_Y') . '.xlsx'
        );
    }
}
