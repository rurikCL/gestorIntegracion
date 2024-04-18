<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_PompeyoEmpresas extends Model
{
    protected $table = 'MA_PompeyoEmpresas';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Empresa',
        'Rut',
        'Activo',
        'H_TannerID',
        'Direccion',
        'Telefono',
        'NumeroCuenta',
    ];

}
