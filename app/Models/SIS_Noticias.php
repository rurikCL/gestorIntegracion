<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_Noticias extends Model
{
    protected $table = 'SIS_Noticias';
    protected $primaryKey = 'ID';
    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'Titulo',
        'Ruta',
        'Descripcion',
        'Activo',
        'Texto',
        'Area'
    ];
    public $timestamps = false;
}
