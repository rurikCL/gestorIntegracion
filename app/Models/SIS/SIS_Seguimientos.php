<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_Seguimientos extends Model
{

    protected $table = 'SIS_Seguimientos';
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
        'SucursalID',
    ];
}
