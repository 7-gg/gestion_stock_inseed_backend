<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class UserService
{
    public function create(array $data): User
    {
        return User::create(Arr::only($data, ['name', 'email', 'phone', 'is_admin', 'is_manager']));
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = User::query();

        // Recherche simple
        $search = $filters['search'];
        $query->where(function ($qB) use ($search) {
            $qB->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        });


        // Filtre rôle
        if (!empty($filters['role'])) {
            switch ($filters['role']) {
                case 'admin':
                    $query->where('is_admin', true);
                    break;
                case 'manager':
                    $query->where('is_manager', true);
                    break;
            }
        }

        // Tri
        $query->orderBy('updated_at', 'desc');

        return $query->paginate(12);
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function update(User $user, array $data): User
    {
        // Supprimer toute tentative de mise à jour de is_admin
        unset($data['is_admin']);

        $user->update(Arr::only($data, ['name', 'email', 'phone', 'is_manager']));

        return $user;
    }

    public function disable(User $user): User
    {
        $user->delete();

        return $user;
    }
}
