<?php

namespace App\Filament\Resources\API\ApiProveedoresResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RespuestasTipoRelationManager extends RelationManager
{
    protected static string $relationship = 'respuestasTipo';

    protected static ?string $recordTitleAttribute = 'ApiID';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Descripcion')->columnSpan(2)->required(),
                Forms\Components\Select::make('Tipo')
                ->options([
                    'ERROR' => 'ERROR',
                    'EXITO' => 'EXITO',
                ])->default('ERROR')->disablePlaceholderSelection(),
                Forms\Components\TextInput::make('llave'),
                Forms\Components\TextInput::make('Mensaje'),
                Forms\Components\TextInput::make('Trigger'),
                Forms\Components\Toggle::make('Activo')->default(true),
                Forms\Components\Toggle::make('Reprocesa')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Tipo'),
                Tables\Columns\TextColumn::make('Descripcion'),
                Tables\Columns\TextColumn::make('llave'),
                Tables\Columns\TextColumn::make('Mensaje'),
                Tables\Columns\TextColumn::make('Trigger'),
                Tables\Columns\ToggleColumn::make('Activo'),
                Tables\Columns\ToggleColumn::make('Reprocesa'),
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
