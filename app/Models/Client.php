<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $connection = 'mysql-pompeyo';

    protected $table = 'MA_Clientes';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public function typeOfWorker()
    {
        return $this->belongsTo( TypeOfWorker::class, 'TipoTrabajadorID', 'ID' );
    }

    public function typeOfClient()
    {
        return $this->belongsTo( TypeOfClient::class, 'TipoClienteID', 'ID' );
    }

    public function commune()
    {
        return $this->belongsTo( Commune::class, 'ComunaID', 'ID');
    }
}
