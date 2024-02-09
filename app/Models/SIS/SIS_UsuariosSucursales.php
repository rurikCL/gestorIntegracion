<?php

namespace App\Models\SIS;

use App\Models\MA\MA_Usuarios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_UsuariosSucursales extends Model
{
    use HasFactory;

    protected $table = 'SIS_UsuariosSucursales';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'SucursalID',
        'UsuarioID',
        'Activo',
        'DisponibleLead',
        'CargoID',
        'fechaAsignacion'
    ];

    public function usuario()
    {
        return $this->hasOne(MA_Usuarios::class, 'ID', 'UsuarioID');
    }
}
