<?php

namespace App\Models\OC;

use App\Models\MA\MA_Usuarios;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OC_Approvers extends Model
{
    protected $connection = 'mysql-pompeyo';

    protected $table = 'OC_approvers';
    protected $primaryKey = 'ID';
    protected $fillable = [
        'ID',
        'branchOffice_id',
        'level',
        'user_id',
        'min',
        'max',
    ];

    public function usuarios()
    {
        return $this->belongsTo(MA_Usuarios::class, 'user_id', 'ID');
    }
}
