<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APC_RentabilidadOt extends Model
{
    protected $table = 'APC_MovimientoVentas';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'Sucursal',
        'FechaFacturacion',
        'TipoDocumento',
        'TipoTrabajoOT',
        'Folio',
        'FolioOT',
        'FechaOT',
        'OTTipo',
        'OTSeccion',
        'ClienteOT',
        'ClienteRut',
        'ClienteDireccion',
        'ClienteComuna',
        'ClienteCiudad',
        'ClienteTelefonos',
        'ClienteEmail',
        'TipoCargoServicio',
        'VentaMO',
        'CostoMO',
        'MargenMO',
        'MargenMOPorcentaje',
        'TotalInsumos',
        'TotalSeguro',
        'VentaCarroceria',
        'CostoCarroceria',
        'MargenCarroceria',
        'MargenCarroceriaPorcentaje',
        'VentaServicioTerceros',
        'CostoServicioTerceros',
        'MargenServicioTerceros',
        'MargenTercerosPorcentaje',
        'VentaRepuestos',
        'CostoRepuestos',
        'MargenRepuestos',
        'MargenRepuestosPorcentaje',
        'TotalMaterialML',
        'CostoMaterialML',
        'MargenMaterialML',
        'MargenMaterialPje',
        'VentaLubricantes',
        'CostoLubricantes',
        'MargenLubricantes',
        'MargenLubricantesPorcentaje',
        'TotalDeducible',
        'TotalVenta',
        'TotalCosto',
        'TotalMargen',
        'TotalMargenPorcentaje',
        'TotalNetoFacturado',
        'Descuestos',
        'ClienteNombre2',
        'ClienteRut2',
        'ClienteDireccion2',
        'ClienteComuna2',
        'ClienteCiudad2',
        'ClienteTelefonos2',
        'ClienteEmail2',
        'Marca',
        'Modelo',
        'NumeroVIN',
        'Chasis',
        'Patente',
        'Kilometraje',
        'Mecanico',
        'Recepcionista',
        'FolioGarantia',
        'TipoMantención',
    ];

}
