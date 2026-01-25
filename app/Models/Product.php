<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'category_id',
        'unit_id',
        'name',
        'characteristics',
        'history',
    ];

    protected $casts = [
        'characteristics' => 'array',
        'history' => 'array',
    ];

    // Relations

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'stock_products')
            ->withPivot('provider', 'quantity', 'minimum_quantity')
            ->withTimestamps();
    }

    public function stockProducts()
    {
        return $this->hasMany(StockProduct::class);
    }
}
