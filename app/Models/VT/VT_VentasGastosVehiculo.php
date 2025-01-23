<?php

namespace App\Models\VT;

use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Gerencias;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_VentasGastosVehiculo extends Model
{
    use HasFactory;

    protected $table = 'VT_VentasGastosVehiculo';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'Fecha',
        'Gerencia',
        'Combustible',
        'Logistica',
        'PreparacionUsados',
        'Multas',
        'GarantiaUsados',
        'SetYPisos',
        'GastosMiscelaneos',
        'RevisionesPreCompra',
        'Reparacion',
        'Log_Acopio',
        'Log_Traslados',
        'Log_Preparacion',
        'Log_AdmFlorplan',
        'NuevosGastos',
        'MarcaFlota',
    ];

    public function gerenciasoc()
    {
        return $this->hasOne(MA_Gerencias::class, 'ID', 'Gerencia');
    }
}
