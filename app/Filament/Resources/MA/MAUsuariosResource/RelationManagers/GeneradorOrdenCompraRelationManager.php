<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class GeneradorOrdenCompraRelationManager extends RelationManager
{
    protected static string $relationship = 'generadorOrdenCompra';

    protected static ?string $recordTitleAttribute = 'GeneradorOC';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branchOffice_id')
                    ->relationship('branch', 'Sucursal')
                    ->searchable()
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.Nombre'),
                Tables\Columns\TextColumn::make('branch.Sucursal'),
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
