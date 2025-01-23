<?php

namespace App\Filament\Resources\VT;

use App\Filament\Resources\VT\VTVentasGastosVehiculoResource\Pages;
use App\Filament\Resources\VT\VTVentasGastosVehiculoResource\RelationManagers;
use App\Models\VT\VT_VentasGastosVehiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SendGrid\Mail\Section;

class VTVentasGastosVehiculoResource extends Resource
{
    protected static ?string $model = VT_VentasGastosVehiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Gastos Vehiculos';
    protected static ?string $navigationGroup = 'Administracion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('Fecha'),
                        Forms\Components\Select::make('Gerencia')
                            ->relationship('gerenciasoc', 'Gerencia')
                            ->label('Gerencia')
                        ,
                        Forms\Components\TextInput::make('Combustible')->numeric(),
                        Forms\Components\TextInput::make('Logistica')->numeric(),
                        Forms\Components\TextInput::make('PreparacionUsados')->numeric(),
                        Forms\Components\TextInput::make('Multas')->numeric(),
                        Forms\Components\TextInput::make('GarantiaUsados')->numeric(),
                        Forms\Components\TextInput::make('SetYPisos')->numeric(),

                        Forms\Components\TextInput::make('GastosMiscelaneos')->numeric(),
                        Forms\Components\TextInput::make('RevisionesPreCompra')->numeric(),
                        Forms\Components\TextInput::make('Reparacion')->numeric(),
                        Forms\Components\TextInput::make('Log_Acopio')->numeric(),
                        Forms\Components\TextInput::make('Log_Traslados')->numeric(),
                        Forms\Components\TextInput::make('Log_Preparacion')->numeric(),
                        Forms\Components\TextInput::make('Log_AdmFlorplan')->numeric(),
                        Forms\Components\TextInput::make('NuevosGastos')->numeric(),
                        Forms\Components\TextInput::make('MarcaFlota')->numeric(),
                    ])->columns()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Fecha'),
                Tables\Columns\TextColumn::make('gerenciasoc.Gerencia'),
                Tables\Columns\TextColumn::make('Combustible'),
                Tables\Columns\TextColumn::make('Logistica'),
                Tables\Columns\TextColumn::make('PreparacionUsados'),
                Tables\Columns\TextColumn::make('SetYPisos'),
                Tables\Columns\TextColumn::make('GastosMiscelaneos'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListVTVentasGastosVehiculos::route('/'),
            'create' => Pages\CreateVTVentasGastosVehiculo::route('/create'),
            'edit' => Pages\EditVTVentasGastosVehiculo::route('/{record}/edit'),
        ];
    }
}
