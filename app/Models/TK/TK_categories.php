<?php

namespace App\Models\TK;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TK_categories extends Model
{
    use HasFactory;
    protected $table = 'TK_categories';

    protected $primaryKey = 'id';
    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'name',
    ];

    public function sub_categories()
    {
        return $this->hasMany(TK_sub_categories::class, 'category_id', 'id');
    }
}
