<?php

namespace App\Models\MA;

use App\Models\RC\RC_cashier_approvers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Sucursales extends Model
{
    use HasFactory;

    protected $table = 'MA_Sucursales';
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
        'TipoSucursalID',
        'EmpresaID',
        'Sucursal',
        'Georeferencia',
        'Direccion',
        'Activa',
        'H_TannerID',
        'H_KiaID',
        'H_ForumID',
        'SucursalApc',
        'CanalSucursal',
        'H_Texto',
        'Visible',
        'VisibleOC',
        'VisibleCC',
        'H_IntouchID',
        'ComunaID',
        'RegionID'
    ];

    public function gerencia()
    {
        return $this->belongsTo(MA_Gerencias::class, 'GerenciaID','ID');
    }

    public function aprobadores()
    {
        return $this->hasMany(RC_cashier_approvers::class, 'branch_office_id','ID');
    }

    public function tipoSucursal()
    {
        return $this->belongsTo(MA_TipoSucursal::class, 'TipoSucursalID','ID');
    }

}
