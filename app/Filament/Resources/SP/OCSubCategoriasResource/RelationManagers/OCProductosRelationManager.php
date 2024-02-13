<?php

namespace App\Filament\Resources\SP\OCSubCategoriasResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OCProductosRelationManager extends RelationManager
{
    protected static string $relationship = 'OCProductos';

    protected static ?string $recordTitleAttribute = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('sku')->disabled(),
                Forms\Components\Select::make('costCenter_id')
                    ->relationship('sucursal', 'Sucursal')
                    ->label('Sucursal / Centro Costo'),
                Forms\Components\Select::make('measure_id')
                    ->relationship('unidadMedida', 'name')
                    ->label('Unidad de Medida'),
                Forms\Components\Select::make('currency_id')
                    ->relationship('moneda', 'name')
                    ->label('Moneda'),
                Forms\Components\Toggle::make('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('sku'),
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
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
