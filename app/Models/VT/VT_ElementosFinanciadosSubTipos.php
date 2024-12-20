<?php

namespace App\Models\VT;

use App\Models\MA\MA_Accesorios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_ElementosFinanciadosSubTipos extends Model
{
    protected $table = 'VT_ElementosFinanciadosSubTipos';
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
        'SubTipo',
        'H_ForumID',
        'Activo',
        'USADOS',
        'KIA',
        'CITROEN',
        'DFSK',
        'GEELY',
        'MG',
        'NISSAN',
        'OPEL',
        'PEUGEOT',
        'SUBARU',
        'LANDKING',
        'TieneInstalacionAcc',
        'TiempoInstalacion',
        'ConsiderarReporte',
    ];

    public function tipo(){
        return $this->belongsTo(VT_ElementosFinanciadosTipos::class, 'TipoID', 'ID');
    }

    public function accesorios()
    {
        return $this->hasMany(MA_Accesorios::class, 'SubTipoID', 'ID');
    }

}
