<?php

namespace App\Filament\Resources\SP;

use App\Filament\Resources\SP\OCProductosResource\Pages;
use App\Filament\Resources\SP\OCProductosResource\RelationManagers;
use App\Models\SP\OCProductos;
use App\Models\SP\SP_oc_products;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OCProductosResource extends Resource
{
    protected static ?string $model = SP_oc_products::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';    protected static ?string $navigationGroup = 'Ordenes de Compra';
    protected static ?string $modelLabel = 'Productos';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('sku')->disabled(),
                Forms\Components\Select::make('ocSubCategory_id')
                    ->relationship('subCategory', 'name')
                    ->label('Sub Categoría'),
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
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
//                    ->description(fn (SP_oc_products $record) => $record->sku)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')->searchable(),
                Tables\Columns\TextColumn::make('subCategory.name')->label('Sub Categoría'),
                Tables\Columns\TextColumn::make('sucursal.Sucursal')->label('Sucursal / Centro Costo'),
                Tables\Columns\ToggleColumn::make('active')->label('Activo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subCategory')
                    ->relationship('subCategory', 'name')
                    ->label('Sub Categoría'),
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
            'index' => Pages\ListOCProductos::route('/'),
            'create' => Pages\CreateOCProductos::route('/create'),
            'edit' => Pages\EditOCProductos::route('/{record}/edit'),
        ];
    }
}
