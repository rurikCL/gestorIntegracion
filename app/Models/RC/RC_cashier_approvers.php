<?php

namespace App\Models\RC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RC_cashier_approvers extends Model
{
    use HasFactory;
    protected $connection = 'mysql-pompeyo';
    protected $table = 'RC_cashier_approvers';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'cashier_id',
        'user_id',
        'replacement_id',
        'level',
        'min',
        'max',
        'branch_office_id',
        'active'
    ];

    public function sucursales()
    {
        return $this->belongsTo('App\Models\MA\MA_Sucursales', 'branch_office_id', 'ID');
    }

    public function usuarios()
    {
        return $this->belongsTo('App\Models\MA\MA_Usuarios', 'user_id', 'ID');
    }

    public function reemplazo()
    {
        return $this->belongsTo('App\Models\MA\MA_Usuarios', 'replacement_id', 'ID');
    }
}
