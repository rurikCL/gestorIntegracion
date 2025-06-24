<?php

namespace App\Models\Repuestos;

use App\Models\dyp\Marca;
use App\Models\Rental\RentalArchivos;
use App\Models\Rental\RentalFlujos;
use App\Models\Sucursales;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlujoRepuesto extends Model
{
    use SoftDeletes;
    protected $table = "RP_Flujo";
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = "ID";
    protected $fillable = [
        'ID',
        'NumeroPedido',
        'Estado',
        'Disponible',
        'FechaSolicitado',
        'SolicitadoPor',
        'FechaTermino',
        'CreadoPor',
        'TerminadoPor',
        'Importador',
        'DypID',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function Creador()
    {
        return $this->hasOne(User::class, 'ID', 'CreadoPor');
    }

    public function Terminador()
    {
        return $this->hasOne(User::class, 'ID', 'TerminadoPor');
    }


    public function Importador()
    {
        return $this->hasOne(Importador::class, 'ID', 'ImportadorID');
    }
    public function Sucursal()
    {
        return $this->hasOne(Sucursales::class, 'ID', 'SucursalID');
    }
    public function Repuestos()
    {
        return $this->belongsTo(Repuestos::class, 'ID', 'FlujoID');
    }
    public function Marca()
    {
        return $this->hasOne(Marca::class, 'ID', 'MarcaID');
    }

    public function Proveedor()
    {
        return $this->hasOne(RepuestosProveedor::class, 'ID', 'ProveedorID');
    }


}
