<?php

namespace App\Models\TDP;

use App\Models\VT\VT_Cotizaciones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_Cotizaciones extends Model
{
    use HasFactory;
    protected $table = 'TDP_Cotizaciones';
//    protected $connection = 'mysql';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'seguimiento',
        'estado',
        'rut',
        'cliente',
        'marca',
        'modelo',
        'fecha_cotizacion',
        'fecha',
        'fecha_cursada',
        'sucursal',
        'vendedor',
        'ejecutivo',
        'tipo_vehiculo',
        'producto',
        'promocion',
        'plazo',
        'seguro',
        'forma_pago',
        'accesorios',
        'cotizados_nissan',
        'solicitados_nissan',
        'aprobados_nissan',
        'cruzados_nissan',
        'ForumTanner',
        'ConcatExterna',
        'ClienteID',
        'Cargado',
        'id_cotizacion_pompeyo',
        'Fecha_Carga',
        'Cargado2',
        'ConcatAnterior',
        ];

    public function vt_cotizaciones()
    {
        return $this->hasOne(VT_Cotizaciones::class,'ConcatID', 'ConcatExterna');
    }


}
