<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Versiones extends Model
{
    use HasFactory;

    protected $table = 'MA_Versiones';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'ModeloID',
        'Version',
        'Activo',
        'H_TannerID',
        'H_KiaID',
        'H_ForumID',
        'ActivoUsados',
        'ActivoNuevo',
    ];
}
