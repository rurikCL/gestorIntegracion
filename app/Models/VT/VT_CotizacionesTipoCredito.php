<?php

namespace App\Models\VT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_CotizacionesTipoCredito extends Model
{
    use HasFactory;
    protected $table = 'VT_CotizacionesTipoCredito';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;
}
