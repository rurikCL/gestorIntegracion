<?php

namespace App\Models\TK;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TK_agents extends Model
{
    use HasFactory;

    protected $table = 'TK_agents';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'subCategory_id',
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\Models\MA\MA_Usuarios',  'user_id', 'ID');
    }

    public function subCategory()
    {
        return $this->belongsTo('App\Models\TK\TK_sub_categories',  'subCategory_id', 'id');
    }
}
