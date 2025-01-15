<?php

namespace App\Models\VT;

use App\Models\MA\MA_Clientes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_VentasComentarios extends Model
{
    use HasFactory;

    protected $table = 'VT_VentasComentarios';
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
        'ClienteID',
        'VentaID',
        'Comentario'
    ];

    public function venta()
    {
        return $this->belongsTo(VT_Ventas::class, 'ID', 'VentaID');
    }

    public function cliente(){
        return $this->belongsTo(MA_Clientes::class, 'ID', 'ClienteID');
    }
}
