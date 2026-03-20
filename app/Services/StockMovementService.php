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
    public function list(array $filters = []): LengthAwarePaginator
    {
        // On part du modèle global pour permettre de voir TOUS les mouvements si besoin
        $query = StockMovement::query()
            ->with(['creator', 'validator', 'stockProduct']) // Ajout de stockProduct pour savoir de quel produit il s'agit
            ->latest('created_at'); // Tri décroissant (DESC)

        // Filtre par Produit (si l'ID est fourni)
        if (!empty($filters['stock_product_id'])) {
            $query->where('stock_product_id', $filters['stock_product_id']);
        }

        // Filtre par Type de mouvement (Entrée/Sortie)
        if (!empty($filters['movement'])) {
            $query->where('movement', $filters['movement']);
        }

        // Filtre par Date (Intervalle)
        if (!empty($filters['start']) && !empty($filters['end'])) {
            $query->betweenDates($filters['start'], $filters['end']);
        }

        return $query->paginate(2);
    }

    public function create(array $data): StockMovement
    {
        $data['created_by'] = Auth::id();

        // 1. Vérification spécifique pour la SORTIE
        if ($data['movement'] === 'SORTIE' || $data['movement']->value === 'SORTIE') {
            if (empty($data['beneficiary_email'])) {
                throw new \InvalidArgumentException("L'email du bénéficiaire est obligatoire pour une sortie.");
            }

            // 2. Recherche automatique du validateur par email
            // On cherche si un utilisateur possède cet email
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
