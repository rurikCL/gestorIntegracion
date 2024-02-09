<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Modelos extends Model
{
    use HasFactory;

    protected $table = 'MA_Modelos';
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
        'Modelo',
        'Activo',
        'H_TannerID',
        'H_KiaID',
        'H_IntouchID',
        'H_Texto',
        'ActivoUsados',
        'ActivoNuevo',
        'RutaFichaTecnica',
    ];

    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'MarcaID');
    }

    public function versiones()
    {
        return $this->hasMany(MA_Versiones::class, 'ModeloID', 'ID');
    }
}
