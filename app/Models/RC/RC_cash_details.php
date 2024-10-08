<?php

namespace App\Models\RC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RC_cash_details extends Model
{
    use HasFactory;
    protected $connection = 'mysql-pompeyo';
    protected $table = 'RC_cash_details';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'number_document',
        'date',
        'type_document',
        'provider',
        'description',
        'account_id',
        'total',
        'cash_id',
        'state',
    ];


    public function cash(){
        return $this->belongsTo('App\Models\RC\RC_cashes', 'cash_id');
    }

    public function account(){
        return $this->belongsTo('App\Models\RC\RC_cash_accounts', 'account_id');
    }
}
