<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_AutoRedInspections extends Model
{
    use HasFactory;

    protected $table = 'SIS_AutoRedInspections';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'FechaSolicitud',
        'FechaFirma',
        'Patente',
        'Marca',
        'Modelo',
        'Anno',
        'Kilometraje',
        'Version',
        'Color',
        'SucursalInspeccion',
        'Inspector',
        'CostoTotal',
        'CostoTecnico',
        'CostoAccesorios',
        'KmInspeccion',
        'PorcentajeCompletado',
        'Sucursal',
        'Vendedor',
        'EmailVendedor',
        'ArchivoInspeccion',
        'IDTransaccion',
        'IDInspeccion',
    ];
}
