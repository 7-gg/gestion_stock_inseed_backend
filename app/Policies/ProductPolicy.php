<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\StockProduct;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Create product: admin OR user who is chief on any stock.
     */
    public function create(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * View a product: admin OR user with access to a stock that contains the product.
     */
    public function view(User $user, Product $product): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $stockIds = $user->stocks()
            ->whereNull('stock_users.ended_at')
            ->pluck('stocks.id');

        if ($stockIds->isEmpty()) {
            return false;
        }

        return StockProduct::where('product_id', $product->id)
            ->whereIn('stock_id', $stockIds)
            ->exists();
    }

    /**
     * Update a product: admin OR user who is chief on any stock containing the product.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $stockIds = $user->stocks()
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->pluck('stocks.id');

        if ($stockIds->isEmpty()) {
            return false;
        }

        return StockProduct::where('product_id', $product->id)
            ->whereIn('stock_id', $stockIds)
            ->exists();
    }

    /**
     * Delete a product: admin only.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->is_admin;
    }
}
