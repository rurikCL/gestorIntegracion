<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfClient extends Model
{
    use HasFactory;

    protected $connection = 'mysql-pompeyo';

    protected $table = 'MA_TiposClientes';

    protected $primaryKey = 'ID';

    public $timestamps = false;
}
