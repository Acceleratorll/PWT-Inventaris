<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'location_id',
        'amount',
        'purchase_date',
        'expired',
    ];

    protected $casts = [
        'expired' => 'date',
        'purchase_date' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($productLocation) {
            $productLocation->product->updateAmount();
        });

        static::updated(function ($productLocation) {
            $productLocation->product->updateAmount();
        });
    }


    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
