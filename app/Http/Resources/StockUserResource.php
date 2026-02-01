<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'stock_id'   => $this->stock_id,
            'user_id'    => $this->user_id,
            'is_chief'   => $this->is_chief,
            'comment'    => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'stock' => $this->whenLoaded('stock', function () {
                return [
                    'id'   => $this->stock->id,
                    'name' => $this->stock->name,
                ];
            }),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }
}
