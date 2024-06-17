<?php

namespace App\Models\MA;

use App\Models\SP\SP_oc_order_requests;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_PompeyoEmpresas extends Model
{
    protected $table = 'MA_PompeyoEmpresas';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Empresa',
        'Rut',
        'Activo',
        'H_TannerID',
        'Direccion',
        'Telefono',
        'NumeroCuenta',
    ];

    public function spOrderRequest()
    {
        return $this->belongsTo(SP_oc_order_requests::class, 'ID', 'business_id');
    }

}
