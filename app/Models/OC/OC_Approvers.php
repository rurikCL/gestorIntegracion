<?php

namespace App\Models\OC;

use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use Illuminate\Database\Eloquent\Model;

class OC_Approvers extends Model
{
    protected $connection = 'mysql-pompeyo';

    protected $table = 'OC_approvers';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'branchOffice_id',
        'level',
        'user_id',
        'min',
        'max',
    ];

    public function sucursales()
    {
        return $this->belongsTo(MA_Sucursales::class, 'branchOffice_id', 'ID');
    }

    public function usuarios()
    {
        return $this->belongsTo(MA_Usuarios::class, 'user_id', 'ID');
    }
}
