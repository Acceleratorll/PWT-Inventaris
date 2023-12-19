<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotaDinas extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'authorized',
        'from_date',
        'to_date',
        'desc',
    ];

    public function product_plannings(): HasMany
    {
        return $this->hasMany(ProductPlanning::class);
    }
}
