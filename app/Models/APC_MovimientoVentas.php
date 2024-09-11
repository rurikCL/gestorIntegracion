<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APC_MovimientoVentas extends Model
{
    protected $table = 'APC_MovimientoVentas';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'Detalle',
        'Sucursal',
        'TipoDocumento',
        'Folio',
        'FechaDocumento',
        'Estado',
        'Vendedor',
        'Cliente',
        'Sku',
        'Nombre',
        'Grupo',
        'Bodega',
        'Ubicacion',
        'UnidadMedida',
        'Cantidad',
        'PrecioUnitario',
        'Valor',
        'SubTotal',
        'TipoTransaccion',
        'SubGrupo',
        'Venta',
        'UsuarioCreacion',
        'FechaCreacion',
        'FolioOt',
        'DescripcionOt',
        'TipoCargo',
        'FechaEmision',
        'RutCliente',
        'SeccionOt',
        'NroNotaVenta',
        'FolioFactura',
        'FechaFacturacion',
    ];
}
