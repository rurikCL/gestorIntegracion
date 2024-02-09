<?php

namespace App\Models;

use App\Models\FLU\FLU_Notificaciones;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\MA\MA_Vehiculos;
use App\Models\MA\MA_Versiones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_Ventas extends Model
{
    use HasFactory;

    protected $connection = 'mysql-pompeyo';

    protected $table = 'VT_Ventas';

    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $dates = [
        'FechaVenta',
        'FechaEstimadaEntrega',
        'FechaFactura',
        'FechaActaEntrega',
        'FechaEstimadaFactura',
    ];

    protected $fillable = [
        'FechaCreacion',
        'EventoCreacionID',
        'UsuarioCreacionID',
        'FechaActualizacion',
        'EventoActualizacionID',
        'UsuarioActualizacionID',
        'FechaVenta',
        'CotizacionID',
        'SinCotizacion',
        'SucursalID',
        'VendedorID',
        'JefeSucursalID',
        'CanalID',
        'ClienteID',
        'VehiculoID',
        'Cajon',
        'MarcaID',
        'ModeloID',
        'VersionID',
        'CompraPara',
        'ClienteParaID',
        'NotaVenta',
        'EstadoVentaID',
        'TipoVentaID',
        'EntidadFinancieraID',
        'NumeroContrato',
        'AgendaPrimeraMantencion',
        'FechaEstimadaEntrega',
        'Comentario',
        'PrecioCompra',
        'PrecioLista',
        'BonoFinanciamiento',
        'BonoMarca',
        'BonoAdicional',
        'BonoFlotas',
        'BonoMantencionIncluida',
        'BonoOtros',
        'Combustible',
        'Logistica',
        'PreparacionUsados',
        'Multas',
        'GarantiaUsados',
        'SetYPisos',
        'DescuentoVendedor',
        'PreparacionPreEntrega',
        'AccesoriosVentaPompeyo',
        'AccesoriosCostoPompeyo',
        'AccesoriosCostoCliente',
        'AccesoriosVentaCliente',
        'TramiteCostoPompeyo',
        'TramiteVentaPompeyo',
        'TramiteCostoCliente',
        'TramiteVentaCliente',
        'GastosDirectosVehiculo',
        'UtilidadComercial',
        'ElementosFinanciables',
        'AccesoriosFinanciados',
        'TramitesFinanciados',
        'TipoMantencionID',
        'TieneSeguro',
        'SeguroID',
        'Patente',
        'FechaFactura',
        'NumeroFactura',
        'ValorFactura',
        'TransferenciaOtroConsecionario',
        'TipoCupon',
        'ColorReferencial',
        'CreditoInstantaneo',
        'CreditoFirmado',
        'CantidadCuota',
        'TasaInteres',
        'ComisionAcepNeta',
        'TipoCredito',
        'ValorCuota',
        'Pie',
        'Colocacion',
        'SaldoFinanciar',
        'SolicitarInscripcion',
        'TieneVPP',
        'EstadoActaEntregaID',
        'FechaActaEntrega',
        'EstadoFI',
        'MantencionVentaCliente',
        'MantencionesFinanciados',
        'TieneRevision',
        'TieneTasacion',
        'TieneAccesorio',
        'BancoID',
        'AseguradoraID',
        'Vin',
        'ColorID',
        'Deducible',
        'NumeroPoliza',
        'FormaPago',
        'MontoPrima',
        'SaldoCreditoPompeyo',
        'ModeloTxt',
        'TramiteNoFinanciado',
        'AccesorioNoFinanciado',
        'UsuarioFI',
        'AccesoriosInstalados',
        'BonoCliente',
        'BonoMarca2',
        'BonoFinanciamientoAdicional',
        'Provision_ComisionAcepNeta',
        'ComisionAcepNeta_Cancelado',
        'Provision_BonoCliente',
        'BonoCliente_Cancelado',
        'Provision_BonoMarca',
        'BonoMarca_Cancelado',
        'Provision_BonoFinanciamiento',
        'BonoFinanciamiento_Cancelado',
        'Provision_BonoFinanciamientoAdicional',
        'BonoFinanciamientoAdicional_Cancelado',
        'Provision_BonoFlotas',
        'BonoFlotas_Cancelado',
        'Provision_BonoMantencionIncluida',
        'BonoMantencionIncluida_Cancelado',
        'Tmp_ComisionAcepNeta_Cancelado',
        'Tmp_BonoCliente_Cancelado',
        'Tmp_BonoMarca_Cancelado',
        'Tmp_BonoFinanciamiento_Cancelado',
        'Tmp_BonoFlotas_Cancelado',
        'Tmp_BonoMantencionIncluida_Cancelado',
        'Tmp_BonoFinanciamientoAdicional_Cancelado',
        'TieneArchivoReserva',
        'Borrar',
        'ComisionUtilidadVend',
        'ComisionCreditosVend',
        'ComisionSegurosVend',
        'ComPorcVtas',
        'ComPorcCreditos',
        'ComPorcSeguros',
        'TieneFed',
        'FechaEstimadaFactura',
        'DuracionSeguro',
        'SolGarantiaUsados',
        'NumeroMotor',
    ];


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

    public function vehiculo()
    {
        return $this->hasOne(MA_Vehiculos::class, 'ID', 'VehiculoID');
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

    public function sucursal()
    {
        return $this->hasOne(MA_Sucursales::class, 'ID', 'SucursalID');
    }


    public function scopeNoNotificado($query, $flujo)
    {
        return $query->select($this->table.'.*')
        ->leftJoin('FLU_Notificaciones', function ($join) use($flujo){
            $join->on('FLU_Notificaciones.ID_Ref', '=', $this->table.'.ID')
                ->where('FLU_Notificaciones.ID_Flujo', '=', $flujo);
        })->where('FLU_Notificaciones.ID', null);
    }

    public function scopeVendido($query)
    {
        return $query->where('EstadoVentaID', 4);
    }

    public function scopeDesde($query, $fecha)
    {
        return $query->where('FechaVenta', '>=', $fecha);
    }

    public function scopeHasta($query, $fecha)
    {
        return $query->where('FechaVenta', '<=', $fecha);
    }

    public function scopeGerencia($query, $gerenciaID)
    {
        return $query->whereHas('sucursal', function ($query) use ($gerenciaID) {
            $query->where('GerenciaID', $gerenciaID);
        });
    }

}
