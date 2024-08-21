<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APC_Repuestos extends Model
{
    protected $table = 'APC_Repuestos';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'Sucursal',
        'Recepcionista',
        'Cliente',
        'Fecha_Consumo',
        'Folio_OT',
        'Grupo',
        'Condicion',
        'OT_Estado',
        'OT_Seccion',
        'OT_Tipo',
        'Numero_Vin',
        'Bodega',
        'Ubicacion',
        'Sub_Grupo',
        'Clasificacion',
        'Marca',
        'Version',
        'Placa_Patente',
        'Fecha_Creacion_OT',
        'Tipo_Documento',
        'Folio_Documento',
        'SKU',
        'Nombre',
        'Cantidad',
        'Costo',
        'Sub_Total',
        'Tipo_Cargo',
        'Fecha_Liquidacion',
        'Fecha_Facturacion',
    ];
}
