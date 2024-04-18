<?php

namespace App\Filament\Resources\OC;

use App\Filament\Resources\OC\OCPurchaseOrdersResource\Pages;
use App\Filament\Resources\OC\OCPurchaseOrdersResource\RelationManagers;
use App\Models\OC\OC_purchase_orders;
use App\Models\OC\OCPurchaseOrders;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OCPurchaseOrdersResource extends Resource
{
    protected static ?string $model = OC_purchase_orders::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Ordenes de Compra';
    protected static ?string $modelLabel = 'Orden de Compra';
    protected static ?string $navigationLabel = 'Ordenes de Compra';
    protected static ?string $pluralLabel = 'Ordenes de Compra';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('empresa.Empresa'),
                Tables\Columns\TextColumn::make('gerencia.Gerencia'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListOCPurchaseOrders::route('/'),
            'create' => Pages\CreateOCPurchaseOrders::route('/create'),
            'edit' => Pages\EditOCPurchaseOrders::route('/{record}/edit'),
        ];
    }
}
