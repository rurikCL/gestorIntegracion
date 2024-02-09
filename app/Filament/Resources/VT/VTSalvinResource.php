<?php

namespace App\Filament\Resources\VT;

use App\Filament\Resources\VT\VTSalvinResource\Pages;
use App\Filament\Resources\VT\VTSalvinResource\RelationManagers;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MA_Usuarios;
use App\Models\VT\VT_Salvin;
use App\Models\VT\VTSalvin;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SendGrid\Mail\Section;

class VTSalvinResource extends Resource
{
    protected static ?string $model = VT_Salvin::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $modelLabel = 'Salvin';
    protected static ?string $navigationGroup = 'Administracion';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Datos de la Venta')
                            ->schema([
                                Forms\Components\TextInput::make('Estado'),
                                Forms\Components\TextInput::make('FechaVenta'),
                                Forms\Components\TextInput::make('FechaFactura'),
                                Forms\Components\TextInput::make('Timestamp'),

                                Forms\Components\TextInput::make('Tipo'),
                                Forms\Components\TextInput::make('Saldo'),
                                Forms\Components\Textarea::make('Comentario')->columnSpan(2),
                                Forms\Components\TextInput::make('FechaEstimado'),
                                Forms\Components\TextInput::make('TipoEstimado'),
                                Forms\Components\TextInput::make('Tramo'),
                                Forms\Components\TextInput::make('SaldosVigentes'),
                                Forms\Components\TextInput::make('FechaActualizacion'),
                                Forms\Components\TextInput::make('FechaFacturaEst'),
                                Forms\Components\TextInput::make('Financiera'),
                                Forms\Components\TextInput::make('TipoVenta'),
                            ])->columns(2),
                    ])->columns(2),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Datos del Vehiculo')
                        ->schema([
                            Forms\Components\Select::make('Marca')
                                ->options(MA_Marcas::all()->pluck('Marca', 'ID')->toArray()),
                            Forms\Components\TextInput::make('Modelo'),
                            Forms\Components\TextInput::make('Cajon'),
                            Forms\Components\Select::make('Sucursal')
                            ->options(fn () => MA_Sucursales::all()->pluck('Sucursal', 'ID')->toArray())->searchable(),
                        ])->columns(2),
                    Forms\Components\Section::make('Datos del Cliente')
                        ->schema([
                            Forms\Components\TextInput::make('Cliente'),
                            Forms\Components\TextInput::make('ClienteRut'),
                            Forms\Components\TextInput::make('Telefono'),
                        ]),
                    Forms\Components\Section::make('Datos Vendedor')
                        ->schema([
                            Forms\Components\Select::make('Vendedor')
                            ->options(fn () => MA_Usuarios::all()->pluck('Nombre', 'ID')->toArray())->searchable(),
                            Forms\Components\Select::make('JefeSucursal')
                            ->options(fn () => MA_Usuarios::jefeSucursal()->pluck('Nombre', 'ID')->toArray())->searchable(),
                        ])
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('marcav.Marca')
                    ->label('Marca'),
                Tables\Columns\TextColumn::make('Modelo')
                    ->description(fn(VT_Salvin $record) => mb_strimwidth($record->Comentario, 0, 60, "..."))
                    ->tooltip(fn(VT_Salvin $record) => $record->Comentario),
                Tables\Columns\TextColumn::make('Cajon')->color('warning')->searchable(),
                Tables\Columns\TextColumn::make('Estado'),
                Tables\Columns\TextColumn::make('Saldo')->money("CLP"),
                Tables\Columns\TextColumn::make('Tramo'),
                Tables\Columns\TextColumn::make('Financiera'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ComentariosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVTSalvins::route('/'),
            'create' => Pages\CreateVTSalvin::route('/create'),
            'edit' => Pages\EditVTSalvin::route('/{record}/edit'),
        ];
    }
}
