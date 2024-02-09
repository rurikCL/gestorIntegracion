<?php

namespace App\Models\CC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CC_Reclamos extends Model
{
    use HasFactory;
    protected $table = 'CC_Reclamos';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;
}
