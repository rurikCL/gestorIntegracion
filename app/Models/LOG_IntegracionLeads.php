<?php

namespace App\Models;

use App\Models\MA\MA_Clientes;
use App\Models\MK\MK_Leads;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LOG_IntegracionLeads extends Model
{
    use HasFactory;

    protected $table = 'LOG_IntegracionLeads';
    protected $connection = 'mysql-pompeyo';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Fecha',
        'Rut',
        'Nombre',
        'Email',
        'Telefono',
        'Modelo',
        'Version',
        'Sucursal',
        'Origen',
        'SubOrigen',
        'IdExterno',
        'SP'
    ];

    public function scopeInfo($query, $data)
    {
        return $query->create([
            'Fecha' => $data['Fecha'],
            'Rut' => $data['Rut'],
            'Nombre' => $data['Nombre'],
            'Email' => $data['Email'],
            'Telefono' => $data['Telefono'],
            'Modelo' => $data['Modelo'],
            'Version' => $data['Version'],
            'Sucursal' => $data['Sucursal'],
            'Origen' => $data['Origen'],
            'SubOrigen' => $data['SubOrigen'],
            'IdExterno' => $data['IdExterno'],
            'SP' => $data['SP'],
        ]);
    }
}
