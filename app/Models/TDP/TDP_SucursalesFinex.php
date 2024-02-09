<?php

namespace App\Models\TDP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_SucursalesFinex extends Model
{
    use HasFactory;

    protected $table = 'TDP_SucursalesFinex';
//    protected $connection = 'mysql';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'IDSucursal',
        'NombreFinex',
    ];

}
