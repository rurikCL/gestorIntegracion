<?php

namespace App\Models\SP;

use App\Models\OC\OC_purchase_orders;
use App\Models\OC_OrdenCompra;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_orders_requests extends Model
{
    use HasFactory;

    protected $table = 'SP_orders_requests';
    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id',
        'request_id'
    ];

    public function ordenCompra()
    {
        return $this->hasOne(OC_purchase_orders::class, 'id', 'order_id');
    }

    public function solicitudCompra()
    {
        return $this->hasOne(SP_oc_order_requests::class, 'id', 'request_id');
    }
}
