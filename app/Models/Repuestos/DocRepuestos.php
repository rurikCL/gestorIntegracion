<?php

namespace App\Models\Repuestos;

use App\Models\Rental\RentalArchivos;
use App\Models\Rental\RentalFlujos;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocRepuestos extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "RP_Documentos";
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = "ID";
    protected $fillable = [
        'ID',
        'Url',
        'TipoDocumento',
        'NumeroDocumento',
        'SubidoPor',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function Pedido()
    {
        return $this->hasOne(FlujoRepuesto::class, 'ID', 'FlujoID');
    }
    public function SubidoPor()
    {
        return $this->hasOne(User::class, 'ID', 'SubidoPor');
    }



}
