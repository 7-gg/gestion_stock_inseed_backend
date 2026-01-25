<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\User;
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
        return Stock::query()
            ->orderBy('updated_at', 'desc')
            ->when(!empty($filters['q']), function ($query) use ($filters) {
                $q = $filters['q'];
                $query->where('name', 'like', "%{$q}%");
            })
            ->paginate(15);
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
