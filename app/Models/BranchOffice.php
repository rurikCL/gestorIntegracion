<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchOffice extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID';

    protected $table = 'MA_Sucursales';
    protected $connection = 'mysql-pompeyo';

}
