<?php

namespace App\Models\TDP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_Modelos extends Model
{
    use HasFactory;
    protected $table = 'TDP_Modelos';
//    protected $connection = 'mysql';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'IDAnterior',
        'Cruce',
        'IDNuevo',
    ];
}
