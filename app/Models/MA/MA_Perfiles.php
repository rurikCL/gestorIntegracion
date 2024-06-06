<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Perfiles extends Model
{
    use HasFactory;
    protected $table = 'MA_Perfiles';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'Perfil',
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
    ];
}
