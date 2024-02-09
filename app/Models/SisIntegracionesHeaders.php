<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisIntegracionesHeaders extends Model
{
    use HasFactory;
    protected $table = 'SIS_IntegracionesMetodosHeader';
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
        'MetodoID',
        'Header'
    ];

    public function integracion()
    {
        return $this->belongsTo( SisIntegraciones::class, 'IntegracionID', 'ID' );
    }
    public function metodo()
    {
        return $this->belongsTo( SisIntegracionesMetodos::class, 'MetodoID', 'ID' );
    }
}
