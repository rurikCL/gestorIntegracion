<?php

namespace App\Models\TK;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TKc_Tickets extends Model
{
    use HasFactory;
    protected $table = 'TKc_tickets';
    protected $connection = 'mysql';
//    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'id';
    public $timestamps = false;
}
