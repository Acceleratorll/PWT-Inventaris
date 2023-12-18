<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransactionLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'transaction_id',
        'location_id',
        'amount',
        'expired',
    ];

    protected $casts = [
        'expired' => 'datetime'
    ];

    public function outgoing_product(): HasOne
    {
        return $this->hasOne(OutgoingProduct::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
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
