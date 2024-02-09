<?php

namespace App\Models\TDP;

use App\Models\MA\MA_Gerencias;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Sucursales;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_FacebookSucursales extends Model
{
    use HasFactory;
    protected $table = 'TDP_FacebookSucursales';
//    protected $connection = 'mysql';
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
        'SucursalID',
        'MarcaID',
        'GerenciaID',
        'Sucursal',
        ];


    public function sucursal()
    {
        return $this->belongsTo(MA_Sucursales::class, 'SucursalID', 'ID');
    }
    public function marca()
    {
        return $this->belongsTo(MA_Marcas::class, 'MarcaID', 'ID');
    }
    public function gerencia()
    {
        return $this->belongsTo(MA_Gerencias::class, 'GerenciaID', 'ID');
    }

}
