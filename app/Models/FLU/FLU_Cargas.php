<?php

namespace App\Models\FLU;

use App\Models\MA\MA_Usuarios;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FLU_Cargas extends Model
{
    use HasFactory;

    protected $table = 'FLU_Cargas';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = true;

    protected $casts = [
        'File' => 'array',
    ];

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'ID_Flujo',
        'FechaCarga',
        'Registros',
        'RegistrosCargados',
        'RegistrosFallidos',
        'Estado',
        'File',
        'FilePath',
    ];

    public function flujo()
    {
        return $this->belongsTo(FLU_Flujos::class, 'ID_Flujo', 'ID');
    }

    public function creadoPor()
    {
        return $this->hasOne(User::class, 'id', 'UsuarioCreacionID');
    }
}
