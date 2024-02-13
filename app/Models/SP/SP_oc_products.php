<?php

namespace App\Models\SP;

use App\Models\MA\MA_Sucursales;
use BinaryCats\Sku\Concerns\SkuOptions;
use BinaryCats\Sku\HasSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_oc_products extends Model
{
    use HasSku;
    protected $table = 'SP_oc_products';
    protected $primaryKey = 'id';
    protected $fillable = [ 'name', 'sku', 'ocSubCategory_id', 'costCenter_id', 'currency_id', 'active', 'measure_id', 'FechaCreacion', 'EventoCreacionID', 'UsuarioCreacionID', 'FechaActualizacion', 'EventoActualizacionID', 'UsuarioActualizacionID' ];


    public function skuOptions() : SkuOptions
    {
        return SkuOptions::make()
            ->forceUnique(false)
            ->generateOnCreate(true)
            ->refreshOnUpdate(true);
    }
    public function subCategory()
    {
        return $this->belongsTo(SP_oc_sub_categories::class, 'ocSubCategory_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(MA_Sucursales::class, 'costCenter_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(SP_measures::class, 'measure_id', 'id');
    }

    public function moneda()
    {
        return $this->belongsTo(SP_currencies::class, 'currency_id', 'id');
    }
}
