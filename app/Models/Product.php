<?php

namespace App\Models;

use App\Events\ProductQuantityLow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'material_id',
        'product_type_id',
        'qualifier_id',
        'category_product_id',
        'product_code',
        'name',
        'max_amount',
        'amount',
        'note',
    ];

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

    public function category_product(): BelongsTo
    {
        return $this->belongsTo(CategoryProduct::class);
    }

    public function outgoing_products(): HasMany
    {
        return $this->hasMany(OutgoingProducts::class);
    }

    public function incoming_products(): HasMany
    {
        return $this->hasMany(IncomingProducts::class);
    }
}
