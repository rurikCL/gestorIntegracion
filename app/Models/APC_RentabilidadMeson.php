<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APC_RentabilidadMeson extends Model
{

    protected $table = 'APC_RentabilidadMeson';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        "Sucursal",
        "FechaFacturacion",
        "TipoDocumento",
        "Folio",
        "Vendedor",
        "Cliente",
        "Rut",
        "Digito",
        "CodigoUnicoExtranjero",
        "SKU",
        "NombreSKU",
        "Marca",
        "GrupoSKU",
        "SubGrupoSKU",
        "UnidadMediaSKU",
        "Cantidad",
        "Venta",
        "Costo",
        "Margen",
        "PorcentajeMargen",
    ];
}
