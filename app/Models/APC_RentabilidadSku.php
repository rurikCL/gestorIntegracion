<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APC_RentabilidadSku extends Model
{

    protected $table = 'APC_RentabilidadSku';
    protected $primaryKey = 'id';

    protected $fillable = [
        'Sucursal',
        'TipoDocumento',
        'Folio',
        'FechaFacturacion',
        'FolioOt',
        'Servicio',
        'SKU',
        'Nombre',
        'Grupo',
        'SubGrupo',
        'Marca',
        'Medida',
        'Cantidad',
        'Mecanico',
        'Venta',
        'Costo',
        'Margen',
        'Porcentaje',
        'Recepcionista',
    ];
}
