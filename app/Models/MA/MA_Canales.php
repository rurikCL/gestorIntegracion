<?php

namespace App\Models\MA;

use App\Models\MK\MK_Leads;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MA_Canales extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'MA_Canales';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'Canal',
        'Activo',
    ];

    public function lead()
    {
        return $this->belongsTo(MK_Leads::class, 'CanalID', 'ID');
    }
}
