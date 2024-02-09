<?php

namespace App\Filament\Resources\VT;

use App\Filament\Resources\VT\VTVentasResource\Pages;
use App\Models\User;
use App\Models\VT\VT_Ventas;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use function Psl\Dict\group_by;

class VTVentasResource extends Resource
{
    protected static ?string $model = VT_Ventas::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $modelLabel = 'Ventas';
    protected static ?string $navigationGroup = 'Administracion';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Venta')
                        ->schema([
                            Forms\Components\TextInput::make('ID'),
                            Forms\Components\TextInput::make('FechaVenta'),
                            Forms\Components\TextInput::make('CotizacionID'),
                            Forms\Components\TextInput::make('SinCotizacion'),
                            Forms\Components\TextInput::make('SucursalID'),

                            Forms\Components\TextInput::make('NotaVenta'),
                            Forms\Components\TextInput::make('EstadoVentaID'),
                            Forms\Components\TextInput::make('TipoVentaID'),
                            Forms\Components\TextInput::make('EntidadFinancieraID'),
                            Forms\Components\TextInput::make('NumeroContrato'),
                        ])->columns(2),
                    Forms\Components\Section::make('Fechas')
                        ->schema([
                            Forms\Components\TextInput::make('FechaCreacion')
                                ->disabled()
                                ->default(date('Y-m-d H:i:s')),
                            Forms\Components\TextInput::make('EventoCreacionID')
                                ->disabled()
                                ->default(1),
                            Forms\Components\TextInput::make('UsuarioCreacionID')
                                ->disabled()
                                ->default(1),
                            Forms\Components\TextInput::make('FechaActualizacion')
                                ->disabled()
                                ->default(date('Y-m-d H:i:s')),
                            Forms\Components\TextInput::make('EventoActualizacionID')
                                ->disabled()
                                ->default(1),
                            Forms\Components\TextInput::make('UsuarioActualizacionID')
                                ->disabled()
                                ->default(1),
                        ])->columns(2),
                ]),
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Cliente')
                    ->schema([
                        Forms\Components\TextInput::make('ClienteID'),
                    ]),

                    Forms\Components\Section::make('Vendedor')
                    ->schema([
                        Forms\Components\TextInput::make('VendedorID'),
                        Forms\Components\TextInput::make('JefeSucursalID'),

                    ]),
                    Forms\Components\Section::make('Vehiculo')
                    ->schema([
                        Forms\Components\TextInput::make('VehiculoID'),
                        Forms\Components\TextInput::make('MarcaID'),
                        Forms\Components\TextInput::make('ModeloID'),
                        Forms\Components\TextInput::make('VersionID'),
                        Forms\Components\TextInput::make('Cajon'),

                    ])
                ]),

                Forms\Components\TextInput::make('CanalID'),
                Forms\Components\TextInput::make('CompraPara'),
                Forms\Components\TextInput::make('ClienteParaID'),
                Forms\Components\TextInput::make('AgendaPrimeraMantencion'),
                Forms\Components\TextInput::make('FechaEstimadaEntrega'),
                Forms\Components\TextInput::make('Comentario'),
                Forms\Components\TextInput::make('PrecioCompra'),
                Forms\Components\TextInput::make('PrecioLista'),
                Forms\Components\TextInput::make('BonoFinanciamiento'),
                Forms\Components\TextInput::make('BonoMarca'),
                Forms\Components\TextInput::make('BonoAdicional'),
                Forms\Components\TextInput::make('BonoFlotas'),
                Forms\Components\TextInput::make('BonoMantencionIncluida'),
                Forms\Components\TextInput::make('BonoOtros'),
                Forms\Components\TextInput::make('Combustible'),
                Forms\Components\TextInput::make('Logistica'),
                Forms\Components\TextInput::make('PreparacionUsados'),
                Forms\Components\TextInput::make('Multas'),
                Forms\Components\TextInput::make('GarantiaUsados'),
                Forms\Components\TextInput::make('SetYPisos'),
                Forms\Components\TextInput::make('DescuentoVendedor'),
                Forms\Components\TextInput::make('PreparacionPreEntrega'),
                Forms\Components\TextInput::make('AccesoriosVentaPompeyo'),
                Forms\Components\TextInput::make('AccesoriosCostoPompeyo'),
                Forms\Components\TextInput::make('AccesoriosCostoCliente'),
                Forms\Components\TextInput::make('AccesoriosVentaCliente'),
                Forms\Components\TextInput::make('TramiteCostoPompeyo'),
                Forms\Components\TextInput::make('TramiteVentaPompeyo'),
                Forms\Components\TextInput::make('TramiteCostoCliente'),
                Forms\Components\TextInput::make('TramiteVentaCliente'),
                Forms\Components\TextInput::make('GastosDirectosVehiculo'),
                Forms\Components\TextInput::make('UtilidadComercial'),
                Forms\Components\TextInput::make('ElementosFinanciables'),
                Forms\Components\TextInput::make('AccesoriosFinanciados'),
                Forms\Components\TextInput::make('TramitesFinanciados'),
                Forms\Components\TextInput::make('TipoMantencionID'),
                Forms\Components\TextInput::make('TieneSeguro'),
                Forms\Components\TextInput::make('SeguroID'),
                Forms\Components\TextInput::make('Patente'),
                Forms\Components\TextInput::make('FechaFactura'),
                Forms\Components\TextInput::make('NumeroFactura'),
                Forms\Components\TextInput::make('ValorFactura'),
                Forms\Components\TextInput::make('TransferenciaOtroConsecionario'),
                Forms\Components\TextInput::make('TipoCupon'),
                Forms\Components\TextInput::make('ColorReferencial'),
                Forms\Components\TextInput::make('CreditoInstantaneo'),
                Forms\Components\TextInput::make('CreditoFirmado'),
                Forms\Components\TextInput::make('CantidadCuota'),
                Forms\Components\TextInput::make('TasaInteres'),
                Forms\Components\TextInput::make('ComisionAcepNeta'),
                Forms\Components\TextInput::make('TipoCredito'),
                Forms\Components\TextInput::make('ValorCuota'),
                Forms\Components\TextInput::make('Pie'),
                Forms\Components\TextInput::make('Colocacion'),
                Forms\Components\TextInput::make('SaldoFinanciar'),
                Forms\Components\TextInput::make('SolicitarInscripcion'),
                Forms\Components\TextInput::make('TieneVPP'),
                Forms\Components\TextInput::make('EstadoActaEntregaID'),
                Forms\Components\TextInput::make('FechaActaEntrega'),
                Forms\Components\TextInput::make('EstadoFI'),
                Forms\Components\TextInput::make('MantencionVentaCliente'),
                Forms\Components\TextInput::make('MantencionesFinanciados'),
                Forms\Components\TextInput::make('TieneRevision'),
                Forms\Components\TextInput::make('TieneTasacion'),
                Forms\Components\TextInput::make('TieneAccesorio'),
                Forms\Components\TextInput::make('BancoID'),
                Forms\Components\TextInput::make('AseguradoraID'),
                Forms\Components\TextInput::make('Vin'),
                Forms\Components\TextInput::make('ColorID'),
                Forms\Components\TextInput::make('Deducible'),
                Forms\Components\TextInput::make('NumeroPoliza'),
                Forms\Components\TextInput::make('FormaPago'),
                Forms\Components\TextInput::make('MontoPrima'),
                Forms\Components\TextInput::make('SaldoCreditoPompeyo'),
                Forms\Components\TextInput::make('ModeloTxt'),
                Forms\Components\TextInput::make('TramiteNoFinanciado'),
                Forms\Components\TextInput::make('AccesorioNoFinanciado'),
                Forms\Components\TextInput::make('UsuarioFI'),
                Forms\Components\TextInput::make('AccesoriosInstalados'),
                Forms\Components\TextInput::make('BonoCliente'),
                Forms\Components\TextInput::make('BonoMarca2'),
                Forms\Components\TextInput::make('BonoFinanciamientoAdicional'),
                Forms\Components\TextInput::make('Provision_ComisionAcepNeta'),
                Forms\Components\TextInput::make('ComisionAcepNeta_Cancelado'),
                Forms\Components\TextInput::make('Provision_BonoCliente'),
                Forms\Components\TextInput::make('BonoCliente_Cancelado'),
                Forms\Components\TextInput::make('Provision_BonoMarca'),
                Forms\Components\TextInput::make('BonoMarca_Cancelado'),
                Forms\Components\TextInput::make('Provision_BonoFinanciamiento'),
                Forms\Components\TextInput::make('BonoFinanciamiento_Cancelado'),
                Forms\Components\TextInput::make('Provision_BonoFinanciamientoAdicional'),
                Forms\Components\TextInput::make('BonoFinanciamientoAdicional_Cancelado'),
                Forms\Components\TextInput::make('Provision_BonoFlotas'),
                Forms\Components\TextInput::make('BonoFlotas_Cancelado'),
                Forms\Components\TextInput::make('Provision_BonoMantencionIncluida'),
                Forms\Components\TextInput::make('BonoMantencionIncluida_Cancelado'),
                Forms\Components\TextInput::make('Tmp_ComisionAcepNeta_Cancelado'),
                Forms\Components\TextInput::make('Tmp_BonoCliente_Cancelado'),
                Forms\Components\TextInput::make('Tmp_BonoMarca_Cancelado'),
                Forms\Components\TextInput::make('Tmp_BonoFinanciamiento_Cancelado'),
                Forms\Components\TextInput::make('Tmp_BonoFlotas_Cancelado'),
                Forms\Components\TextInput::make('Tmp_BonoMantencionIncluida_Cancelado'),
                Forms\Components\TextInput::make('Tmp_BonoFinanciamientoAdicional_Cancelado'),
                Forms\Components\TextInput::make('TieneArchivoReserva'),
                Forms\Components\TextInput::make('Borrar'),
                Forms\Components\TextInput::make('ComisionUtilidadVend'),
                Forms\Components\TextInput::make('ComisionCreditosVend'),
                Forms\Components\TextInput::make('ComisionSegurosVend'),
                Forms\Components\TextInput::make('ComPorcVtas'),
                Forms\Components\TextInput::make('ComPorcCreditos'),
                Forms\Components\TextInput::make('ComPorcSeguros'),
                Forms\Components\TextInput::make('TieneFed'),
                Forms\Components\TextInput::make('FechaEstimadaFactura'),
                Forms\Components\TextInput::make('DuracionSeguro'),
                Forms\Components\TextInput::make('SolGarantiaUsados'),
                Forms\Components\TextInput::make('NumeroMotor'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')->searchable(),
                Tables\Columns\TextColumn::make('FechaVenta')
                    ->searchable()
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVTVentas::route('/'),
            'create' => Pages\CreateVTVentas::route('/create'),
            'edit' => Pages\EditVTVentas::route('/{record}/edit'),
        ];
    }

}
