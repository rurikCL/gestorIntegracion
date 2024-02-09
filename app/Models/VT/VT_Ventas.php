<?php

namespace App\Models\VT;

use App\Models\CC\CC_Optiman;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_TipoMantencion;
use App\Models\SIS\SIS_Solicitudes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VT_Ventas extends Model
{
    use HasFactory;

    protected $table = 'VT_Ventas';
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
        'NumeroMotor'
    ];


    public function vpp()
    {
        return $this->hasMany(VT_Vpp::class, 'VentaID', 'ID');
    }
    public function tramite()
    {
        return $this->hasMany(VT_VentasTramites::class, 'VentaID', 'ID');
    }

    public function cotizacionesTipoCredito()
    {
        return $this->hasOne(VT_CotizacionesTipoCredito::class, 'ID', 'TipoCredito');
    }

    public function sucursal()
    {
        return $this->hasOne(MA_Sucursales::class, 'ID', 'SucursalID');
    }

    public function modelo()
    {
        return $this->hasOne(MA_Modelos::class, 'ID', 'ModeloID');
    }

    public function tipoMantencion()
    {
        return $this->hasOne(MA_TipoMantencion::class, 'ID', 'TipoMantencionID');
    }

    public function optiman()
    {
        return $this->hasOne(CC_Optiman::class, 'VentaID', 'ID');
    }

    public function solicitudes()
    {
        return $this->hasMany(SIS_Solicitudes::class, 'ReferenciaID', 'ID');
    }


    public function scopeInfoCliente($query)
    {
        return $query->has('optiman', function ($q) {

        });
    }
}
