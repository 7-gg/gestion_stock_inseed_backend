<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class StockMovement extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'stock_movements';

    // created_at exists, updated_at does not
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'stock_product_id',
        'movement',
        'quantity',
        'price',
        'beneficiary',
        'beneficiary_email',
        'validated_by',
        'validated_at',
        'comment',
        'created_by',
    ];

    protected $casts = [
        'movement' => StockMovementType::class,
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('public'); // ou 's3' selon ta config
    }

    // Relations

    public function stockProduct()
    {
        return $this->belongsTo(StockProduct::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopeEntries($query)
    {
        return $query->where('movement', 'ENTREE');
    }

    public function scopeExits($query)
    {
        return $query->where('movement', 'SORTIE');
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeExitsReceived($query)
    {
        return $query->where('movement', 'SORTIE')->whereNotNull('received_at');
    }

    public function scopeExitsPending($query)
    {
        return $query->where('movement', 'SORTIE')->whereNull('received_at');
    }

    public function isValidated(): bool
    {
        return !is_null($this->validated_at);
    }
}
