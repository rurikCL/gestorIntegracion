<?php

namespace App\Models\SP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_providers extends Model
{
    use HasFactory;

    protected $table = 'SP_providers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'rut',
        'payment_condition',
        'contact',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'cuenta',
        'costCenter',
        'gasto',
    ];
}
