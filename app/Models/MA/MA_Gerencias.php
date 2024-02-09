<?php

namespace App\Models\MA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MA_Gerencias extends Model
{
    use HasFactory;

    protected $table = 'MA_Gerencias';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'UnidadNegocioID',
        'Gerencia',
        'Vehiculos',
        'Activo',
        'Visible',
        'MarcaAsociada',
    ];

    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'MarcaAsociada');
    }
}
