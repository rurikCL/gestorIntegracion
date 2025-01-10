<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_Solicitudes extends Model
{
    use HasFactory;

    protected $table = 'SIS_Solicitudes';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'ClienteID',
        'ReferenciaID',
        'ResponsableID',
        'TipoID',
        'EstadoID',
        'PasoActual',
        'Comentario',
        'SucursalID',
        'Respondida',
        'PerfiResponsableActual',
        'CargoResponsableActual'
    ];

    public function datosTransferencias()
    {
        return $this->hasMany(SIS_DatosTransferencias::class, 'SolicitudID', 'ID');
    }
}
