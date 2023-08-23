<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer',
        'order_type',
        'total',
    ];

    public function outgoing_product(): HasMany
    {
        return $this->hasMany(OutgoingProducts::class);
    }
}
