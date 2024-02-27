<?php

namespace App\Models\MA;

use App\Models\SIS\SIS_UsuariosSucursales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Usuarios extends Model
{
    use HasFactory;

    protected $table = 'MA_Usuarios';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Email',
        'Clave',
        'Rut',
        'TelefonoOficina',
        'Celular',
        'PerfilID',
        'CargoID',
        'Disponible',
        'Activo',
        'DetalleID',
        'H_IntouchID',
        'H_TannerID',
        'H_ForumID',
        'H_OlxID',
        'NombreAutofact',
        'NombreFinex',
        'NombreBci',
        'NombreAutopro',
        'FinancieraIDAsociada',
        'ClaveAnt',
        'TemporadaVacaciones',
        'ResponsableIDOC',
        'ResponsableIDTKTI',
        'ResponsableIDTKCC',
        'ResponsableIDRRHH',
    ];

    public function getRutFormatAttribute()
    {
        return substr($this->Rut, 0, strlen($this->Rut) - 1) . '-' . substr($this->Rut, -1);
    }

    public function sucursales()
    {
        return $this->hasMany(SIS_UsuariosSucursales::class, 'UsuarioID', 'ID');
    }

    public function scopeSucursalAsignada($query, $sucursal_id)
    {
        return $query->whereHas('sucursales', function ($query) use ($sucursal_id) {
            $query->where('SucursalID', $sucursal_id);
        });
    }

    public function scopeJefeSucursal($query)
    {
        return $query->where('CargoID', 2)->where('PerfilID', 3);
    }

    public function perfil()
    {
        return $this->belongsTo(MA_Perfiles::class, 'PerfilID');
    }

    public function cargo()
    {
        return $this->belongsTo(MA_Cargos::class, 'CargoID');
    }

}
