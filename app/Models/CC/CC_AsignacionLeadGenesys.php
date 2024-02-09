<?php

namespace App\Models\CC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CC_AsignacionLeadGenesys extends Model
{
    use HasFactory;

    protected $table = 'CC_AsignacionLeadGenesys';
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
        'GerenciaID',
        'SucursalID',
        'VendedorID',
        'Activo'
    ];
}
