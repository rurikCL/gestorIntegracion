<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_VentasTramites extends Model
{
    use HasFactory;

    protected $table = 'VT_VentasTramites';
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
        'TipoID',
        'SubTipoID',
        'Valor',
        'Costo',
        'Financiar',
        'CargoTramite',
        'VentaID'
    ];

    public function venta()
    {
        return $this->belongsTo(VT_Ventas::class, 'ID', 'VentaID');
    }

    public function tipo()
    {
        return $this->hasOne(VT_VentasTramitesTipo::class, 'ID', 'TipoID');
    }
}
