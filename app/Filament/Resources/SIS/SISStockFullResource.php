<?php

namespace App\Filament\Resources\SIS;

use App\Filament\Resources\SIS\SISStockFullResource\Pages;
use App\Filament\Resources\SIS\SISStockFullResource\RelationManagers;
use App\Models\SIS\SIS_StockFull;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SISStockFullResource extends Resource
{
    protected static ?string $model = SIS_StockFull::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Stock';
    protected static ?string $navigationGroup = 'Administracion';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Empresa'),
                Forms\Components\TextInput::make('Sucursal'),
                Forms\Components\TextInput::make('FolioVenta'),
                Forms\Components\TextInput::make('Venta'),
                Forms\Components\TextInput::make('EstadoVenta'),
                Forms\Components\TextInput::make('FechaVenta'),
                Forms\Components\TextInput::make('TipoDocumento'),
                Forms\Components\TextInput::make('Vendedor'),
                Forms\Components\TextInput::make('FechaIngreso'),
                Forms\Components\TextInput::make('FechaFacturacion'),
                Forms\Components\TextInput::make('VIN'),
                Forms\Components\TextInput::make('Marca'),
                Forms\Components\TextInput::make('Modelo'),
                Forms\Components\TextInput::make('Version'),
                Forms\Components\TextInput::make('CodigoVersion'),
                Forms\Components\TextInput::make('Anno'),
                Forms\Components\TextInput::make('Kilometraje'),
                Forms\Components\TextInput::make('CodigoInterno'),
                Forms\Components\TextInput::make('PlacaPatente'),
                Forms\Components\TextInput::make('CondicionVehiculo'),
                Forms\Components\TextInput::make('ColorExterior'),
                Forms\Components\TextInput::make('ColorInterior'),
                Forms\Components\TextInput::make('PrecioVenta'),
                Forms\Components\TextInput::make('EstadoAutoPro'),
                Forms\Components\TextInput::make('DiasStock'),
                Forms\Components\TextInput::make('EstadoDealer'),
                Forms\Components\TextInput::make('Bodega'),
                Forms\Components\TextInput::make('Equipamiento'),
                Forms\Components\TextInput::make('NumeroMotor'),
                Forms\Components\TextInput::make('NumeroChasis'),
                Forms\Components\TextInput::make('Proveedor'),
                Forms\Components\TextInput::make('FechaDisponibilidad'),
                Forms\Components\TextInput::make('FacturaCompra'),
                Forms\Components\TextInput::make('VencimientoDocumento'),
                Forms\Components\TextInput::make('FechaCompra'),
                Forms\Components\TextInput::make('FechaVctoRevisionTecnica'),
                Forms\Components\TextInput::make('NPropietarios'),
                Forms\Components\TextInput::make('FolioRetoma'),
                Forms\Components\TextInput::make('FechaRetoma'),
                Forms\Components\TextInput::make('DiasReservado'),
                Forms\Components\TextInput::make('PrecioCompra'),
                Forms\Components\TextInput::make('Gasto'),
                Forms\Components\TextInput::make('Accesorios'),
                Forms\Components\TextInput::make('TotalCosto'),
                Forms\Components\TextInput::make('PrecioLista'),
                Forms\Components\TextInput::make('Margen'),
                Forms\Components\TextInput::make('Z'),
                Forms\Components\TextInput::make('DisponibleENissan'),
                Forms\Components\TextInput::make('UnidadEspecial'),
                Forms\Components\TextInput::make('BonoFinanciamiento'),
                Forms\Components\TextInput::make('BonoMarca'),
                Forms\Components\TextInput::make('BonoAdicional'),
                Forms\Components\TextInput::make('DisponibleUsados'),
                Forms\Components\TextInput::make('Descuento'),
                Forms\Components\TextInput::make('MarcaID'),
                Forms\Components\TextInput::make('ModeloID'),
                Forms\Components\TextInput::make('VersionID'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
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
            'index' => Pages\ListSISStockFulls::route('/'),
            'create' => Pages\CreateSISStockFull::route('/create'),
            'edit' => Pages\EditSISStockFull::route('/{record}/edit'),
        ];
    }
}
