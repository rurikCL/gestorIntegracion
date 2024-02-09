<?php

namespace App\Models\FLU;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FLU_OrigenFuente extends Model
{
    use HasFactory;

    protected $table = 'FLU_OrigenFuente';
    protected $connection = 'mysql-pompeyo';
    protected $primaryKey = 'ID';
    protected $fillable = [
        'OrigenFuente',
        'Descripcion',
        'OrigenID',
        'SubOrigenID',
        'Activo',
    ];
}
