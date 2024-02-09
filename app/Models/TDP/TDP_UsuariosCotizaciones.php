<?php

namespace App\Models\TDP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_UsuariosCotizaciones extends Model
{
    use HasFactory;
    protected $table = 'TDP_UsuariosCotizaciones';
//    protected $connection = 'mysql';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'IDRoma',
        'NombreRoma',
    ];
}
