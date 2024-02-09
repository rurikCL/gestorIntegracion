<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_SubOrigenes extends Model
{
    use HasFactory;

    protected $table = 'MA_SubOrigenes';
    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'OrigenID',
        'SubOrigen',
        'ActivoInput',
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
    ];

}
