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
