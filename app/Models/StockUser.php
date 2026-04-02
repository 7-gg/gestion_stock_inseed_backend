<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_users';

    protected $fillable = [
        'stock_id',
        'user_id',
        'is_chief',
        'comment',
    ];

    protected $casts = [
        'is_chief' => 'boolean',
    ];


    /**
     * Scope pour récupérer uniquement les assignments actives
     * Une assignment est active si elle n'a pas de date de fin (deleted_at null)
     * ou si sa date de fin est dans le futur
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('deleted_at')
                ->orWhere('deleted_at', '>', now());
        });
    }

    /**
     * Scope pour récupérer les assignments expirées
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', now());
    }

    // Vos relations existantes...

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeChief($query)
    {
        return $query->where('is_chief', true);
    }
}
