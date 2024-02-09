<?php

namespace App\Models\VT;

use App\Models\MA\MA_Marcas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VT_Salvin extends Model
{
    use HasFactory;

    protected $table = "VT_Salvin";
    protected $primaryKey = "ID";
    public $timestamps = false;

    protected $fillable = [
        'Marca',
        'Modelo',
        'Cajon',
        'Cliente',
        'ClienteRut',
        'Telefono',
        'Estado',
        'FechaVenta',
        'FechaFactura',
        'Sucursal',
        'Tipo',
        'Saldo',
        'Vendedor',
        'JefeSucursal',
        'Comentario',
        'Timestamp',
        'FechaEstimado',
        'TipoEstimado',
        'Tramo',
        'SaldosVigentes',
        'FechaActualizacion',
        'FechaFacturaEst',
        'Financiera',
        'TipoVenta'
    ];


    public function comentarios()
    {
        return $this->hasMany(VT_SalvinComentarios::class, 'Cajon', 'Cajon');
    }

    public function marcav()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'Marca');
    }
}
