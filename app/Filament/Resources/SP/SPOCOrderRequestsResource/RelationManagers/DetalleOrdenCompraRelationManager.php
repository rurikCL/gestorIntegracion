<?php

namespace App\Filament\Resources\SP\SPOCOrderRequestsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetalleOrdenCompraRelationManager extends RelationManager
{
    protected static string $relationship = 'detalleOrdenCompra';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ocProduct_id')
                    ->relationship('productoOC', 'name')
                    ->required(),
                Forms\Components\Select::make('ocCategory_id')
                    ->relationship('categoriaOC', 'name')
                    ->required(),
                Forms\Components\Select::make('ocSubCategory_id')
                    ->relationship('subCategoriaOC', 'name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric(),
                Forms\Components\TextInput::make('unitPrice')
                    ->numeric(),
                Forms\Components\TextInput::make('totalPrice')
                    ->numeric(),
                Forms\Components\TextInput::make('description'),
                Forms\Components\TextInput::make('state'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Detalle Orden')
            ->columns([
                Tables\Columns\TextColumn::make('productoOC.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categoriaOC.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subCategoriaOC.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('unitPrice'),
                Tables\Columns\TextColumn::make('totalPrice'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('state'),
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
