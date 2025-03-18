<?php

namespace App\Models\MK;

use App\Models\Api\API_LogSolicitud;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\MA\MA_Canales;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_LeadsEstados;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Origenes;
use App\Models\MA\MA_Regiones;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\MA\MA_Versiones;
use App\Models\VT\VT_Cotizaciones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MK_Campanas extends Model
{
    use HasFactory;

    protected $table = 'MK_Campanas';
    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'Campana',
        'Objetivo',
        'Presupuesto',
        'Costo',
        'EstadoID',
        'TipoID',
        'Activo',
    ];


}
