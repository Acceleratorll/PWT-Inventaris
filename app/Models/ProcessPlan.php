<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_type_id',
        'code',
        'desc',
        'status',
        'outed_date',
    ];

    protected $casts = [
        'outed_date' => 'datetime'
    ];

    public function outgoing_products(): HasMany
    {
        return $this->hasMany(OutgoingProduct::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order_type(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }
}
