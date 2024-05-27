<?php

namespace App\Models\VT;

use App\Models\FLU\FLU_Notificaciones;
use App\Models\Lead;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Origenes;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\MA\MA_Versiones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_Cotizaciones extends Model
{
    use HasFactory;

    protected $table = 'VT_Cotizaciones';
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
        'FechaCotizacion',
        'SucursalID',
        'VendedorID',
        'EjecutivoFI',
        'CanalID',
        'ClienteID',
        'OrigenID',
        'SubOrigenID',
        'EstadoID',
        'Patente',
        'MarcaID',
        'ModeloID',
        'VersionID',
        'Color',
        'EstadoVehiculo',
        'Anno',
        'Cantidad',
        'PrecioLista',
        'BonoMarca',
        'BonoFinanciamiento',
        'BonoAdicional',
        'ValorVehiculo',
        'TipoCreditoID',
        'CantidadCuotas',
        'FechaPagare',
        'FechaVencimiento',
        'MetodoPago',
        'Pie',
        'Retoma',
        'TasaInteres',
        'GastosOperacionales',
        'AdicionalesTotal',
        'SimulacionCuotaIDExterno',
        'ValorCuota',
        'Vfmg',
        'Preevaluacion',
        'SeguroDegravamen',
        'SeguroCesantia',
        'Testdrive',
        'Aval',
        'AvalClienteID',
        'ConcatID',
        'Agendado',
        'LeadID',
        'SolicitudCredito',
        'FechaSolicitudCredito',
        'SolCreditoIDExterno',
        'EnviaPDFEmail',
        'EnviaPDFWtsp',
        'FinancieraID',
        'RenovacionID',
        'Venta',
        'Vendido',
        'ForumTanner',
        'CotExterna',
        'VentaID',
        'TieneTasacion',
        'TieneRevision',
        'Llamado',
        'VisibleSegundas',
        'AdicionalesFinanciadosTotal',
        'Contesta',
        'LogTareas',
        'LogSeguimientos',
        'Observaciones',
        'Cantidades',
        'BonoFlotas',
        'DctoVendedor',
        'BonoCliente',
        'BonoMantencionIncluida',
        'concatMG',
        'SolCreditoSegIDExterno',
        'FechaSolicitudCreditoSegundas',
        'FechaCarga',
        'Bandera',
        'IDCarga',
        'ConcatNuevo',
        'VinProb',
        'ConcatAnterior'
    ];

    public function sucursal()
    {
        return $this->hasOne(MA_Sucursales::class, 'ID', 'SucursalID');
    }

    public function cliente()
    {
        return $this->hasOne(MA_Clientes::class, 'ID', 'ClienteID');
    }

    public function vendedor()
    {
        return $this->hasOne(MA_Usuarios::class, 'ID', 'VendedorID');
    }

    public function marca()
    {
        return $this->hasOne(MA_Marcas::class, 'ID', 'MarcaID');
    }

    public function modelo()
    {
        return $this->hasOne(MA_Modelos::class, 'ID', 'ModeloID');
    }

    public function version()
    {
        return $this->hasOne(MA_Versiones::class, 'ID', 'VersionID');
    }

    public function lead()
    {
        return $this->hasOne(Lead::class, 'ID', 'LeadID');
    }

    public function estado()
    {
        return $this->hasOne(VT_CotizacionesEstados::class, 'ID', 'EstadoID');
    }

    public function venta()
    {
        return $this->hasOne(VT_Ventas::class, 'ID', 'VentaID');
    }

    public function origen()
    {
        return $this->hasOne(MA_Origenes::class, 'ID', 'OrigenID');
    }

    public function subOrigen()
    {
        return $this->hasOne(MA_SubOrigenes::class, 'ID', 'SubOrigenID');
    }

    public function ejecutivo()
    {
        return $this->hasOne(MA_Usuarios::class, 'ID', 'EjecutivoFI');
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

    public function notificacion()
    {
        return $this->hasOne(FLU_Notificaciones::class, 'ID_Ref', 'ID');
    }
    public function scopeNoNotificado($query, $flujo)
    {
        /*return $query->doesntHave('notificacion')
            ->orWhereHas('notificacion', function ($query) use ($flujo) {
                $query->where('ID_Flujo', $flujo)
                    ->where('Notificado', 0);
            });*/

        return $query->select($this->table.'.*')
            ->leftJoin('FLU_Notificaciones', function ($join) use($flujo){
                $join->on('FLU_Notificaciones.ID_Ref', '=', $this->table.'.ID')
                    ->where('FLU_Notificaciones.ID_Flujo', '=', $flujo);
            })->where('FLU_Notificaciones.ID', null);
    }

    public function scopeDesde($query, $fecha)
    {
        return $query->where('FechaCreacion', '>=', $fecha);
    }


}
