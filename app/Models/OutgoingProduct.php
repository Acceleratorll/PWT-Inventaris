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
        'qty',
    ];

    public function process_plan(): BelongsTo
    {
        return $this->belongsTo(ProcessPlan::class);
    }

    public function product_transaction_location(): BelongsTo
    {
        return $this->belongsTo(ProductTransactionLocation::class);
    }
}
