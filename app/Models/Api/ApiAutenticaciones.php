<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiAutenticaciones extends Model
{
    use HasFactory;

    protected $table = 'API_Autenticaciones';
    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ProveedorID',
        'Token1',
        'Token2',
        'Expiration',
        'FechaInicio',
        'FechaExpiracion',
        'Status',
    ];

    public function proveedores()
    {
        return $this->belongsTo( ApiProveedores::class, 'ProveedorID', 'id' );
    }
}
