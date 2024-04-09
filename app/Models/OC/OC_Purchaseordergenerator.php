<?php

namespace App\Models\OC;

use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OC_Purchaseordergenerator extends Model
{
    protected $table = "OC_purchaseordergenerator";
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'branchOffice_id',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(MA_Usuarios::class, 'user_id', 'ID');
    }
    public function branch()
    {
        return $this->belongsTo(MA_Sucursales::class, 'branchOffice_id', 'ID');
    }

}
