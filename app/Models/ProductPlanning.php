<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPlanning extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'nota_dinas_id',
        'requirement_amount',
        'product_amount',
        'procurement_plan_amount',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function nota_dinas(): BelongsTo
    {
        return $this->belongsTo(NotaDinas::class);
    }
}
