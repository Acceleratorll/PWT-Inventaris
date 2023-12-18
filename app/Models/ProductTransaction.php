<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'transaction_id',
        'amount',
        'product_amount',
        'expired',
    ];

    protected $casts = [
        'expired' => 'datetime'
    ];

    public function updateAmount()
    {
        $this->amount = $this->product_transaction_locations->sum('amount');
        $this->save();
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function product_transaction_locations(): HasMany
    {
        return $this->hasMany(ProductTransactionLocation::class);
    }
}
