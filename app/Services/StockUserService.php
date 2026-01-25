<?php

namespace App\Services;

use App\Models\StockUser;
use Illuminate\Pagination\LengthAwarePaginator;

class StockUserService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = StockUser::query()->with(['stock', 'user']);

        if (!empty($filters['stock_id'])) {
            $query->where('stock_id', $filters['stock_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->paginate(15);
    }

    public function create(array $data): StockUser
    {
        return StockUser::create($data);
    }

    public function update(StockUser $stockUser, array $data): StockUser
    {
        $stockUser->update($data);
        return $stockUser;
    }

    public function delete(StockUser $stockUser): bool
    {
        return $stockUser->delete();
    }
}
