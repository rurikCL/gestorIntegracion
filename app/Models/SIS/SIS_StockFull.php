<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_StockFull extends Model
{
    use HasFactory;

    protected $table = 'SIS_StockFull';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'Empresa',
        'Sucursal',
        'FolioVenta',
        'Venta',
        'EstadoVenta',
        'FechaVenta',
        'TipoDocumento',
        'Vendedor',
        'FechaIngreso',
        'FechaFacturacion',
        'VIN',
        'Marca',
        'Modelo',
        'Version',
        'CodigoVersion',
        'Anno',
        'Kilometraje',
        'CodigoInterno',
        'PlacaPatente',
        'CondicionVehiculo',
        'ColorExterior',
        'ColorInterior',
        'PrecioVenta',
        'EstadoAutoPro',
        'DiasStock',
        'EstadoDealer',
        'Bodega',
        'Equipamiento',
        'NumeroMotor',
        'NumeroChasis',
        'Proveedor',
        'FechaDisponibilidad',
        'FacturaCompra',
        'VencimientoDocumento',
        'FechaCompra',
        'FechaVctoRevisionTecnica',
        'NPropietarios',
        'FolioRetoma',
        'FechaRetoma',
        'DiasReservado',
        'PrecioCompra',
        'Gasto',
        'Accesorios',
        'TotalCosto',
        'PrecioLista',
        'Margen',
        'Z',
        'DisponibleENissan',
        'UnidadEspecial',
        'BonoFinanciamiento',
        'BonoMarca',
        'BonoAdicional',
        'DisponibleUsados',
        'Descuento',
        'MarcaID',
        'ModeloID',
        'VersionID'
    ];
}
