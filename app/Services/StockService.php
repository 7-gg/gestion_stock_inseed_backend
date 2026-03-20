<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class StockService
{
    public function create(array $data): Stock
    {
        return Stock::create(Arr::only($data, ['name', 'description', 'location', 'created_by']));
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Stock::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where('name', 'like', "%{$search}%");
        }

        return $query
            ->with(['users', 'products'])
            ->orderBy('updated_at', 'desc')
            ->paginate(12);
    }


    public function find(int $id): ?Stock
    {
        return Stock::find($id);
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->update(Arr::only($data, ['name', 'description', 'location']));

        return $stock;
    }

    public function delete(Stock $stock): void
    {
        $stock->delete();
    }
}
