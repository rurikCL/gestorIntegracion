<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APC_Stock extends Model
{
    use HasFactory;

    protected $table = 'APC_Stock';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'Empresa',
        'Sucursal',
        'Folio_Venta',
        'Venta',
        'Estado_Venta',
        'Fecha_Venta',
        'Tipo_Documento',
        'Vendedor',
        'Fecha_Ingreso',
        'Fecha_Facturacion',
        'VIN',
        'Marca',
        'Modelo',
        'Version',
        'Codigo_Version',
        'Anio',
        'Kilometraje',
        'Codigo_Interno',
        'Placa_Patente',
        'Condicion_Vehículo',
        'Color_Exterior',
        'Color_Interior',
        'Precio_Venta_Total',
        'Estado_AutoPro',
        'Dias_Stock',
        'Estado_Dealer',
        'Bodega',
        'Equipamiento',
        'Numero_Motor',
        'Numero_Chasis',
        'Proveedor',
        'Fecha_Disponibilidad',
        'Factura_Compra',
        'Vencimiento_Documento',
        'Fecha_Compra',
        'Fecha_Vencto_Rev_tec',
        'N_Propietarios',
        'Folio_Retoma',
        'Fecha_Retoma',
        'Dias_Reservado',
        'Precio_Compra_Neto',
        'Gasto',
        'Accesorios',
        'Total_Costo',
        'Precio_Lista',
        'Margen',
        'Margen_porcentaje'
    ];
}
