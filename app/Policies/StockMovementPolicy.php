<?php

namespace App\Policies;

use App\Models\Stock;
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
    public function create(User $user, Stock $stock): bool
    {
        return $user->is_admin || $user->stocks()->where('stocks.id', $stock->id)->exists();
    }


    /**
     * Determine if the user can view a movement.
     * Admin OR user with access to the stock.
     */
    public function view(User $user, StockMovement $movement): bool
    {
        if ($user->is_admin || $user->is_manager) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $movement->stockProduct->stock_id)
            ->whereNull('stock_users.deleted_at')
            ->exists();
    }

    /**
     * Determine if the user can validate a stock movement.
     * Admin OR chief on the stock.
     */
    public function validate(User $user, StockMovement $movement): bool
    {
        return $movement
            ->where('beneficiary_email', $movement->beneficiary_email)
            ->exists();
    }
}
