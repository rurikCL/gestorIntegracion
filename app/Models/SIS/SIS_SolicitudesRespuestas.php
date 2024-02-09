<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_SolicitudesRespuestas extends Model
{
    use HasFactory;
    protected $table = 'SIS_Solicitudes';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'SolicitudID',
        'AccionID',
        'Paso',
        'Comentario'
    ];
}
