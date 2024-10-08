<?php

namespace App\Models\RC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RC_cash_accounts extends Model
{
    use HasFactory;
    protected $connection = 'mysql-pompeyo';
    protected $table = 'RC_cash_accounts';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'number_account',
    ];


}
