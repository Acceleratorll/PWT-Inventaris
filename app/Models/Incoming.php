<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Incoming extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
    ];

    public function incoming_products()
    {
        return $this->hasMany(IncomingProducts::class);
    }
}
