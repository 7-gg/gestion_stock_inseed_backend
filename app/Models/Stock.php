<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Mass assignable attributes.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'created_by',
        'history',
    ];

    // Relations

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'stock_users')
            ->withPivot('is_chief', 'comment')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'stock_products')
            ->withPivot('provider', 'quantity', 'minimum_quantity')
            ->withTimestamps();
    }

    public function stockProducts()
    {
        return $this->hasMany(StockProduct::class);
    }

    public function stockMovements()
    {
        return $this->hasManyThrough(StockMovement::class, StockProduct::class);
    }

    public function currentChief()
    {
        return $this->users()
            ->wherePivot('is_chief', true)
            ->whereNull('stock_users.ended_at');
    }

    public function currentUsers()
    {
        return $this->users()
            ->wherePivot('is_chief', false);
    }
}
