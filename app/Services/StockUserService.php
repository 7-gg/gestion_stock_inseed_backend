<?php

namespace App\Services;

use App\Models\StockUser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockUserService
{

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = StockUser::query()->with(['stock', 'user']);

        // Filtrage par Stock
        if (!empty($filters['stock_id'])) {
            // Utilisation de double quotes pour injecter la variable ou concaténation
            // Log::info("Filtrage par stock_id : " . $filters['stock_id']);

            $query->where('stock_id', $filters['stock_id']);
        }

        // Filtrage par Utilisateur
        if (!empty($filters['user_id'])) {
            // Log::info("Filtrage par user_id : " . $filters['user_id']);

            $query->where('user_id', $filters['user_id']);
        }
        // OPTIONNEL : Si tu veux FORCER l'affichage de rien du tout si pas de user_id passé
        // else { $query->whereRaw('1 = 0'); }

        $results = $query->paginate(4);

        // Log::info("Nombre d'affectations trouvées : " . $results->total());

        return $results;
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
        $result = $stockUser->delete();

        return $result === true;
    }
}
