<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_transaction_id',
        'product_id',
        'qty',
    ];

    public function product_transaction(): BelongsTo
    {
        return $this->belongsTo(ProductTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
