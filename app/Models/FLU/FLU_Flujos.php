<?php

namespace App\Models\FLU;

use App\Models\Api\ApiSolicitudes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FLU_Flujos extends Model
{
    use HasFactory;
    protected $table = 'FLU_Flujos';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = true;

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Tipo',
        'Trigger',
        'Recurrencia',
        'RecurrenciaValor',
        'Activo',
        'MaxLote',
        'Opciones',
        'Reintentos',
        'TiempoEspera',
        'Role',
        'Metodo',
    ];

    public function notificaciones()
    {
        return $this->hasMany(FLU_Notificaciones::class, 'ID_Flujo', 'ID');
    }

    public function solicitudes()
    {
        return $this->hasMany(ApiSolicitudes::class, 'FlujoID', 'ID');
    }

    public function homologaciones()
    {
        return $this->hasMany(FLU_Homologacion::class, 'FlujoID', 'ID');
    }

}
