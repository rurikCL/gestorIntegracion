<?php

namespace App\Models\SP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_oc_quotegenerator extends Model
{
    use HasFactory;

    protected $table = 'SP_oc_quotegenerator';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'branchOffice_id',
        'user_id',
    ];
}
