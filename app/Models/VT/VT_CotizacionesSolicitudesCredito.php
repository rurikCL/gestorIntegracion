<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_CotizacionesSolicitudesCredito extends Model
{
    use HasFactory;

    protected $table = 'VT_CotizacionesSolicitudesCredito';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Financiera',
        'Estado',
        'FechaCotizacion',
        'Sucursal',
        'Vendedor',
        'NombreEjecutivo',
        'RutCliente',
        'NombreCliente',
        'EmailCliente',
        'TelefonoCliente',
        'Marca',
        'Modelo',
        'Producto',
        'TipoCredito',
        'NuevoUsado',
        'TipoCreditoID',
        'SucursalID',
        'EstadoID',
        'VendedorID',
        'EjecutivoID',
        'ClienteID',
        'MarcaID',
        'ModeloID',
        'Concat',
        'Cargado',
        'VendedorEnSucursal',
        'CanalID',
        'OrigenID',
        'SubOrigenID',
    ];
}
