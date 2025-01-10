<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_ClientesDiarios extends Model
{
    protected $table = 'VT_ClientesDiarios';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    // llena el arreglo con los valores que se pueden editar
    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'UsuarioModificacionID',
        'FechaModificacion',
        'EventoModificacionID',
        'UsuarioModificacionID',
        'ClienteID',
        'UltimoComentario',
        'MenuSecundarioID',
        'TipoID',
        'EstadoID',
        'Comentario'
    ];
}
