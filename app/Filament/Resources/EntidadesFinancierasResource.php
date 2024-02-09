<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntidadesFinancierasResource\Pages;
use App\Filament\Resources\EntidadesFinancierasResource\RelationManagers;
use App\Models\EntidadesFinancieras;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntidadesFinancierasResource extends Resource
{
    protected static ?string $model = EntidadesFinancieras::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Administracion';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('FechaCreacion'),
                Forms\Components\TextInput::make('EventoCreacionID'),
                Forms\Components\TextInput::make('UsuarioCreacionID'),
                Forms\Components\DateTimePicker::make('FechaActualizacion'),
                Forms\Components\TextInput::make('EventoActualizacionID'),
                Forms\Components\TextInput::make('UsuarioActualizacionID'),
                Forms\Components\TextInput::make('EntidadFinanciera')
                    ->maxLength(100),
                Forms\Components\Toggle::make('Activo'),
                Forms\Components\TextInput::make('Email')
                    ->maxLength(70),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('EntidadFinanciera'),
                Tables\Columns\TextColumn::make('Email'),
//                Tables\Columns\TextColumn::make('FechaCreacion')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('EventoCreacionID'),
//                Tables\Columns\TextColumn::make('UsuarioCreacionID'),
//                Tables\Columns\TextColumn::make('FechaActualizacion')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('EventoActualizacionID'),
//                Tables\Columns\TextColumn::make('UsuarioActualizacionID'),
                Tables\Columns\ToggleColumn::make('Activo'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEntidadesFinancieras::route('/'),
        ];
    }
}
