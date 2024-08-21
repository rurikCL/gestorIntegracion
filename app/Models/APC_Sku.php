<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APC_Sku extends Model
{
    protected $table = 'APC_Sku';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'Sucursal',
        'Bodega',
        'Ubicacion',
        'Cod_Sku',
        'Sku',
        'Saldo',
        'Cup',
        'Total',
        'Grupo',
        'Sub_Grupo',
        'Marca',
        'Clasificacion',
        'Condicion',
        'Categoria',
        'Fecha_Primera_Compra',
        'Fecha_Ultima_Compra',
        'Fecha_Ultima_Venta',
    ];
}
