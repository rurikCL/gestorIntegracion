<?php

namespace App\Models\SP;

use BinaryCats\Sku\Concerns\SkuOptions;
use BinaryCats\Sku\HasSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Table;

class SP_oc_categories extends Model
{
    use HasFactory, hasSku;
    protected $table = 'SP_oc_categories';
    protected $fillable = ['name', 'sku', 'FechaCreacion', 'EventoCreacionID', 'UsuarioCreacionID', 'FechaActualizacion', 'EventoActualizacionID', 'UsuarioActualizacionID'];

    public function skuOptions() : SkuOptions
    {
        return SkuOptions::make()
            ->forceUnique(false)
            ->generateOnCreate(true)
            ->refreshOnUpdate(true);
    }
}
