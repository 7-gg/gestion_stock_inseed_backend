<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_admin' => $this->is_admin,
            'is_manager' => $this->is_manager,
            'login_attempts' => $this->login_attempts,
            'login_code_expires_at' => $this->login_code_expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,

            // Relations

            'stocks' => $this->whenLoaded('stocks', function () {
                return StockResource::collection($this->stocks);
            }),
            'created_stocks' => $this->whenLoaded('createdStocks', function () {
                return StockResource::collection($this->createdStocks);
            }),
            'created_products' => $this->whenLoaded('createdProducts', function () {
                return ProductResource::collection($this->createdProducts);
            }),
            'created_movements' => $this->whenLoaded('createdMovements', function () {
                return StockMovementResource::collection($this->createdMovements);
            }),
            'validated_stock_movements' => $this->whenLoaded('validatedStockMovements', function () {
                return StockMovementResource::collection($this->validatedStockMovements);
            }),
            'stock_assignments' => $this->whenLoaded('stockAssignments', function () {
                return StockUserResource::collection($this->stockAssignments);
            }),
            'current_stock_assignments' => $this->whenLoaded('currentStockAssignments', function () {
                return StockUserResource::collection($this->currentStockAssignments);
            }),
        ];
    }
}
