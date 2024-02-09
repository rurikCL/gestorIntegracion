<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory;

    protected $table = 'MA_Comunas';

    protected $primaryKey = 'ID';
    protected $connection = 'mysql-pompeyo';

}
