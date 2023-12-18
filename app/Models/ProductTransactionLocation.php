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
        'product_transaction_id',
        'location_id',
        'amount',
    ];

    protected static function boot()
    {
        parent::boot();

        // Listen for creating event
        static::creating(function ($model) {
            $model->updateRelatedAmounts();
        });

        // Listen for deleting event
        static::deleting(function ($model) {
            $model->updateRelatedAmounts();
        });
    }

    public function updateRelatedAmountsOnOutgoingCreate()
    {
        $this->amount -= $this->outgoing_products->sum('amount');
        $this->save();

        $this->product_transaction->updateAmount();
        $this->product_transaction->product->updateAmount();
    }

    public function updateRelatedAmountsOnOutgoingUpdate($amountDifference)
    {
        $this->amount += $amountDifference;
        $this->save();

        $this->product_transaction->updateAmount();
        $this->product_transaction->product->updateAmount();
    }

    public function updateRelatedAmountsOnOutgoingDelete()
    {
        $this->amount += $this->outgoing_products->sum('amount');
        $this->save();

        $this->product_transaction->updateAmount();
        $this->product_transaction->product->updateAmount();
    }

    public function updateRelatedAmounts()
    {
        $this->product_transaction->updateAmount();
        $this->product_transaction->product->updateAmount();
    }

    public function outgoing_products(): HasMany
    {
        return $this->hasMany(OutgoingProduct::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function product_transaction(): BelongsTo
    {
        return $this->belongsTo(ProductTransaction::class);
    }
}
