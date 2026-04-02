<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockUser;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class StockService
{
    public function create(array $data): Stock
    {
        // 1. Création du stock
        $stock = Stock::create(Arr::only($data, ['name', 'description', 'location', 'created_by']));

        // 2. Récupération de l'utilisateur (on évite une requête inutile si on a déjà l'objet)
        $user = User::findOrFail($data['created_by']);

        // 3. Si c'est un manager, on le lie au stock comme "chef"
        if ($user->is_manager) {
            StockUser::create([
                'stock_id' => $stock->id,
                'user_id'  => $user->id,
                'is_chief' => true,
                'comment'  => 'Créateur du stock',
            ]);
        }

        return $stock;
    }

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Stock::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query
            // Ajoute les colonnes de comptage automatiques
            ->withCount([
                'users',
                'products',
                'stockMovements as movements_count' // On renomme pour la clarté
            ])
            ->with(['users', 'products']) // Gardez vos eager loads si nécessaire
            ->orderBy('updated_at', 'desc')
            ->paginate(12);
    }

    public function find(int $id): ?Stock
    {
        // On définit les comptes AVANT de récupérer l'enregistrement
        return Stock::withCount([
            'stockUsers as users_count', // Utilise les mêmes noms que dans ton Resource
            'stockProducts as products_count',
            'stockMovements as movements_count'
        ])
            // ->with(['users', 'products', 'creator']) // Charge les relations nécessaires
            ->find($id);
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->update(Arr::only($data, ['name', 'description', 'location']));

        return $stock;
    }

    public function delete(Stock $stock): void
    {
        $stock->delete();
    }
}
