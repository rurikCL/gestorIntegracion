<?php

namespace App\Filament\Resources\VT;

use App\Filament\Resources\VT\VTCotizacionesResource\Pages;
use App\Filament\Resources\VT\VTCotizacionesResource\RelationManagers;
use App\Models\VT\VT_Cotizaciones;
use App\Models\VT\VTCotizaciones;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VTCotizacionesResource extends Resource
{
    protected static ?string $model = VT_Cotizaciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Cotizaciones';
    protected static ?string $navigationGroup = 'Administracion';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Cotización')
                    ->schema([
                        Forms\Components\TextInput::make('ID')
                            ->readOnly(),
                        Forms\Components\TextInput::make('FechaCreacion')
                            ->readOnly(),
                        Forms\Components\Select::make('SucursalID')
                            ->relationship('sucursal', 'Sucursal')
                            ->searchable(),
                        Forms\Components\Select::make('VendedorID')
                            ->relationship('vendedor', 'Nombre'),
                        Forms\Components\Select::make('EjecutivoFI')
                            ->relationship('ejecutivo', 'Nombre'),
                        Forms\Components\TextInput::make('CanalID'),
                        Forms\Components\Select::make('ClienteID')
                            ->relationship('cliente', 'Nombre')
                            ->searchable(),
                        Forms\Components\Select::make('OrigenID')
                            ->relationship('origen', 'Origen'),
                        Forms\Components\Select::make('SubOrigenID')
                            ->relationship('suborigen', 'SubOrigen'),
                        Forms\Components\Select::make('EstadoID')
                            ->relationship('estado', 'Estado'),
                        Forms\Components\TextInput::make('Patente'),
                        Forms\Components\Select::make('MarcaID')
                            ->relationship('marca', 'Marca'),
                        Forms\Components\Select::make('ModeloID')
                            ->relationship('modelo', 'Modelo'),
                        Forms\Components\Select::make('VersionID')
                            ->relationship('version', 'Version'),
                        Forms\Components\TextInput::make('PrecioLista'),
                        Forms\Components\TextInput::make('BonoMarca'),
                        Forms\Components\TextInput::make('BonoFinanciamiento'),
                        Forms\Components\TextInput::make('BonoAdicional'),
                        Forms\Components\TextInput::make('ValorVehiculo'),
                        Forms\Components\TextInput::make('TipoCreditoID'),
                        Forms\Components\TextInput::make('CantidadCuotas'),
                        Forms\Components\TextInput::make('FechaPagare'),
                        Forms\Components\TextInput::make('FechaVencimiento'),
                        Forms\Components\TextInput::make('MetodoPago'),
                        Forms\Components\TextInput::make('Pie'),
                        Forms\Components\TextInput::make('TasaInteres'),
                        Forms\Components\TextInput::make('GastosOperacionales'),
                        Forms\Components\TextInput::make('AdicionalesTotal'),
                        Forms\Components\TextInput::make('ValorCuota'),
                        Forms\Components\TextInput::make('ConcatID'),
                        Forms\Components\TextInput::make('Agendado'),
                        Forms\Components\TextInput::make('LeadID'),
                        Forms\Components\TextInput::make('SolicitudCredito'),
                        Forms\Components\TextInput::make('FechaSolicitudCredito'),
                        Forms\Components\TextInput::make('SolCreditoIDExterno'),
                        Forms\Components\TextInput::make('FinancieraID'),
                        Forms\Components\TextInput::make('RenovacionID'),
                        Forms\Components\TextInput::make('Venta'),
                        Forms\Components\TextInput::make('ForumTanner'),
                        Forms\Components\TextInput::make('CotExterna'),
                        Forms\Components\TextInput::make('VentaID'),
                        Forms\Components\TextInput::make('ConcatAnterior'),
                    ])->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('FechaCotizacion'),
                Tables\Columns\TextColumn::make('sucursal.Sucursal'),
                Tables\Columns\TextColumn::make('vendedor.Nombre'),
                Tables\Columns\TextColumn::make('cliente.Nombre'),
                Tables\Columns\TextColumn::make('estado.Estado'),
                Tables\Columns\TextColumn::make('modelo.Nombre'),
                Tables\Columns\TextColumn::make('ConcatID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SolicitudCredito'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->relationship('estado', 'Estado'),
                Tables\Filters\SelectFilter::make('sucursal')
                    ->relationship('sucursal', 'Sucursal'),
                Tables\Filters\SelectFilter::make('vendedor')
                    ->relationship('vendedor', 'Nombre')
                ->searchable(),

            ])
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn() => !auth()->user()->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->disabled(fn() => !auth()->user()->isAdmin()),
                ]),
            ])
            ->defaultSort('ID', 'desc');
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
            'index' => Pages\ListVTCotizaciones::route('/'),
            'create' => Pages\CreateVTCotizaciones::route('/create'),
            'edit' => Pages\EditVTCotizaciones::route('/{record}/edit'),
        ];
    }
}
