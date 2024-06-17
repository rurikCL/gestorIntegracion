<?php

namespace App\Models\SP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_oc_detail_order_requests extends Model
{
    use HasFactory;

    protected $table = 'SP_oc_detail_order_requests';
    protected $primaryKey = 'id';
    protected $fillable = [
        'ocCategory_id',
        'ocSubCategory_id',
        'ocProduct_id',
        'amount',
        'unitPrice',
        'totalPrice',
        'ocOrderRequest_id',
        'state',
        'description',
    ];

    public function categoriaOC()
    {
        return $this->hasOne(SP_oc_categories::class, 'id', 'ocCategory_id');
    }

    public function subCategoriaOC()
    {
        return $this->hasOne(SP_oc_sub_categories::class, 'id', 'ocSubCategory_id');
    }

    public function productoOC()
    {
        return $this->hasOne(SP_oc_products::class, 'id', 'ocProduct_id')
            ->where('active', 1);
    }
}
