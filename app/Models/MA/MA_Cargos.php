<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MA_Cargos extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'MA_Cargos';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'Cargo',
    ];

    public function cargosPerfil() : hasManyThrough
    {
        return $this->hasManyThrough(MA_Cargos::class, MA_CargosPerfil::class, 'CargoID', 'ID', 'ID', 'ID');
    }
}
