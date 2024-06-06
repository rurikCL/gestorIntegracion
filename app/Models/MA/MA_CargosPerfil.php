<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_CargosPerfil extends Model
{
    use HasFactory;

    protected $table = 'MA_CargosPerfil';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'CargoRex',
        'CargoHomologado',
        'AreaNegocio',
        'PerfilID',
        'CargoID',
        'HerramientaInformatica',
        'NivelAprobacion',
        'Orden',
    ];

    public function cargo()
    {
        return $this->belongsTo(MA_Cargos::class, 'CargoID', 'ID');
    }

    public function perfil()
    {
        return $this->belongsTo(MA_Perfiles::class, 'PerfilID', 'ID');
    }
}
