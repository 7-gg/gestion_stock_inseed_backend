<?php

namespace App\Policies;

use App\Models\StockProduct;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StockProduct $stockProduct): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $stockProduct->stock_id)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StockProduct $stockProduct): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->stocks()
            ->where('stocks.id', $stockProduct->stock_id)
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at')
            ->exists();
    }
}
