<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->is_manager;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->is_admin || $user->id === $model->id;
    }

    /**
     * Only admins can create users.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->is_admin || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Un admin ne peut pas se supprimer lui-même
        if ($user->id === $model->id) {
            return false;
        }

        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can toggle admin status.
     */
    public function toggleAdmin(User $user, User $model): bool
    {
        // Un admin ne peut pas modifier son propre statut admin
        if ($user->id === $model->id) {
            return false;
        }

        return $user->is_admin;
    }

    /**
     * Determine whether the user can toggle manager status.
     */
    public function toggleManager(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can reset password.
     */
    public function resetPassword(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view admin history.
     */
    public function viewAdminHistory(User $user, User $model): bool
    {
        return $user->is_admin;
    }
}
