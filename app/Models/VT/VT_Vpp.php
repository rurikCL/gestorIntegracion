<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MA\MA_Vehiculos;


class VT_Vpp extends Model
{
    use HasFactory;

    protected $table = 'VT_Vpp';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'UsuarioActualizacionID',
        'EntidadFinancieraTxt',
        'MontoPrenda',
        'estado_solicitud_prepago',
        'FechaVencimiento',
        'PagaPrenda',
        'MarcaOld',
        'ModeloOld',
        'cliente_nombre_prepago',
        'la',
        'fecha_pagare',
        'cliente_rut_prepago',
        'prepago_cargado',
        'TomadorTxt',
        'fecha_comprobante_prepago_cancelado',
        'lb_anterior',
        'cliente_prepago',
        'rut_prepago',
        'saldo_prepago',
        'tipo_pago_prepago',
        'enviado_prepago',
        'estado_prepago',
        'monto_prepago_confirmado',
        'Trazabilidad_EstadoAlzamiento',
        'Trazabilidad_EtapaTransferencia',
        'EtapaTxt',
        'Patente',
        'VentaID',
        'TienePrenda',
        'PrepagoID',
        'VehiculoID',
        'SucursalTomaID',
        'ClienteID',
        'MarcaID',
        'ModeloID',
        'ModeloTxt',
        'PrecioCompra',
        'OrigenID',
        'SubOrigenID',
        'TomadorID',
        'UbicacionRecepcionVPP',
        'Anio',
        'Kilometraje',
        'Comentario',
        'EstadoID',
        'Activo'
    ];

    public function vehiculo()
    {
        return $this->hasOne(MA_Vehiculos::class, 'ID', 'VehiculoID');
    }
}
