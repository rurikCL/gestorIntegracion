<?php

namespace App\Models\PV;

use App\Models\FLU\FLU_Notificaciones;
use App\Models\VT_Ventas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PV_PostVenta extends Model
{
    use HasFactory;
    protected $connection = 'mysql-pompeyo';

    protected $table = 'PV_PostVenta';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $dates = [
        'FechaOT',
        'FechaFacturacion'
    ];


    public function venta()
    {
        return $this->belongsTo( VT_Ventas::class, 'Chasis', 'Vin' );
    }

    public function notificacion()
    {
        return $this->hasOne(FLU_Notificaciones::class, 'ID_Ref', 'ID');
    }

    public function scopeOrdenesKia($query)
    {
        return $query->where('Marca', 'LIKE', '%KIA%');
    }

    public function scopeNoNotificado__($query)
    {
        return $query->where('Notificado', false);
    }

    public function scopeNoNotificado($query, $flujo)
    {
        /*return $query->doesntHave('notificacion')
            ->orWhereHas('notificacion', function ($query) use ($flujo) {
                $query->where('ID_Flujo', $flujo)
                    ->where('Notificado',0);
            });*/

        return $query->select($this->table.'.*')
            ->leftJoin('FLU_Notificaciones', function ($join) use($flujo){
                $join->on('FLU_Notificaciones.ID_Ref', '=', $this->table.'.ID')
                    ->where('FLU_Notificaciones.ID_Flujo', '=', $flujo);
            })->where('FLU_Notificaciones.ID', null);
    }

    public function scopeNotificar($query, $ID)
    {
        return $query->where('ID', $ID)
            ->update(['Notificado' => 1]);
    }

    public function scopeVentasDesde($query, $fecha)
    {
        return $query->whereHas('venta', function ($query) use ($fecha) {
            $query->where('FechaVenta', '>=', $fecha);
        })->where('FechaFacturacion', '>=', $fecha);
    }

    public function scopeTipoMantencion($query)
    {
        return $query->whereBetween('TipoMantencion', [71,107]);
    }
}
