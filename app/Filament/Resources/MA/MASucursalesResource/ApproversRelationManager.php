<?php

namespace App\Filament\Resources\MA\MASucursalesResource;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ApproversRelationManager extends RelationManager
{
    protected static string $relationship = 'aprobadores';

    protected static ?string $recordTitleAttribute = 'Aprobadores';
    protected static ?string $inverseRelationship = 'aprobadores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_office_id')
                    ->relationship('sucursales', 'Sucursal'),
                Forms\Components\Select::make('user_id')
                    ->relationship('usuarios', 'Nombre')
                    ->searchable()->columnSpan(2),
                Forms\Components\Select::make('level')
                    ->options([
                        1 => 'Nivel 1',
                        2 => 'Nivel 2',
                        3 => 'Nivel 3',
                        4 => 'Nivel 4',
                        5 => 'Nivel 5',
                        6 => 'Nivel 6',
                        7 => 'Nivel 7',
                        8 => 'Nivel 8',
                        9 => 'Nivel 9',
                        10 => 'Nivel 10',
                    ])->default(1),
                Forms\Components\TextInput::make('min'),
                Forms\Components\TextInput::make('max'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
//                Tables\Columns\TextColumn::make('sucursales.Sucursal', 'Sucursal'),
                Tables\Columns\TextColumn::make('level', 'Nivel'),
                Tables\Columns\TextColumn::make('usuarios.Nombre', 'Nombre'),
                Tables\Columns\TextColumn::make('min', 'Minimo'),
                Tables\Columns\TextColumn::make('max', 'Maximo'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
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
