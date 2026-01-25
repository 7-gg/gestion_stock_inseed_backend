<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends BusinessException
{
    public static function forProduct(int $productId, int $stockId, int $available, int $requested): self
    {
        return new self("Insufficient stock for product {$productId} in stock {$stockId}: available={$available}, requested={$requested}");
    }
}
