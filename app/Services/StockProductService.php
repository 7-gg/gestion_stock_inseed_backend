<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockProduct;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockProductService
{
    public function list(Stock $stock, array $filters = []): LengthAwarePaginator
    {
        $query = $stock->stockProducts()->with('product');
        if (!empty($filters['provider'])) {
            $query->where('provider', 'like', "%{$filters['provider']}%");
        }
        return $query->paginate(15);
    }

    public function create(Stock $stock, array $data): StockProduct
    {
        $data['stock_id'] = $stock->id;
        return StockProduct::create($data);
    }

    public function update(StockProduct $stockProduct, array $data): StockProduct
    {
        return DB::transaction(function () use ($stockProduct, $data) {
            $stockProduct->update($data);
            return $stockProduct;
        });
    }

    public function delete(StockProduct $stockProduct): bool
    {
        return $stockProduct->delete();
    }
}
