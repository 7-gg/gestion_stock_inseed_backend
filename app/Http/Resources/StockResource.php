<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'location'   => $this->location,
            'created_by' => $this->created_by,
            'history'    => $this->history,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,

            // Relations
            'creator' => $this->whenLoaded('creator', function () {
                return new UserResource($this->creator);
            }),

            'users' => $this->whenLoaded('users', function () {
                return $this->users->map(function ($user) {
                    return [
                        'id'        => $user->id,
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'pivot' => [
                            'is_chief'   => $user->pivot->is_chief,
                            'comment'    => $user->pivot->comment,
                            'started_at' => $user->pivot->started_at,
                            'ended_at'   => $user->pivot->ended_at,
                        ],
                    ];
                });
            }),

            'products' => $this->whenLoaded('products', function () {
                return $this->products->map(function ($product) {
                    return [
                        'id'   => $product->id,
                        'name' => $product->name,
                        'pivot' => [
                            'provider'         => $product->pivot->provider,
                            'quantity'         => $product->pivot->quantity,
                            'minimum_quantity' => $product->pivot->minimum_quantity,
                        ],
                    ];
                });
            }),

            'stock_products' => $this->whenLoaded('stockProducts', function () {
                return $this->stockProducts->map(function ($sp) {
                    return [
                        'id'        => $sp->id,
                        'product_id' => $sp->product_id,
                        'stock_id'  => $sp->stock_id,
                        'provider'  => $sp->provider,
                        'quantity'  => $sp->quantity,
                        'minimum_quantity' => $sp->minimum_quantity,
                    ];
                });
            }),

            'stock_movements' => $this->whenLoaded('stockMovements', function () {
                return $this->stockMovements->map(function ($movement) {
                    return [
                        'id'              => $movement->id,
                        'stock_product_id' => $movement->stock_product_id,
                        'type'            => $movement->type,
                        'quantity'        => $movement->quantity,
                        'created_at'      => $movement->created_at,
                    ];
                });
            }),

            'current_chief' => $this->whenLoaded('users', function () {
                return $this->currentChief()->first()?->only(['id', 'name', 'email']);
            }),

            'current_users' => $this->whenLoaded('users', function () {
                return $this->currentUsers->map(fn($user) => $user->only(['id', 'name', 'email']));
            }),


        ];
    }
}
