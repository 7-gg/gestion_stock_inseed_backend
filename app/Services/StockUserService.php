<?php

namespace App\Services;

use App\Models\StockUser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class StockUserService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = StockUser::query()->with(['stock', 'user']);

        if (!empty($filters['stock_id'])) {
            $query->where('stock_id', $filters['stock_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->paginate(15);
    }

    public function create(array $data): StockUser
    {
        return DB::transaction(function () use ($data) {

            // 1️⃣ Empêcher un user actif deux fois sur le même stock
            $alreadyActive = StockUser::where('stock_id', $data['stock_id'])
                ->where('user_id', $data['user_id'])
                ->lockForUpdate()
                ->exists();

            if ($alreadyActive) {
                throw ValidationException::withMessages([
                    'user_id' => 'Cet utilisateur est déjà affecté à ce stock.',
                ]);
            }

            // 2️⃣ Empêcher plusieurs chefs actifs
            if (!empty($data['is_chief']) && $data['is_chief']) {
                $chiefExists = StockUser::where('stock_id', $data['stock_id'])
                    ->where('is_chief', true)
                    ->lockForUpdate()
                    ->exists();

                if ($chiefExists) {
                    throw ValidationException::withMessages([
                        'is_chief' => 'Ce stock a déjà un responsable actif.',
                    ]);
                }
            }

            // 3️⃣ Création
            return StockUser::create($data);
        });
    }

    public function update(StockUser $stockUser, array $data): StockUser
    {
        $stockUser->update($data);
        return $stockUser;
    }

    public function delete(StockUser $stockUser): bool
    {
        return $stockUser->delete();
    }
}
