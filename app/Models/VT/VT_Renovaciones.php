<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_Renovaciones extends Model
{
    protected $table = 'VT_Renovaciones';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    // llena el arreglo con los valores que se pueden editar
    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'UsuarioModificacionID',
        'FechaModificacion',
        'EventoModificacionID',
        'UsuarioModificacionID',
        'VentaID',
        'FechaFactura',
        'FechaParaTrabajar',
        'FechaVencimiento',
        'FechaRenovacion',
        'EstadoID',
        'SubEstadoID',
        'ClienteID',
        'SucursalID',
        'VendedorID',
        'EjecutivoFI',
        'MarcaID',
        'ModeloID',
        'VersionID',
        'Anno',
        'ValorVehiculo',
        'Retoma',
        'Prepago',
        'Pie',
        'ValorCuotaAprox',
        'CantidadCuotas',
        'VFMG',
        'TipoCreditoID',
        'ConcatID',
        'Cotizado',
        'CotizacionID',
        'Agendado',
        'Venta',
        'Vendido',
        'FechaReAsignado',
        'Llamado',
        'Contesta',
        'LogTareas',
        'IntencionRenovar',
        'VendedorAsignadoID',
        'DerivarRenovacion',
    ];
}
