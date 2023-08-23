<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
    ];

    public function qualifiers()
    {
        return $this->hasMany(Qualifier::class);
    }
}
