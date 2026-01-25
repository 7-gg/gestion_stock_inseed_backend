<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockMovementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can create a movement.
     * Admin OR chief on the stock.
     */
    public function create(User $user, $stockId): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', is_object($stockId) ? $stockId->id : $stockId)
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * Determine if the user can view a movement.
     * Admin OR user with access to the stock.
     */
    public function view(User $user, StockMovement $movement): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $movement->stockProduct->stock_id)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * Determine if the user can validate a stock movement.
     * Admin OR chief on the stock.
     */
    public function validate(User $user, StockMovement $movement): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $movement->stockProduct->stock_id)
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }
}
