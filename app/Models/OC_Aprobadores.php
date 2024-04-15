<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OC_Aprobadores extends Model
{
    protected $table = 'OC_Aprobadores';
    protected $primaryKey = 'ID';
    protected $fillable = [
        'ID',
        'SucursalID',
        'Nivel',
        'UserID',
        'Min',
        'Max',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

}
