<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfWorker extends Model
{
    use HasFactory;

    protected $table = 'MA_TipoTrabajador';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
}
