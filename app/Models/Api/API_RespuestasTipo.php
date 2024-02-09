<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class API_RespuestasTipo extends Model
{
    use HasFactory;

    protected $table = 'API_RespuestasTipos';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = true;

    protected $fillable = [
        'ApiID',
        'Tipo',
        'Descripcion',
        'llave',
        'Mensaje',
        'Activo',
        'Reprocesa',
    ];

    public function apiproveedor()
    {
        return $this->belongsTo(ApiProveedores::class, 'ApiID', 'ID');
    }

}
