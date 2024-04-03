<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_IndicadorMonetario extends Model
{
    use HasFactory;
    protected $table = 'MA_IndicadorMonetario';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = true;
    protected $fillable = [
        'Monto',
        'Tipo',
        'FechaIndicador',
        'Fuente',
    ];
}
