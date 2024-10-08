<?php

namespace App\Models\RC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RC_cashes extends Model
{
    use HasFactory;
    protected $connection = 'mysql-pompeyo';
    protected $table = 'RC_cashes';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'branch_office_id',
        'total',
        'comment',
        'status',
    ];

    public function sucursales()
    {
        return $this->belongsTo('App\Models\MA\MA_Sucursales', 'branch_office_id', 'ID');
    }

    public function usuarios()
    {
        return $this->belongsTo('App\Models\MA\MA_Usuarios', 'user_id', 'ID');
    }

    public function aprobadores()
    {
        return $this->hasMany('App\Models\RC\RC_cashier_approvals', 'cash_id', 'id');
    }

    public function articulos(){
        return $this->hasMany('App\Models\RC\RC_cash_details', 'cash_id', 'id');
    }
}
