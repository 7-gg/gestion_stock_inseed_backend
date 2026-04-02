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
            'comment'      => $this->comment,
            'created_by'   => $this->created_by,
            'created_at'   => $this->created_at,

            'files' => $this->getMedia('attachments')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getFullUrl(),
                    'name' => $media->file_name,
                    'mime' => $media->mime_type
                ];
            }),

            'stock_product' => new StockProductResource($this->whenLoaded('stockProduct')),

            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id'    => $this->creator->id,
                    'name'  => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),

            'validator' => new UserResource($this->whenLoaded('validator')),
        ];
    }
}
