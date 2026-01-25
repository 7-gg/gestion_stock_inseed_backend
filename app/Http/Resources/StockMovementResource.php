<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'stock_product_id' => $this->stock_product_id,
            'movement'     => $this->movement,
            'quantity'     => $this->quantity,
            'price'        => $this->price,
            'beneficiary'  => $this->beneficiary,
            'validated_by' => $this->validated_by,
            'validated_at' => $this->validated_at,
            'proofs'       => $this->proofs,
            'comment'      => $this->comment,
            'created_by'   => $this->created_by,
            'created_at'   => $this->created_at,

            'stock_product' => $this->whenLoaded('stockProduct', function () {
                return [
                    'id'   => $this->stockProduct->id,
                    'stock_id' => $this->stockProduct->stock_id,
                    'product_id' => $this->stockProduct->product_id,
                ];
            }),

            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id'    => $this->creator->id,
                    'name'  => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),

            'validator' => $this->whenLoaded('validator', function () {
                return [
                    'id'    => $this->validator->id,
                    'name'  => $this->validator->name,
                    'email' => $this->validator->email,
                ];
            }),
        ];
    }
}
