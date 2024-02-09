<?php

namespace App\Models\Api;

use App\Models\EntidadesFinancieras;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\SisIntegraciones;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSolicitudes extends Model
{
    use HasFactory;

    protected $table = 'API_Solicitudes';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ReferenciaID',
        'ProveedorID',
        'ApiID',
        'Prioridad',
        'Peticion',
        'CodigoRespuesta',
        'Respuesta',
        'FechaPeticion',
        'FechaResolucion',
        'Exito',
        'FlujoID',
        'Reprocesa',
        'Reintentos',
    ];

    /*protected $dates = [
        'FechaPeticion',
        'FechaResolucion',
    ];*/

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
        'deleted_at' => 'datetime:Y-m-d h:i:s',
//        'FechaPeticion' => 'datetime:d/m/Y h:i:s',
//        'FechaResolucion' => 'datetime:d/m/Y h:i:s',
    ];

    public function proveedores()
    {
        return $this->belongsTo( ApiProveedores::class, 'ApiID', 'id');
    }

    public function entidadesFinancieras()
    {
        return $this->belongsTo( EntidadesFinancieras::class, 'ProveedorID', 'ID' );
    }

    public function integracion()
    {
        return $this->belongsTo( SisIntegraciones::class, 'ProveedorID', 'ID' );
    }

    public function flujo()
    {
        return $this->belongsTo(FLU_Flujos::class, 'FlujoID', 'ID');
    }

    public function notificacion()
    {
        return $this->hasOne(FLU_Notificaciones::class, 'ID_Ref', 'ReferenciaID');
    }

    public function logs()
    {
        return $this->hasMany(API_LogSolicitud::class, 'SolicitudID', 'id');
    }

    public function scopeSolicitudesPendientes($query)
    {
        return $query->where('CodigoRespuesta', '>', 202)
            ->count();
    }
    public function scopeSolicitudesListas($query)
    {
        return $query->where('CodigoRespuesta', 200)
            ->orWhere('CodigoRespuesta', 202)
            ->count();
    }

    public function scopeSolicitudesExitosas($query)
    {
        return $query->where('Exito', 1)
            ->count();
    }    public function scopeSolicitudesFallidas($query)
    {
        return $query->where('Exito', 0)
            ->count();
    }
      public function scopeUltimaResolucion($query)
    {
         $data = $query->orderBy('FechaResolucion', 'DESC')->first();

         if($data && $data->FechaResolucion != null){
             return Carbon::create($data->FechaResolucion)->format('d/m/Y h:i:s');
         } else {
             return "No hay resolucion";
         }
    }

    public function scopeNotificado($query, $flujo)
    {
        return $query->whereHas('notificacion', function ($query) use ($flujo) {
            $query->where('ID_Flujo', $flujo);
        });
    }


}
