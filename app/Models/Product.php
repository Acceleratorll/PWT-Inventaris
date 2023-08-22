<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function product_type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    public function qualifier(): BelongsTo
    {
        return $this->belongsTo(Qualifier::class);
    }
}
