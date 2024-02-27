<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Livewire\Livewire;

class SucursalesRelationManager extends RelationManager
{
    protected static string $relationship = 'sucursales';

    protected static ?string $recordTitleAttribute = 'Sucursales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('SucursalID')
                    ->options(fn() => \App\Models\MA\MA_Sucursales::all()->pluck('Sucursal', 'ID'))
                    ->required(),
                Forms\Components\Select::make('CargoID')
                    ->options(fn() => \App\Models\MA\MA_Cargos::all()->pluck('Cargo', 'ID'))
                    ->required(),
                Forms\Components\Toggle::make('DisponibleLead'),
                Forms\Components\Toggle::make('Activo')->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sucursal.Sucursal'),
                Tables\Columns\TextColumn::make('cargo.Cargo'),
                Tables\Columns\ToggleColumn::make('DisponibleLead'),
                Tables\Columns\ToggleColumn::make('Activo'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
