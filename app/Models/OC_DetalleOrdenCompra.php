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

}
