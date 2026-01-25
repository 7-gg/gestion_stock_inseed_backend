<?php

namespace App\Services;

use App\Models\StockProduct;
use Illuminate\Support\Facades\DB;

/**
 * Service responsible for attaching products to stocks and simple stock-product operations.
 */
class ProductStockService
{
    /**
     * Attach a product to a stock or return existing record. Runs in a transaction when creating.
     */
    public function attachProduct(int $stockId, int $productId, int $quantity = 0, ?int $minimum = null): StockProduct
    {
        return DB::transaction(function () use ($stockId, $productId, $quantity, $minimum) {
            $record = StockProduct::firstOrCreate([
                'stock_id' => $stockId,
                'product_id' => $productId,
            ], [
                'quantity' => $quantity,
                'minimum_quantity' => $minimum,
            ]);

            // If record existed but we provided a quantity/minimum, update them
            $changes = [];
            if ($record->wasRecentlyCreated === false && ($record->quantity !== $quantity || $record->minimum_quantity !== $minimum)) {
                $record->fill([
                    'quantity' => $quantity,
                    'minimum_quantity' => $minimum,
                ])->save();
            }

            return $record->refresh();
        });
    }

    /**
     * Increment the quantity by $delta (positive or negative). Throws if quantity would be negative.
     */
    public function changeQuantity(int $stockId, int $productId, int $delta): StockProduct
    {
        return DB::transaction(function () use ($stockId, $productId, $delta) {
            $record = StockProduct::where('stock_id', $stockId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (! $record) {
                // Create with 0 so operations can proceed
                $record = $this->attachProduct($stockId, $productId, 0, null);
            }

            $new = $record->quantity + $delta;

            if ($new < 0) {
                throw \App\Exceptions\InsufficientStockException::forProduct($productId, $stockId, $record->quantity, abs($delta));
            }

            $record->quantity = $new;
            $record->save();

            return $record->refresh();
        });
    }

    /**
     * Returns true if stock quantity <= minimum threshold (if set).
     */
    public function isBelowMinimum(int $stockId, int $productId): bool
    {
        $record = StockProduct::where('stock_id', $stockId)->where('product_id', $productId)->first();

        if (! $record || is_null($record->minimum_quantity)) {
            return false;
        }

        return $record->quantity <= $record->minimum_quantity;
    }
}
