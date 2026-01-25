<?php

namespace App\Policies;

use App\Models\AdminHistory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminHistoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdminHistory $history): bool
    {
        return $user->is_admin;
    }
}
