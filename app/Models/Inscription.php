<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $connection = 'mysql-pompeyo';

    protected $table = 'VT_Inscripciones';

    protected $primaryKey = 'ID';

    protected $fillable = [ 'Patente', 'EtapaID'];
}
