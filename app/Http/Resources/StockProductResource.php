<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'stock_id'         => $this->stock_id,
            'product_id'       => $this->product_id,
            'provider'         => $this->provider,
            'quantity'         => (int) $this->quantity,
            'minimum_quantity' => (int) $this->minimum_quantity,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,

            // Relation Stock
            'stock' => $this->whenLoaded('stock', function () {
                return [
                    'id'   => $this->stock->id,
                    'name' => $this->stock->name,
                ];
            }),

            // Relation Produit (avec Catégorie et Unité imbriquées)
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id'          => $this->product->id,
                    'name'        => $this->product->name,
                    // On récupère les relations du produit s'il elles sont chargées
                    'category'    => $this->product->category ? [
                        'id'   => $this->product->category->id,
                        'name' => $this->product->category->name,
                    ] : null,
                    'unit'        => $this->product->unit ? [
                        'id'   => $this->product->unit->id,
                        'name' => $this->product->unit->name,
                    ] : null,
                    'characteristics' => $this->product->characteristics, // JSON
                ];
            }),

            // Relation Mouvements (chargés via le limit(10) du contrôleur)
            'movements' => StockMovementResource::collection($this->whenLoaded('movements')),
        ];
    }
}
