<?php

namespace App\Models\SIS;

use App\Models\MA\MA_Cargos;
use App\Models\MA\MA_Gerencias;
use App\Models\MA\MA_Perfiles;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_UnidadNegocios;
use App\Models\MA\MA_Usuarios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_SolicitudesArbolAprobaciones extends Model
{
    use HasFactory;

    protected $table = 'SIS_SolicitudesArbolAprobaciones';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'UnidadNegocioID',
        'GerenciaID',
        'SucursalDerivadaID',
        'PerfilCreadorID',
        'CargoCreadorID',
        'NivelCreador',
        'PerfilResponsableID',
        'CargoResponsableID',
        'NivelResponsable',
        'UsuarioResponsableID',
        'PasoFinal',
    ];

    public function unidadNegocios()
    {
        return $this->belongsTo(MA_UnidadNegocios::class, 'UnidadNegocioID', 'ID');
    }

    public function gerencia()
    {
        return $this->belongsTo(MA_Gerencias::class, 'GerenciaID', 'ID');
    }

    public function sucursalDerivada()
    {
        return $this->belongsTo(MA_Sucursales::class, 'SucursalDerivadaID', 'ID');
    }

    public function perfilCreador()
    {
        return $this->belongsTo(MA_Perfiles::class, 'PerfilCreadorID', 'ID');
    }

    public function cargoCreador()
    {
        return $this->belongsTo(MA_Cargos::class, 'CargoCreadorID', 'ID');
    }

    public function perfilResponsable()
    {
        return $this->belongsTo(MA_Perfiles::class, 'PerfilResponsableID', 'ID');
    }

    public function cargoResponsable()
    {
        return $this->belongsTo(MA_Cargos::class, 'CargoResponsableID', 'ID');
    }

    public function usuarioResponsable()
    {
        return $this->belongsTo(MA_Usuarios::class, 'UsuarioResponsableID', 'ID');
    }


}
