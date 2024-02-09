<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOriginLead extends Model
{
    use HasFactory;

    protected $table = 'MA_SubOrigenes';
    protected $connection = 'mysql-pompeyo';

}
