<?php

namespace App\Models\VT;

use App\Models\APC_Stock;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\MA\MA_Vehiculos;
use App\Models\MA\MA_Versiones;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VT_EstadoResultado extends Model
{
    use HasFactory;
    protected $table = "VT_EstadoResultado";
    protected $connection = 'mysql-pompeyo';
    protected $primaryKey = 'ID';


    public function notificacion()
    {
        return $this->hasOne(FLU_Notificaciones::class, 'ID_Ref', 'ID');
    }

    public function cliente()
    {
        return $this->hasOne(MA_Clientes::class, 'ID', 'ClienteID');
    }

    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'MarcaID');
    }

    public function modelo()
    {
        return $this->hasOne(MA_Modelos::class, 'ID', 'ModeloID');
    }

    public function vendedor()
    {
        return $this->hasOne(MA_Usuarios::class, 'ID', 'VendedorID');
    }

    public function version()
    {
        return $this->hasOne(MA_Versiones::class, 'ID', 'VersionID');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'CodigoInterno', 'Cajon');
    }
    public function apcstock()
    {
        return $this->hasOne(APC_Stock::class, 'VIN', 'Vin');
    }

    public function sucursal()
    {
        return $this->hasOne(MA_Sucursales::class, 'ID', 'SucursalID');
    }

    public function venta()
    {
        return $this->hasOne(VT_Ventas::class, 'ID', 'VentaID');
    }


    // -------------------------------------------------------------------------------
    public function scopeNoNotificado($query, $flujo)
    {
        return $query->select($this->table.'.*')
            ->leftJoin('FLU_Notificaciones', function ($join) use($flujo){
                $join->on('FLU_Notificaciones.ID_Ref', '=', $this->table.'.ID')
                    ->where('FLU_Notificaciones.ID_Flujo', '=', $flujo);
            })->where('FLU_Notificaciones.ID', null);
    }


    public function scopeGerencia($query, $gerenciaID)
    {
        if (is_array($gerenciaID)){
            return $query->whereHas('sucursal', function ($query) use ($gerenciaID) {
                $query->whereIn('GerenciaID', $gerenciaID);
            });
        } else {
            return $query->whereHas('sucursal', function ($query) use ($gerenciaID) {
                $query->where('GerenciaID', $gerenciaID);
            });
        }

    }

    public function scopeFechaVenta($query, $fecha, $operador = '=')
    {
        return $query->whereHas('venta', function ($query) use ($fecha, $operador){
            return $query->where('FechaVenta',$operador, $fecha);
        });
    }
}
