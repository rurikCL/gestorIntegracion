<?php

namespace App\Models\MK;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MK_LeadsEstados extends Model
{
    use HasFactory;
    protected $table = 'MK_LeadsEstados';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;
}
