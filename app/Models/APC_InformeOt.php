<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Table;

class APC_InformeOt extends Model
{
    use HasFactory;

    protected $table = "APC_InformeOt";

    protected $fillable = [
        'Sucursal',
        'FechaIngreso',
        'FechaCierre',
        'Seccion',
        'TipoOt',
        'Folio',
        'Recepcionista',
        'Estado',
        'FechaEntrega',
        'FechaEntregaReal',
        'Marca',
        'Nombre',
        'Version',
        'Anio',
        'Patente',
        'VIN',
        'Dealer',
        'FechaFacturaVehiculo',
        'Color',
        'KilometrajeActual',
        'Cliente',
        'CompaniaSeguro',
        'NumeroSiniestro',
        'TotalServicios',
        'TotalRepuestos',
        'Neto',
        'PendienteFacturacion',
        'Grua8Anios',
        'ReingresoATaller',
        'ClientePrioritario',
        'PruebaDeRuta',
        'ComunicarACliente',
        'Campania',
        'ControlDeCalidad',
        'GeneraPresupuesto',
        'Atributo',
        'Horometro',
        'ObservacionOt',
        'SucursalID',
        'EstadoInterno',
        'MarcaID',
        'AutoDetenido',
        'FechaEstimada',
        'NumeroFactura',
        'FechaFactura',
        'TipoComentario',
        'Anulado',
    ];

    public function scopeUpdateTramo($query)
    {
        return $query->update([
            'group' => DB::raw('FLOOR(DATEDIFF(CURRENT_DATE, FechaIngreso) / 30) + 1')
        ]);
    }
}
