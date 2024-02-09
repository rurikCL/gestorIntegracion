<?php

namespace App\Models\TDP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TDP_CotizacionesEstados extends Model
{
    use HasFactory;
    protected $table = 'TDP_CotizacionesEstados';
//    protected $connection = 'mysql';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'IDAnterior',
        'IDNuevo',
    ];
}
