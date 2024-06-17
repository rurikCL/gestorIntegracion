<?php

namespace App\Models\SP;

use App\Models\MA\MA_Gerencias;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_PompeyoEmpresas;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\OC\OC_purchase_orders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_oc_order_requests extends Model
{
    use HasFactory;

    protected $table = 'SP_oc_order_requests';
    protected $primaryKey = 'id';
    protected $fillable = [
        'business_id',
        'brand_id',
        'branch_id',
        'typeOfBranch_id',
        'buyers_id',
        'section_id',
        'state',
    ];

    public function empresa()
    {
        return $this->hasOne(MA_PompeyoEmpresas::class, 'ID', 'business_id')
            ->where('Activo', 1);
    }
    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'brand_id');
    }
    public function sucursal()
    {
        return $this->hasOne(MA_Sucursales::class, 'ID', 'branch_id');
    }

    public function comprador()
    {
        return $this->hasOne(MA_Usuarios::class, 'ID', 'buyers_id');
    }

    public function ordenCompra()
    {
        return $this->hasOne(SP_orders_requests::class, 'request_id', 'id');
    }

    public function detalleOrdenCompra()
    {
        return $this->hasMany(SP_oc_detail_order_requests::class, 'ocOrderRequest_id', 'id');
    }
}
