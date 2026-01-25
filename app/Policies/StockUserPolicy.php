<?php

namespace App\Policies;

use App\Models\StockUser;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can assign users to a stock.
     */
    public function assign(User $user, Stock $stock): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $stock->id)
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * Determine whether the user can update a stock assignment.
     */
    public function update(User $user, StockUser $stockUser): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $stockUser->stock_id)
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * Determine whether the user can remove a user from a stock.
     */
    public function delete(User $user, StockUser $stockUser): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $stockUser->stock_id)
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }
}
