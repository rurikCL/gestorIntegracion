<?php

namespace App\Models\RC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RC_cashier_approvals extends Model
{
    use HasFactory;
    protected $connection = 'mysql-pompeyo';
    protected $table = 'RC_cashier_approvals';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cash_id',
        'cashier_approver_id',
        'level',
        'state',
        'active',
    ];



    public function usuarios()
    {
        return $this->belongsTo('App\Models\MA\MA_Usuarios', 'cashier_approver_id', 'ID');
    }

    public function cash(){
        return $this->belongsTo('App\Models\RC\RC_cashes', 'cash_id');
    }
}
