<?php

namespace App\Models\FLU;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FLU_Homologacion extends Model
{
    use HasFactory;

    protected $table = 'FLU_Homologacion';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = true;

    public $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'CodHomologacion',
        'FlujoID',
        'ValorIdentificador',
        'ValorRespuesta',
        'ValorNombre',
        'Activo'
    ];

    public function flujo()
    {
        return $this->belongsTo(FLU_Flujos::class, 'FlujoID', 'ID');
    }

    public function scopeGetDato($query, $ID, $IDflujo, $codigo, $default='')
    {
        $dato = $query->where('FlujoID', $IDflujo)
            ->where('Activo', 1)
            ->where('ValorIdentificador', $ID)
            ->where('CodHomologacion', $codigo)
            ->where('ValorRespuesta','<>','')
            ->first();

        if ($dato) {
            return $dato->ValorRespuesta;
        } else {
            return $default;
        }
    }

    public function scopeTipoDato($query, $tipo)
    {
        return $query->where('CodHomologacion', $tipo);
    }

    public function scopeNuevo($query, $data)
    {
//        Log::info("Generando nueva homologacion, favor completar");

        return $query->firstOrCreate(
            ['CodHomologacion' => $data['codigo'],
                'FlujoID' => $data['flujo'],
                'ValorIdentificador' => $data['identificador']
            ],[
            'FechaCreacion' => date("Y-m-d"),
            'EventoCreacionID' => 1,
            'UsuarioCreacionID' => 1,
            'CodHomologacion' => $data['codigo'],
            'FlujoID' => $data['flujo'],
            'ValorIdentificador' => $data['identificador'],
            'ValorRespuesta' => $data['respuesta'] ?? '',
            'ValorNombre' => $data['nombre'] ?? '',
            'Activo' => 1
        ]);
    }

}
