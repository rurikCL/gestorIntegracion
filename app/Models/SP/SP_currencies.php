<?php

namespace App\Models\SP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_currencies extends Model
{
    use HasFactory;
    protected $table = 'SP_currencies';
    protected $primaryKey = 'id';

}
