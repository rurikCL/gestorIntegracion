<?php

namespace App\Models\OC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OC_purchase_orders extends Model
{
    use HasFactory;
    protected $table = "OC_purchase_orders";

    protected $connection = 'mysql-pompeyo';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'business_id',
        'brand_id',
        'branch_id',
        'typeOfBranch_id',
        'buyers_id',
        'state',
        'provider',
        'condition',
        'comment',
        'ocOrderRequest_ids',
        'direction',
        'commune',
        'contact_id',
        'pre_oc',
    ];


    public function sucursal()
    {
        return $this->belongsTo('App\Models\MA\MA_Sucursales', 'branch_id', 'ID');
    }

    public function marca()
    {
        return $this->belongsTo('App\Models\MA\MA_Marcas', 'branch_id', 'ID');
    }

    public function gerencia()
    {
        return $this->belongsTo('App\Models\MA\MA_Gerencias', 'brand_id', 'ID');
    }

    public function comprador() :hasOne
    {
        return $this->hasOne('App\Models\MA\MA_Usuarios', 'ID', 'buyers_id');
    }

    public function contacto() :hasOne
    {
        return $this->hasOne('App\Models\MA\MA_Usuarios', 'ID', 'contact_id');
    }

    public function empresa() :hasOne
    {
        return $this->hasOne('App\Models\MA\MA_PompeyoEmpresas', 'ID', 'business_id');
    }

    public function tipoSucursal() : hasOne
    {
        return $this->hasOne('App\Models\MA\MA_TipoSucursal', 'ID', 'typeOfBranch_id');
    }
}
