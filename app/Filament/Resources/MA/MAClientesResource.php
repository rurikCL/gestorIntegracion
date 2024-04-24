<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAClientesResource\Pages;
use App\Filament\Resources\MA\MAClientesResource\RelationManagers;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MAClientes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MAClientesResource extends Resource
{
    protected static ?string $model = MA_Clientes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralLabel = 'Clientes';




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Nombre'),
                Forms\Components\TextInput::make('Rut'),
                Forms\Components\TextInput::make('Email'),
                Forms\Components\TextInput::make('Telefono'),
                Forms\Components\TextInput::make('Direccion'),
                Forms\Components\Select::make('ComunaID')
                ->options(fn() => \App\Models\MA\MA_Comunas::pluck('Comuna', 'ID'))->searchable(),
                Forms\Components\Select::make('RegionID')
                ->options(fn() => \App\Models\MA\MA_Regiones::pluck('Region', 'ID'))->searchable(),
                Forms\Components\DatePicker::make('FechaNacimiento'),
                Forms\Components\Select::make('Sexo')
                ->options([
                    'Masculino' => 'Masculino',
                    'Femenino' => 'Femenino',
                ]),
                Forms\Components\TextInput::make('id_usuario'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            /*->modifyQueryUsing(function (Builder $query) {
                $query->withCount(['ventas' =>function (Builder $query) {
                    $query->where('EstadoVentaID', 4);
                }]);
            })*/
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Nombre')->searchable()
                ->description(fn(MA_Clientes $record) => $record->Direccion),
                Tables\Columns\TextColumn::make('Rut')->searchable(),
                Tables\Columns\TextColumn::make('Email')->searchable(),
                Tables\Columns\TextColumn::make('Telefono')->searchable(),
//                Tables\Columns\BadgeColumn::make('ventas_count')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make()
                    ->disabled(!Auth::user()->isRole(['admin', 'marketing'])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->disabled(!Auth::user()->isRole(['admin'])),
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
            'index' => Pages\ListMAClientes::route('/'),
            'create' => Pages\CreateMAClientes::route('/create'),
            'edit' => Pages\EditMAClientes::route('/{record}/edit'),
        ];
    }
}
