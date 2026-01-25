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
            'id' => $this->id,
            'stock_id' => $this->stock_id,
            'product_id' => $this->product_id,
            'provider' => $this->provider,
            'quantity' => $this->quantity,
            'minimum_quantity' => $this->minimum_quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'stock' => $this->whenLoaded('stock', function () {
                return ['id' => $this->stock->id, 'name' => $this->stock->name,];
            }),
            'product' => $this->whenLoaded('product', function () {
                return ['id' => $this->product->id, 'name' => $this->product->name,];
            }),
        ];
    }
}
