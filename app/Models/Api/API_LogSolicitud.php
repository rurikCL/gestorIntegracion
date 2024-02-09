<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class API_LogSolicitud extends Model
{
    use HasFactory;

    protected $table = 'API_LogSolicitudes';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = true;

    protected $fillable = [
        'SolicitudID',
        'Mensaje',
        'UsuarioID',
        'Tipo',
        'status',
    ];

    public function solicitud()
    {
        return $this->belongsTo(API_Solicitudes::class, 'ID', 'SolicitudID');
    }
}
