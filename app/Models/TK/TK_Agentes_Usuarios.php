<?php

namespace App\Models\TK;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TK_Agentes_Usuarios extends Model
{
    use HasFactory;
    protected $table = 'TK_Agentes_Usuarios';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'AgenteID',
        'UsuarioID',
        'Activo',
        'EventoCreacionID',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\Models\MA\MA_Usuarios', 'UsuarioID', 'ID');
    }

    public function agente()
    {
        return $this->belongsTo('App\Models\TK\TK_Agentes', 'AgenteID', 'ID');
    }
}
