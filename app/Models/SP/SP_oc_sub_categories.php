<?php

namespace App\Models\SP;

use BinaryCats\Sku\HasSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_oc_sub_categories extends Model
{
    use HasFactory, HasSku;
    protected $table = 'SP_oc_sub_categories';
    protected $fillable = [ 'name', 'sku', 'ocCategory_id', 'FechaCreacion', 'EventoCreacionID', 'UsuarioCreacionID', 'FechaActualizacion', 'EventoActualizacionID', 'UsuarioActualizacionID' ];

    public function oc_category()
    {
        return $this->belongsTo(SP_oc_categories::class, 'ocCategory_id');
    }

    public function OCProductos()
    {
        return $this->hasMany(SP_oc_products::class, 'ocSubCategory_id');
    }
}
