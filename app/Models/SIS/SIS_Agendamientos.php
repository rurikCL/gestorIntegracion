<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIS_Agendamientos extends Model
{
    use HasFactory;
    protected $table = 'SIS_Agendamientos';
//    protected $connection = 'mysql';
    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'id';
    public $timestamps = false;
}
