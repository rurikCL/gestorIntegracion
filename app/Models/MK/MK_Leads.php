<?php

namespace App\Models\MK;

use App\Models\Api\API_LogSolicitud;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Notificaciones;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_LeadsEstados;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Origenes;
use App\Models\MA\MA_Regiones;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\MA\MA_Versiones;
use App\Models\VT\VT_Cotizaciones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MK_Leads extends Model
{
    use HasFactory;

    protected $table = 'MK_Leads';
    protected $connection = 'mysql-pompeyo';


    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'OrigenID',
        'SubOrigenID',
        'ClienteID',
        'SucursalID',
        'VendedorID',
        'MarcaID',
        'ModeloID',
        'VersionID',
        'EstadoID',
        'SubEstadoID',
        'Financiamiento',
        'CampanaID',
        'IntegracionID',
        'IDExterno',
        'ConcatID',
        'Asignado',
        'Llamado',
        'Agendado',
        'Venta',
        'CotizacionID',
        'Cotizado',
        'Vendido',
        'FechaReAsignado',
        'Comentario',
        'Contesta',
        'LandBotID',
        'Contactado',
        'LogEstado',
        'LinkInteres',
        'Nombre',
        'Rut',
        'Email',
        'Telefono',
        'SegundoNombre',
        'Apellido',
        'SegundoApellido',
        'ComunaID',
        'FechaNacimiento',
        'Direccion',
        'TIpoJuridicoID',
        'OrigenIngreso'
    ];

    public function cliente()
    {
        return $this->hasOne(MA_Clientes::class, 'ID', 'ClienteID');
    }

    public function cliente2()
    {
        return $this->belongsTo(MA_Clientes::class, 'ClienteID', 'ID');
    }

    public function estadoLead()
    {
        return $this->hasOne(MK_LeadsEstados::class, 'ID', 'EstadoID');
    }

    public function estadoLead2()
    {
        return $this->belongsTo(MK_LeadsEstados::class, 'EstadoID', 'ID');
    }

    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'MarcaID');
    }

    public function marca2()
    {
        return $this->belongsTo(MA_Marcas::class, 'MarcaID', 'ID');
    }

    public function modelo()
    {
        return $this->hasOne(MA_Modelos::class, 'ID', 'ModeloID');
    }

    public function modelo2()
    {
        return $this->belongsTo(MA_Modelos::class, 'ModeloID', 'ID');
    }

    public function version()
    {
        return $this->hasOne(MA_Versiones::class, 'ID', 'VersionID');
    }

    public function version2()
    {
        return $this->belongsTo(MA_Versiones::class, 'VersionID', 'ID');
    }

    public function origen()
    {
        return $this->hasOne(MA_Origenes::class, 'ID', 'OrigenID');
    }

    public function origen2()
    {
        return $this->belongsTo(MA_Origenes::class, 'OrigenID', 'ID');
    }

    public function subOrigen()
    {
        return $this->hasOne(MA_SubOrigenes::class, 'ID', 'SubOrigenID');
    }

    public function subOrigen2()
    {
        return $this->belongsTo(MA_SubOrigenes::class, 'SubOrigenID', 'ID');
    }

    public function sucursal()
    {
        return $this->hasOne(MA_Sucursales::class, 'ID', 'SucursalID');
    }

    public function sucursal2()
    {
        return $this->belongsTo(MA_Sucursales::class, 'SucursalID', 'ID');
    }


    public function usuario()
    {
        return $this->hasOne(MA_Usuarios::class, 'ID', 'VendedorID');
    }

    public function vendedor()
    {
        return $this->belongsTo(MA_Usuarios::class, 'VendedorID', 'ID');
    }


    public function notificacion()
    {
        return $this->hasOne(FLU_Notificaciones::class, 'ID_Ref', 'ID');
    }

    public function logs()
    {
        return $this->hasManyThrough(API_LogSolicitud::class, ApiSolicitudes::class,
            'ReferenciaID',
            'SolicitudID',
            'ID',
            'ID');
    }

    public function cotizacion()
    {
        return $this->hasOne(VT_Cotizaciones::class, 'ID', 'CotizacionID');
    }

    public function scopeValidado($query)
    {
        return $query->whereHas('cliente', function ($q) {
            $q->whereNotNull('Nombre')
                ->whereNotNull('Rut');
        });
    }

    public function scopePorMarca($query, $marca)
    {
        return $query->whereNotNull('MarcaID')
            ->whereHas('marca', function ($query) use ($marca) {
                return $query->where('Marca', $marca);
            });
    }


    public function scopeNoNotificado($query, $flujo)
    {
        /*return $query->doesntHave('notificacion')
            ->orWhereHas('notificacion', function ($query) use ($flujo) {
                $query->where('ID_Flujo', $flujo)
                    ->where('Notificado', 0);
            });*/

        return $query->select($this->table . '.*')
            ->leftJoin('FLU_Notificaciones', function ($join) use ($flujo) {
                $join->on('FLU_Notificaciones.ID_Ref', '=', $this->table . '.ID')
                    ->where('FLU_Notificaciones.ID_Flujo', '=', $flujo);
            })->where('FLU_Notificaciones.ID', null);
    }

    public function scopeDesde($query, $fecha)
    {
        return $query->where('FechaCreacion', '>=', $fecha);
    }

    public function scopeConEmail($query)
    {
        return $query->whereHas('cliente', function ($query) {
            $query->where('Email', '<>', '');
        });
    }
}
