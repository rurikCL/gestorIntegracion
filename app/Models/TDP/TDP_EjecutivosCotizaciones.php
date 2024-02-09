<?php

namespace App\Models\TDP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_EjecutivosCotizaciones extends Model
{
    use HasFactory;
    protected $table = 'TDP_EjecutivosCotizaciones';
//    protected $connection = 'mysql';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'IDRoma',
        'NombreRoma',
    ];
}
