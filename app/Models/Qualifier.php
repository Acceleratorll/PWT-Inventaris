<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Qualifier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unit_group_id',
        'name',
        'abbreviation',
        'conversion_factor',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function unit_group(): BelongsTo
    {
        return $this->belongsTo(UnitGroup::class);
    }
}
