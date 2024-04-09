<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GeneradorOrdenCompraRelationManager extends RelationManager
{
    protected static string $relationship = 'generadorOrdenCompra';

    protected static ?string $recordTitleAttribute = 'GeneradorOC';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('BranchOfficeID')
                    ->required()
                    ->relationship('branch', 'Sucursal')
                ->searchable(),

            ]);
    }

    public static function table(Table $table): Table
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
