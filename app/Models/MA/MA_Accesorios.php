<?php

namespace App\Models\MA;

use App\Models\VT\VT_ElementosFinanciadosSubTipos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Accesorios extends Model
{
    protected $table = 'MA_Accesorios';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'UsuarioCreacionID',
        'Marca',
        'Modelo',
        'Familia',
        'TipoTxt',
        'SKU',
        'Descripcion',
        'PrecioCosto',
        'PrecioCostoRoma',
        'PrecioVenta',
        'Activo',
        'SubTipoID',
        'MarcaID',
//        'ModeloID',
    ];

    public function subtipo()
    {
        return $this->hasOne(VT_ElementosFinanciadosSubTipos::class, 'ID', 'SubTipoID');
    }

    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'MarcaID');
    }

    public function gerencia()
    {
        return $this->hasOne(MA_Gerencias::class, 'ID', 'MarcaID');
    }

    public function modelo()
    {
        return $this->hasOne(MA_Modelos::class, 'ID', 'ModeloID');
    }



}
