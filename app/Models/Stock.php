<?php

namespace App\Models;

use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Versiones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $connection = 'mysql-pompeyo';
//    protected $connection = 'mysql';

    protected $table = 'SIS_StockFull';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
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
        'VersionID',
    ];


    public function version()
    {
        return $this->hasOne(MA_Versiones::class, 'ID', 'VersionID');
    }

    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'MarcaID');
    }

    public function modelo()
    {
        return $this->hasOne(MA_Modelos::class, 'ID', 'ModeloID');
    }

}
