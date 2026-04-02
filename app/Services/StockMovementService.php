<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\StockProduct;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * Service responsible for stock movements and their side-effects on stock_product quantities.
 */
class StockMovementService
{
    public function list(array $filters = [])
    {
        $query = StockMovement::query();

        // 1. Filtrer par le Stock (via la table intermédiaire stock_products)
        $query->whereHas('stockProduct', function ($q) use ($filters) {
            $q->where('stock_id', $filters['stock_id']);

            // 2. Filtrer par un produit spécifique si demandé
            if (!empty($filters['product_id']) && $filters['product_id'] !== 'all') {
                $q->where('product_id', $filters['product_id']);
            }
        });

        // 3. Filtrer par Type (ENTREE/SORTIE)
        $query->when($filters['type'] ?? null, function ($q, $type) {
            return $q->where('movement', $type);
        });

        // 4. Filtrer par Dates
        $query->when($filters['start_date'] ?? null, function ($q, $start) {
            return $q->whereDate('created_at', '>=', $start);
        });

        $query->when($filters['end_date'] ?? null, function ($q, $end) {
            return $q->whereDate('created_at', '<=', $end);
        });

        return $query->with(['stockProduct.product', 'creator', 'validator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function create(array $data): StockMovement
    {
        $data['created_by'] = Auth::id();

        // 1. Vérification spécifique pour la SORTIE
        // Handle both string and enum cases
        $movementValue = $data['movement'] instanceof \App\Enums\StockMovementType
            ? $data['movement']->value
            : $data['movement'];

        if ($movementValue === 'SORTIE') {
            if (empty($data['beneficiary_email'])) {
                throw new \InvalidArgumentException("L'email du bénéficiaire est obligatoire pour une sortie.");
            }

            // 2. Recherche automatique du validateur par email
            $user = User::where('email', $data['beneficiary_email'])->first();

            if ($user) {
                $data['validated_by'] = $user->id;
            }
        }

        // 3. Extraction des médias avant création (pour ne pas polluer le filable)
        $attachments = $data['attachments'] ?? [];
        unset($data['attachments']);

        // 4. Création du mouvement
        $movement = StockMovement::create($data);

        // 5. Enregistrement des fichiers (Spatie Media Library)
        if (!empty($attachments)) {
            foreach ($attachments as $file) {
                $movement->addMedia($file)->toMediaCollection('attachments');
            }
        }

        return $movement;
    }

    public function update(StockMovement $movement, array $data): StockMovement
    {
        $movement->update($data);
        return $movement;
    }

    public function delete(StockMovement $movement): bool
    {
        return $movement->delete();
    }
}
