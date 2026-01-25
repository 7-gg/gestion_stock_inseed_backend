<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;

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
        $products = $this->service->list($request->only('q'));

        return $this->success(['items' => ProductResource::collection($products->items()), 'meta' => [
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
        ]]);
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
}
