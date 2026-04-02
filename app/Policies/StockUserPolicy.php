<?php

namespace App\Policies;

use App\Models\StockUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockUserPolicy
{
    use HandlesAuthorization;

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
            ->exists();
    }

    /**
     * Determine whether the user can remove a user from a stock.
     */
    public function delete(User $user, StockUser $stockUser): bool
    {
        // 1. Un administrateur peut toujours supprimer
        if ($user->is_admin) {
            return true;
        }
        // 2. Si ce n'est pas un admin, on refuse si la cible est un "chef"
        // (S'il est chef, return false. Sinon, return true).
        return !$stockUser->is_chief;
    }
}
