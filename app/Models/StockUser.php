<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUser extends Model
{

    use HasFactory;

    protected $table = 'stock_users';

    protected $fillable = [
        'stock_id',
        'user_id',
        'is_chief',
        'comment',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'is_chief' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('ended_at')->orWhere('ended_at', '>', now());
    }

    public function scopeChief($query)
    {
        return $query->where('is_chief', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('is_chief', false);
    }
}
