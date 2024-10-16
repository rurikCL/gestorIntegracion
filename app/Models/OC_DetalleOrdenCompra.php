<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OC_DetalleOrdenCompra extends Model
{
    protected $table = "OC_detail_purchase_orders";

    protected $connection = 'mysql-pompeyo';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'ocCategory_id',
        'ocSubCategory_id',
        'ocProduct_id',
        'amount',
        'unitPrice',
        'totalPrice',
        'taxAmount',
        'taxe',
        'branch_id',
        'ocPurchaseOrder_id',
        'description',
        'typeOfBranch_id',
        'section_id',
    ];

    public function categoria(){
        return $this->hasOne('App\Models\SP\SP_oc_categories', 'ID', 'ocCategory_id');
    }
    public function subcategoria(){
        return $this->hasOne('App\Models\SP\SP_oc_sub_categories', 'ID', 'ocSubCategory_id');
    }
    public function producto(){
        return $this->hasOne('App\Models\SP\SP_oc_products', 'ID', 'ocProduct_id');
    }

}
