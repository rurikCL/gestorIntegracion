<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Vehiculos extends Model
{
    use HasFactory;

    protected $table = 'MA_Vehiculos';
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
        'MarcaID',
        'ModeloID',
        'VersionID',
        'Anho',
        'Cajon',
        'Patente',
        'Vin',
        'ColorID',
        'OrigenID',
        'SubOrigenID',
        'Prendado',
        'PrecioLista',
        'PrecioCompra',
        'Kilometraje',
        'EstadoID',
        'MenuSecundarioID',
        'ReferenciaID',
        'Ubicacion',
        'TomadorID',
        'Activo',
        'ModeloTxt',
        'VersionTxt',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
