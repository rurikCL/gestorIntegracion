<?php

namespace App\Models\TK;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TK_Tickets extends Model
{
    use HasFactory;

    protected $table = 'TK_tickets';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'priority',
        'category',
        'subCategory',
        'management',
        'zone',
        'department',
        'applicant',
        'assigned',
        'detail',
        'state',
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
    ];

    public function categoria()
    {
        return $this->belongsTo(TK_categories::class, 'category', 'id');
    }
    public function subCategoria()
    {
        return $this->belongsTo(TK_sub_categories::class, 'subCategory', 'id');
    }



}
