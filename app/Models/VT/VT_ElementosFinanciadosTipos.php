<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_ElementosFinanciadosTipos extends Model
{
    protected $table = 'VT_ElementosFinanciadosTipos';
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
        'Tipo',
    ];}
