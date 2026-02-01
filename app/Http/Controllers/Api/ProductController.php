<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\StockProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use ApiResponses;

    public function __construct(protected ProductService $service) {}

    public function store(StoreProductRequest $request)
    {
        $product = $this->service->create(array_merge(
            $request->validated(),
            ['created_by' => $request->user()->id]
        ));

        return $this->created(new ProductResource($product));
    }

    public function index(Request $request)
    {
        $products = $this->service->list(
            $request->only(['search', 'page', 'category_id', 'unit_id'])
        );

        return $this->success([
            'items' => ProductResource::collection($products->items()),
            'meta' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    public function show(Product $product)
    {
        return $this->success(new ProductResource($product));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'category_id'    => 'nullable|exists:product_categories,id',
            'unit_id'        => 'nullable|exists:product_units,id',
            'characteristics' => 'nullable|array',
            'history'        => 'nullable|array',
        ]);

        $updated = $this->service->update($product, $data);

        return response()->json($updated);
    }

    public function destroy(Product $product)
    {
        $this->service->delete($product);

        return response()->json(null, 204);
    }

    public function toRestock()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // On commence la requête sur les produits en dessous du minimum
        $query = StockProduct::query()
            ->whereColumn('quantity', '<', 'minimum_quantity');

        // Filtre par stocks assignés si ce n'est pas un admin
        if (! $user->is_admin) {
            $query->whereHas('stock.users', fn($q) => $q->where('users.id', $user->id));
        }

        // On eager load les relations utiles
        $query->with([
            'product:id,name,category_id,unit_id',
            'product.category:id,name',
            'product.unit:id,name',
            'stock:id,name,location'
        ]);

        // Pour chaque stock, on prend seulement le produit avec la plus faible quantité relative
        $stockProducts = $query
            ->orderByRaw('quantity / NULLIF(minimum_quantity,0) ASC') // ratio pour comparer niveau par rapport au minimum
            ->get()
            ->groupBy('stock_id')
            ->map(fn($items) => $items->first());

        return $this->success(
            $stockProducts->values(), // reset keys
            'Produits à réapprovisionner (un par stock)'
        );
    }

    public function count()
    {
        $data = [
            'categories' => ProductCategory::count(),
            'products'   => Product::count(),
            'units'      => ProductUnit::count(),
            'allCategories'   => ProductCategory::all(),
            'allUnits'      => ProductUnit::all(),
        ];

        return $this->success($data, 'Products count retrieved');
    }
}
