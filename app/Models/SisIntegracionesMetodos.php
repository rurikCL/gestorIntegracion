<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisIntegracionesMetodos extends Model
{
    use HasFactory;
    protected $table = 'SIS_IntegracionesMetodos';
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
        'IntegracionID',
        'Metodo',
        'Url',
        'IDExterno',
        'Token',
        'Descripcion',
    ];

    public function integracion()
    {
        return $this->belongsTo( SisIntegraciones::class, 'IntegracionID', 'ID' );
    }
}
