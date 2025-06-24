<?php

namespace App\Models\Repuestos;

use App\Models\Rental\RentalArchivos;
use App\Models\Rental\RentalFlujos;
use App\Models\Sucursales;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerfilRepuestos extends Model
{
    use SoftDeletes;

    protected $table = "RP_Perfiles";
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = "ID";
    protected $fillable = [
        'ID',
        'UsuarioID',
        'SucursalID',
        'Perfil',
        'Activo',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    public function Usuario()
    {
        return $this->hasOne(User::class, 'ID', 'UsuarioID');
    }

    public function Sucursal()
    {
        return $this->hasOne(Sucursales::class, 'ID', 'SucursalID');
    }
}
