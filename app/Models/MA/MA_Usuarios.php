<?php

namespace App\Models\MA;

use App\Models\OC\OC_Approvers;
use App\Models\OC\OC_Purchaseordergenerator;
use App\Models\RC\RC_cashier_approvers;
use App\Models\SIS\SIS_UsuariosSucursales;
use App\Models\TK\TK_agents;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MA_Usuarios extends Model
{
    use HasFactory;

    protected $table = 'MA_Usuarios';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Foto',
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

        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'SupervisorID',
        'SucursalID',

    ];

    /*protected function FechaCreacion() : Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Carbon::now()->format('Y-m-d H:i:s'),
        );
    }

    protected function UsuarioCreacionID() : Attribute
    {
        return Attribute::make(
            set: fn (int $value) => Auth::user()->id,
        );
    }*/

    public function getRutFormatAttribute()
    {
        return substr($this->Rut, 0, strlen($this->Rut) - 1) . '-' . substr($this->Rut, -1);
    }

    public function sucursal()
    {
        return $this->belongsTo(MA_Sucursales::class, 'SucursalID');
    }
    public function sucursales()
    {
        return $this->hasMany(SIS_UsuariosSucursales::class, 'UsuarioID', 'ID');
    }

    public function agente()
    {
        return $this->hasOne(TK_agents::class, 'user_id', 'ID');
    }
    public function supervisor()
    {
        return $this->hasOne(MA_Usuarios::class, 'SupervisorID', 'ID');
    }

    public function perfil()
    {
        return $this->belongsTo(MA_Perfiles::class, 'PerfilID');
    }

    public function cargo()
    {
        return $this->belongsTo(MA_Cargos::class, 'CargoID');
    }

    public function generadorOrdenCompra()
    {
        return $this->hasOne(OC_Purchaseordergenerator::class, 'user_id', 'ID');
    }

    public function aprobadorOc()
    {
        return $this->hasMany(OC_Approvers::class, 'user_id');
    }
    public function aprobadorCaja()
    {
        return $this->hasMany(RC_cashier_approvers::class, 'user_id');
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





}
