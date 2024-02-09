<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_AutoRedTransaccion extends Model
{
    use HasFactory;

    protected $table = 'SIS_AutoRedTransaccion';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'FechaCreacion',
        'UltimaActualización',
        'Patente',
        'Toma',
        'Marca',
        'Modelo',
        'Ano',
        'Km',
        'Version',
        'Color',
        'Sucursal',
        'Vendedor',
        'EmailVendedor',
        'CodigoVendedor',
        'Creador',
        'Estado',
        'MotivoRechazo',
        'DetalleRechazo',
        'PrecioOferta',
        'AutorPrecio',
        'AtendidaPor',
        'PrecioSugerido',
        'PrecioPublicacion',
        'PrecioVenta',
        'NombreCliente',
        'RutCliente',
        'EmailCliente',
        'TelefonoCliente',
        'CelularCliente',
        'TelefonoOficinaCliente',
        'MarcaCliente',
        'ModeloCliente',
        'FinanciamientoCliente',
        'ComentarioCliente',
        'IDtransaccion',
        'Origen',
        'TipoCompra',
        'Procedencia',
        'VehiculoRecibido',
        'FechaRecepcion',
        'Inspeccion',
        'FechaInspeccion',
        'IDAutoRed',
        'SucursalID',
        'VendedorID'
    ];
}
