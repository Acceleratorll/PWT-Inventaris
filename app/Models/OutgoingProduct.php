<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'process_plan_id',
        'product_transaction_location_id',
        'amount',
        'product_amount',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->updateRelatedAmountsOnCreate();
        });

        static::updating(function ($model) {
            $model->updateRelatedAmountsOnUpdate();
        });

        static::deleting(function ($model) {
            $model->updateRelatedAmountsOnDelete();
        });
    }

    public function updateRelatedAmountsOnCreate()
    {
        $this->product_transaction_location->updateRelatedAmountsOnOutgoingCreate();
    }

    public function updateRelatedAmountsOnUpdate()
    {
        $oldAmount = $this->getOriginal('amount');
        $newAmount = $this->getAttribute('amount');

        $amountDifference = $newAmount - $oldAmount;

        $this->product_transaction_location->updateRelatedAmountsOnOutgoingUpdate($amountDifference);
    }

    public function updateRelatedAmountsOnDelete()
    {
        $this->product_transaction_location->updateRelatedAmountsOnOutgoingDelete();
    }

    public function process_plan(): BelongsTo
    {
        return $this->belongsTo(ProcessPlan::class);
    }

    public function product_transaction_location(): BelongsTo
    {
        return $this->belongsTo(ProductTransactionLocation::class);
    }
}
