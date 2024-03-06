<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAUsuariosResource\Pages;
use App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;
use App\Models\MA\MA_Usuarios;
use App\Models\MA\MAUsuarios;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MAUsuariosResource extends Resource
{
    protected static ?string $model = MA_Usuarios::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $navigationLabel = 'Usuarios Roma';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Nombre'),
                Forms\Components\TextInput::make('Rut'),
                Forms\Components\TextInput::make('Email'),
                Forms\Components\TextInput::make('Celular'),
                Forms\Components\Select::make('PerfilID')
                    ->relationship('perfil', 'Perfil'),
                Forms\Components\Select::make('CargoID')->name('cargoUsuario')
                    ->options(fn () => \App\Models\MA\MA_Cargos::all()->pluck('Cargo', 'ID')),
                Forms\Components\Toggle::make('Disponible'),
                Forms\Components\Toggle::make('Activo'),
                Forms\Components\TextInput::make('Clave')
                    ->password(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')->searchable(),
                Tables\Columns\TextColumn::make('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('Rut')->searchable(),
                Tables\Columns\TextColumn::make('Email')->searchable(),
                Tables\Columns\TextColumn::make('Celular'),
                Tables\Columns\TextColumn::make('perfil.Perfil')->label('Perfil'),
                Tables\Columns\TextColumn::make('cargo.Cargo')->label('Cargo'),
                Tables\Columns\BooleanColumn::make('Disponible'),
                Tables\Columns\BooleanColumn::make('Activo'),
            ])
            ->filters([
                Tables\Filters\Filter::make('Activo'),
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
            'sucursales' => RelationManagers\SucursalesRelationManager::class,
            'agente' => RelationManagers\AgenteRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMAUsuarios::route('/'),
            'create' => Pages\CreateMAUsuarios::route('/create'),
            'edit' => Pages\EditMAUsuarios::route('/{record}/edit'),
        ];
    }
}
