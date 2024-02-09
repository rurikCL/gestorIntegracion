<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_SalvinComentarios extends Model
{
    use HasFactory;

    protected $table = "VT_SalvinComentarios";
    protected $primaryKey = "ID";
    public $timestamps = false;

    protected $fillable = [
        'Cajon',
        'Fecha',
        'Usuario',
        'Saldo',
        'Comentario',
        'Tipo',
    ];

    public function salvin()
    {
        return $this->belongsTo(VT_Salvin::class, 'Cajon', 'Cajon');
    }
}
