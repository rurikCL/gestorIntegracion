<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAClientesResource\Pages;
use App\Filament\Resources\MA\MAClientesResource\RelationManagers;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MAClientes;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MAClientesResource extends Resource
{
    protected static ?string $model = MA_Clientes::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $navigationLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Nombre'),
                Forms\Components\TextInput::make('Rut'),
                Forms\Components\TextInput::make('Email'),
                Forms\Components\TextInput::make('Telefono'),
                Forms\Components\TextInput::make('Direccion'),
                Forms\Components\TextInput::make('Ciudad'),
                Forms\Components\TextInput::make('FechaNacimiento'),
                Forms\Components\TextInput::make('Sexo'),
                Forms\Components\TextInput::make('id_usuario'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Nombre'),
                Tables\Columns\TextColumn::make('Rut'),
                Tables\Columns\TextColumn::make('Email'),
                Tables\Columns\TextColumn::make('Telefono'),
                Tables\Columns\TextColumn::make('Direccion'),
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
            'index' => Pages\ListMAClientes::route('/'),
            'create' => Pages\CreateMAClientes::route('/create'),
            'edit' => Pages\EditMAClientes::route('/{record}/edit'),
        ];
    }
}
