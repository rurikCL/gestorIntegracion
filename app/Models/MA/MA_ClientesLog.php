<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_ClientesLog extends Model
{
    protected  $table = 'MA_ClientesLog';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'ClienteID',
        'UsuarioActualizacionID',
        'CampoModificado',
        'ValorAnterior',
        'ValorNuevo',
        'Mensaje'
    ];

    public function registrarLog($clienteID, $usuarioID, $campoModificado, $valorAnterior, $valorNuevo, $mensaje = '')
    {
        return self::create([
            'ClienteID' => $clienteID,
            'UsuarioActualizacionID' => $usuarioID,
            'CampoModificado' => $campoModificado,
            'ValorAnterior' => $valorAnterior,
            'ValorNuevo' => $valorNuevo,
            'Mensaje' => $mensaje
        ]);
    }
}
