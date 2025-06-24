<?php

namespace App\Models\Repuestos;

use App\Models\dyp\Marca;
use App\Models\Rental\RentalArchivos;
use App\Models\Rental\RentalFlujos;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repuestos extends Model
{
    use SoftDeletes;
    protected $table = "RP_Repuestos";
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = "ID";
    protected $fillable = [
        'ID',
        'FlujoID',
        'SKU',
        'Repuesto',
        'Tipo',
        'Cantidad',
        'Valor',
        'Estado',
        'esVor',
        'Disponible',
        'FechaSolicitado',
        'FechaEstimada',
        'FechaLlegada',
        'FechaEntrega',
        'SolicitadoPor',
        'CreadoPor',
        'RecibidoPor',
        'EntregadoPor',
        'Importador',
        'DypID',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected function tieneVor(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => ($attributes['esVor'] == 0) ? 'No' : (($attributes['esVor'] == 1 && $attributes['Estado'] != 'En importación')?'Ok':'Si' ),
        );
    }

    protected function totalNeto(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['Cantidad'] * $attributes['Valor'],
        );
    }

    public function Pedido()
    {
        return $this->hasOne(FlujoRepuesto::class, 'ID', 'FlujoID');
    }
    public function Creador()
    {
        return $this->hasOne(User::class, 'ID', 'CreadoPor');
    }
    public function Solicitante()
    {
        return $this->hasOne(User::class, 'ID', 'SolicitadoPor');
    }

    public function Terminador()
    {
        return $this->hasOne(User::class, 'ID', 'TerminadoPor');
    }


    public function Importador()
    {
        return $this->hasOne(Importador::class, 'ID', 'ImportadorID');
    }

    public function Factura()
    {
        return $this->hasOne(DocRepuestos::class, 'ID', 'FacturaID');
    }
    public function Marca()
    {
        return $this->hasOne(Marca::class, 'ID', 'MarcaID');
    }


}
