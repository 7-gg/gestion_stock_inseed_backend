<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\StockProduct;
use Illuminate\Pagination\LengthAwarePaginator;


/**
 * Service responsible for stock movements and their side-effects on stock_product quantities.
 */
class StockMovementService
{
    public function list(StockProduct $stockProduct, array $filters = []): LengthAwarePaginator
    {
        $query = $stockProduct->movements()->with(['creator', 'validator']);

        if (!empty($filters['movement'])) {
            $query->where('movement', $filters['movement']);
        }

        if (!empty($filters['start']) && !empty($filters['end'])) {
            $query->betweenDates($filters['start'], $filters['end']);
        }

        return $query->paginate(15);
    }

    public function create(array $data): StockMovement
    {
        return StockMovement::create($data);
    }

    public function update(StockMovement $movement, array $data): StockMovement
    {
        $movement->update($data);
        return $movement;
    }

    public function delete(StockMovement $movement): bool
    {
        return $movement->delete();
    }
}
