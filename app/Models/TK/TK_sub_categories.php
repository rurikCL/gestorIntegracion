<?php

namespace App\Models\TK;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TK_sub_categories extends Model
{
    use HasFactory;
    protected $table = 'TK_sub_categories';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'Area',
        'Agente',
        'Prioridad',
        'SLA',
        'Activo',

        'created_at',
        'updated_at',
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\TK\TK_categories',  'category_id', 'id');
    }
}
