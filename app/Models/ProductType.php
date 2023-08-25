<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'desc',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
