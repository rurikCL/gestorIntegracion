<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    use HasFactory;

    protected $table = 'MA_Gerencias';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
}
