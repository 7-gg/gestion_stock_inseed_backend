<?php

namespace App\Policies;

use App\Models\Stock;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockPolicy
{
    use HandlesAuthorization;

    /**
     * Only admins can create stocks.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * View a stock if admin or the user has an active access to that stock.
     */
    public function view(User $user, Stock $stock): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $stock->id)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * Update a stock: admin OR chief on the stock.
     */
    public function update(User $user, Stock $stock): bool
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
     * Delete a stock: admin only.
     */
    public function delete(User $user, Stock $stock): bool
    {
        return $user->is_admin;
    }

    /**
     * Assign user to stock: admin OR chief on the stock.
     */
    public function assignUser(User $user, Stock $stock): bool
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
}
