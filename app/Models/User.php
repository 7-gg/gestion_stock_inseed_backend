<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use InteractsWithMedia, HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_admin',
        'is_manager',
        'password',
        'login_code_expires_at',
        'login_attempts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'is_manager' => 'boolean',
        'login_code_expires_at' => 'datetime',
        'login_attempts' => 'integer',
    ];

    // Définir une collection spécifique pour l'avatar
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->singleFile(); // Supprime automatiquement l'ancien fichier si un nouveau arrive
    }

    /**
     * Relations
     */

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'stock_users')
            ->withPivot('comment')
            ->withTimestamps();
    }

    public function hasStockAccess($stockId): bool
    {
        return $this->stocks()
            ->where('stocks.id', $stockId)
            ->exists();
    }
    /**
     * Stocks created by the user (created_by FK on stocks table)
     */
    public function createdStocks()
    {
        return $this->hasMany(Stock::class, 'created_by');
    }

    /**
     * Products created by the user (created_by FK on products table)
     */
    public function createdProducts()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function createdMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }


    public function validatedStockMovements()
    {
        return $this->hasMany(StockMovement::class, 'validated_by');
    }


    public function stockAssignments()
    {
        return $this->hasMany(StockUser::class);
    }

    public function currentStockAssignments()
    {
        return $this->stockAssignments();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isManager(): bool
    {
        return $this->is_manager;
    }

    public function hasRole(string $role): bool
    {
        if ($role === 'admin') return $this->is_admin;
        if ($role === 'manager') return $this->is_manager;
        return false;
    }

    /**
     * Récupère tous les mouvements où l'utilisateur est le bénéficiaire (via email)
     */
    public function assignedMovements()
    {
        return $this->hasMany(StockMovement::class, 'beneficiary_email', 'email');
    }

    public function assignedMovementsValidated()
    {
        return $this->hasMany(StockMovement::class, 'beneficiary_email', 'email')->wherenotnull('validated_at');
    }
}
