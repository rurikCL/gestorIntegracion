<?php

namespace App\Filament\Resources\SIS;

use App\Filament\Resources\SIS\SISStockFullResource\Pages;
use App\Filament\Resources\SIS\SISStockFullResource\RelationManagers;
use App\Models\APC_Stock;
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
//    protected static ?string $model = SIS_StockFull::class;
    protected static ?string $model = APC_Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Stock APC';
    protected static ?string $pluralLabel = 'Stock APC';
    protected static ?string $navigationGroup = 'Administracion';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('Empresa'),
                    Forms\Components\TextInput::make('Sucursal'),
                    Forms\Components\TextInput::make('FolioVenta'),
                    Forms\Components\TextInput::make('Venta'),
                    Forms\Components\TextInput::make('EstadoVenta'),
                    Forms\Components\DateTimePicker::make('FechaVenta'),
                    Forms\Components\TextInput::make('Tipo_Documento'),
                    Forms\Components\TextInput::make('Vendedor'),
                    Forms\Components\DateTimePicker::make('Fecha_Ingreso'),
                    Forms\Components\DateTimePicker::make('Fecha_Facturacion'),
                    Forms\Components\TextInput::make('VIN'),
                    Forms\Components\TextInput::make('Marca'),
                    Forms\Components\TextInput::make('Modelo'),
                    Forms\Components\TextInput::make('Version'),
                    Forms\Components\TextInput::make('Codigo_Version'),
                    Forms\Components\TextInput::make('Anno'),
                    Forms\Components\TextInput::make('Kilometraje'),
                    Forms\Components\TextInput::make('Codigo_Interno'),
                    Forms\Components\TextInput::make('Placa_Patente'),
                    Forms\Components\TextInput::make('Condicion_Vehiculo'),
                    Forms\Components\TextInput::make('Color_Exterior'),
                    Forms\Components\TextInput::make('Color_Interior'),
                    Forms\Components\TextInput::make('Precio_Venta_Total'),
                    Forms\Components\TextInput::make('Estado_AutoPro'),
                    Forms\Components\TextInput::make('Dias_Stock'),
                    Forms\Components\TextInput::make('Estado_Dealer'),
                    Forms\Components\TextInput::make('Bodega'),
                    Forms\Components\TextInput::make('Equipamiento'),
                    Forms\Components\TextInput::make('Numero_Motor'),
                    Forms\Components\TextInput::make('Numero_Chasis'),
                    Forms\Components\TextInput::make('Proveedor'),
                    Forms\Components\DateTimePicker::make('Fecha_Disponibilidad'),
                    Forms\Components\TextInput::make('Factura_Compra'),
                    Forms\Components\DateTimePicker::make('Vencimiento_Documento'),
                    Forms\Components\DateTimePicker::make('Fecha_Compra'),
                    Forms\Components\DateTimePicker::make('Fecha_Vencto_Rev_tec'),
                    Forms\Components\TextInput::make('N_Propietarios'),
                    Forms\Components\TextInput::make('Folio_Retoma'),
                    Forms\Components\DateTimePicker::make('Fecha_Retoma'),
                    Forms\Components\TextInput::make('Dias_Reservado'),
                    Forms\Components\TextInput::make('Precio_Compra_Neto'),
                    Forms\Components\TextInput::make('Gasto'),
                    Forms\Components\TextInput::make('Accesorios'),
                    Forms\Components\TextInput::make('Total_Costo'),
                    Forms\Components\TextInput::make('Precio_Lista'),
//                    Forms\Components\TextInput::make('Margen'),
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('Codigo_Interno')->searchable(),
                Tables\Columns\TextColumn::make('VIN')->searchable(),
                Tables\Columns\TextColumn::make('Marca'),
                Tables\Columns\TextColumn::make('Modelo'),
                Tables\Columns\TextColumn::make('Bodega'),
                Tables\Columns\TextColumn::make('Sucursal'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Bodega')
                ->options(fn() => APC_Stock::where('Bodega', '<>', '')->groupBy('Bodega')->pluck('Bodega', 'Bodega')->toArray()),
//                ->options(fn() => SIS_StockFull::where('Bodega', '<>', '')->groupBy('Bodega')->pluck('Bodega', 'Bodega')->toArray()),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
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
//            'create' => Pages\CreateSISStockFull::route('/create'),
            'edit' => Pages\EditSISStockFull::route('/{record}/edit'),
        ];
    }
}
