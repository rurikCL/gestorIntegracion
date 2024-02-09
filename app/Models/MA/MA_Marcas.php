<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Marcas extends Model
{
    use HasFactory;

    protected $table = 'MA_Marcas';
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
        'Marca',
        'Activo',
        'H_TannerID',
        'H_IntouchID',
        'H_Texto',
    ];

    public function modelos()
    {
        return $this->hasMany(MA_Modelos::class, 'MarcaID', 'ID');
    }

    public function salvin()
    {
        return $this->belongsTo(VT_Salvin::class, 'Marca', 'ID');
    }
}
