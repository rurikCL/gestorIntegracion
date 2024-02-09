<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OriginLead extends Model
{
    use HasFactory;

    protected $table = 'MA_Origenes';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
}
