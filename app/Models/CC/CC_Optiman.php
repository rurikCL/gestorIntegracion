<?php

namespace App\Models\CC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CC_Optiman extends Model
{
    use HasFactory;
    protected $table = 'CC_Optiman';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;
}
