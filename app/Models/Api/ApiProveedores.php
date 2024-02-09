<?php

namespace App\Models\Api;

use App\Models\EntidadesFinancieras;
use App\Models\SisIntegraciones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiProveedores extends Model
{
    use HasFactory;

    protected $table = 'API_Proveedores';
    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ProveedorID',
        'Nombre',
        'Tipo',
        'Url',
        'Header',
        'Metodo',
        'TipoEntrada',
        'Params',
        'Json',
        'TipoRespuesta',
        'Timeout',
        'Token',
        'User',
        'Password',
        'IndiceError',
        'IndiceExito',
        'IndiceRespuesta',
        'IndiceExpiracion',
        'TiempoExpiracion'
    ];

    public function entidadesFinancieras()
    {
        return $this->belongsTo( EntidadesFinancieras::class, 'ProveedorID', 'ID' );
    }

    public function integracion()
    {
        return $this->belongsTo(SisIntegraciones::class, 'ProveedorID', 'ID');
    }

    public function respuestasTipo()
    {
        return $this->hasMany(API_RespuestasTipo::class, 'ApiID', 'id');
    }
}
