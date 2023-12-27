<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_plan_id',
        'product_id',
        'amount',
        'product_amount',
        'expired',
    ];

    protected $casts = [
        'expired' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($outgoingProduct) {
            $product = $outgoingProduct->product;
            $totalAmount = $product->product_locations()->sum('amount');
            $product->update(['total_amount' => $totalAmount]);
        });
    }

    public function process_plan(): BelongsTo
    {
        return $this->belongsTo(ProcessPlan::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
