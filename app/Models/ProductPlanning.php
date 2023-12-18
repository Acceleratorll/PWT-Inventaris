<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPlanning extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'nota_dinas_id',
        'requirement_amount',
        'product_amount',
        'procurement_plan_amount_amount',
    ];
}
