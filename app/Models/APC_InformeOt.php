<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

}
