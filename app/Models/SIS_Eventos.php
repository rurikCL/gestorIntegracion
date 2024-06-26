<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_Eventos extends Model
{
    use HasFactory;
    protected $table = 'SIS_Eventos';
    protected $primaryKey = 'ID';
    protected $fillable = ['FechaCreacion', 'EventoCreacionID', 'UsuarioCreacionID', 'ReferenciaID', 'MenuSecundarioID', 'Comentario' ];
}
