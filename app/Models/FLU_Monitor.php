<?php

namespace App\Models;

use App\Models\FLU\FLU_Flujos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FLU_Monitor extends Model
{
    use HasFactory;
    protected $table = 'FLU_Monitor';
    protected $primaryKey = 'id';

    protected $fillable = [
        'FlujoID',
        'Accion',
        'Estado',
        'Mensaje',
        'FechaInicio',
        'FechaTermino',
        'Duracion',
    ];

    public function flujo(){
        return $this->belongsTo(FLU_Flujos::class, 'FlujoID', 'id');
    }
}
