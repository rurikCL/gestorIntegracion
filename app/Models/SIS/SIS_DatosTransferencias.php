<?php

namespace App\Models\SIS;

use App\Models\MA\MA_Bancos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_DatosTransferencias extends Model
{
    use HasFactory;

    protected $table = 'SIS_DatosTransferencias';
    protected $primaryKey = 'ID';

    // llena el arreglo con los valores que se pueden editar
    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'FechaDeposito',
        'NumeroCentralizacion',
        'FormaPago',
        'NumeroDeposito',
        'LocalDeposito',
        'Monto',
        'BancoID',
        'TipoID',
        'SubTipoID',
        'SolicitudID',
        'MotivoID',
        'comentarios',
        'AsociarCredito',
        'TipoDevolucion',
        'RutaArchivo',
        'ArchivoID'
    ];

    public function banco()
    {
        return $this->hasOne(MA_Bancos::class, 'ID', 'BancoID');
    }
}
