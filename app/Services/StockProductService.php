<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockProduct;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockProductService
{
    public function list(Stock $stock, array $filters = [], array $relations = []): LengthAwarePaginator
    {
        // 1. On initialise la requête avec les relations reçues du contrôleur
        // 2. On ajoute le tri par date de mise à jour (le plus récent en premier)
        $query = $stock->stockProducts()
            ->with($relations)
            ->orderBy('created_at', 'desc');

        // Filtrage par fournisseur
        if (!empty($filters['provider'])) {
            $query->where('provider', 'like', "%{$filters['provider']}%");
        }

        // Optionnel : Ajout d'un filtre de recherche par nom de produit si 'search' est présent
        if (!empty($filters['search'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate(12);
    }

    public function create(Stock $stock, array $data): StockProduct
    {
        $data['stock_id'] = $stock->id;
        return StockProduct::create($data);
    }

    public function update(StockProduct $stockProduct, array $data): StockProduct
    {
        return DB::transaction(function () use ($stockProduct, $data) {
            $stockProduct->update($data);
            return $stockProduct;
        });
    }
}
