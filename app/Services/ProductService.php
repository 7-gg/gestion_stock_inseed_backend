<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ProductService
{
    public function create(array $data): Product
    {
        return Product::create(Arr::only($data, [
            'name',
            'created_by',
            'category_id',
            'unit_id',
            'characteristics',
            'history',
        ]));
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('unit', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }

        // Tri par date de mise à jour décroissante
        $query->orderBy('updated_at', 'desc');

        return $query
            ->with(['creator', 'category', 'unit'])
            ->paginate(15);
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update(Arr::only($data, [
            'name',
            'category_id',
            'unit_id',
            'characteristics',
            'history',
        ]));

        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
