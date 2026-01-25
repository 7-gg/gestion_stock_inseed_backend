<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'created_by'     => $this->created_by,
            'category_id'    => $this->category_id,
            'unit_id'        => $this->unit_id,
            'characteristics' => $this->characteristics,
            'history'        => $this->history,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'deleted_at'     => $this->deleted_at,

            // Relations
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id'   => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id'   => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),

            'unit' => $this->whenLoaded('unit', function () {
                return [
                    'id'   => $this->unit->id,
                    'name' => $this->unit->name,
                ];
            }),

            'stocks' => $this->whenLoaded('stocks', function () {
                return $this->stocks->map(function ($stock) {
                    return [
                        'id'               => $stock->id,
                        'name'             => $stock->name,
                        'pivot' => [
                            'provider'        => $stock->pivot->provider,
                            'quantity'        => $stock->pivot->quantity,
                            'minimum_quantity' => $stock->pivot->minimum_quantity,
                        ],
                    ];
                });
            }),
        ];
    }
}
