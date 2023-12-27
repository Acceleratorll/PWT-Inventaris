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
        'minimal_amount',
        'total_amount',
        'note',
    ];

    public function updateAmount()
    {
        $this->total_amount = $this->product_locations()->sum('amount');
        $this->save();
    }


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

    public function product_transactions(): HasMany
    {
        return $this->hasMany(ProductTransaction::class);
    }

    public function product_locations(): HasMany
    {
        return $this->hasMany(ProductLocation::class);
    }

    public function product_plannings(): HasMany
    {
        return $this->hasMany(ProductPlanning::class);
    }

    public function outgoing_products(): HasMany
    {
        return $this->hasMany(OutgoingProduct::class);
    }
}
