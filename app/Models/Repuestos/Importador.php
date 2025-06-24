<?php

namespace App\Models\Repuestos;

use App\Models\Rental\RentalArchivos;
use App\Models\Rental\RentalFlujos;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Importador extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "RP_Importadores";
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = "ID";
    protected $fillable = [
        'ID',
        'Marca',
        'Importador',
        'Activo',
        'created_at',
        'updated_at',
        'deleted_at'
    ];



}
