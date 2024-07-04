<?php

namespace App\Models\OC;

use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OC_Approvals extends Model
{
    protected $connection = 'mysql-pompeyo';

    protected $table = 'OC_approvals';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ocOrderRequest_id',
        'approver_id',
        'level',
        'state',
        'type',
    ];


    public function usuarios()
    {
        return $this->belongsTo(MA_Usuarios::class, 'approver_id', 'ID');
    }

    public function orden()
    {
        return $this->belongsTo(OC_purchase_orders::class, 'ocOrderRequest_id', 'id');
    }
}
