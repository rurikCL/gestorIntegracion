<?php

namespace App\Models\FLU;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FLU_Notificaciones extends Model
{
    use HasFactory;

    protected $table = 'FLU_Notificaciones';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = true;

    protected $fillable = [
        'ID_Flujo',
        'ID_Ref',
        'Notificado'
    ];

    public function flujo()
    {
        return $this->belongsTo(FLU_Flujos::class, 'ID_Flujo', 'ID');
    }


    public function scopeNotificar($query, $IDref, $IDFlujo)
    {
        $notificacion = $query
            ->firstOrNew([
                'ID_Ref' => $IDref,
                'ID_Flujo' => $IDFlujo,
            ], [
                'ID_Ref' => $IDref,
                'ID_Flujo' => $IDFlujo,
                'Notificado' => 1,
            ]);
        $notificacion->Notificado = 1;

        return $notificacion->save();
    }
}
