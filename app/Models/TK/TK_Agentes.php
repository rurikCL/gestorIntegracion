<?php

namespace App\Models\TK;

use App\Models\MA\MA_Usuarios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TK_Agentes extends Model
{
    use HasFactory;
    protected $table = 'TK_Agentes';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Activo',
        'EventoCreacionID',
    ];

    public function usuarios()
    {
        return $this->hasManyThrough(MA_Usuarios::class, TK_Agentes_Usuarios::class, 'AgenteID', 'ID', 'ID', 'usuarioID');
    }

    public function usuarioAgente()
    {
        return $this->hasMany(TK_Agentes_Usuarios::class, 'AgenteID', 'ID');
    }

}
