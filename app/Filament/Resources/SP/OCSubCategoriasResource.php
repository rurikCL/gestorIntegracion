<?php

namespace App\Filament\Resources\SP;

use App\Filament\Resources\SP\OCSubCategoriasResource\Pages;
use App\Filament\Resources\SP\OCSubCategoriasResource\RelationManagers;
use App\Models\SP\OCSubCategorias;
use App\Models\SP\SP_oc_categories;
use App\Models\SP\SP_oc_sub_categories;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OCSubCategoriasResource extends Resource
{
    protected static ?string $model = SP_oc_sub_categories::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Ordenes de Compra';
    protected static ?string $modelLabel = 'Sub Categorias';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('sku')->disabled(),
                Forms\Components\Select::make('ocCategory_id')
                    ->relationship('oc_category', 'name')
                    ->label('Categoría')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('oc_category.name')->label('Categoría'),
                Tables\Columns\ToggleColumn::make('active')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                ->relationship('oc_category', 'name')
                ->label('Categoría'),
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
            'oc_products' => RelationManagers\OcProductosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOCSubCategorias::route('/'),
            'create' => Pages\CreateOCSubCategorias::route('/create'),
            'edit' => Pages\EditOCSubCategorias::route('/{record}/edit'),
        ];
    }
}
