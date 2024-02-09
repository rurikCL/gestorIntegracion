<?php

namespace App\Models\TDP;

use App\Models\MA\MA_Sucursales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_WebPompeyoSucursales extends Model
{
    use HasFactory;
    protected $table = 'TDP_WebPompeyoSucursales';
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
        'Sucursal',
        'SucursalID',
    ];

    public function sucursal()
    {
        return $this->belongsTo(MA_Sucursales::class, 'SucursalID', 'ID');
    }
}
