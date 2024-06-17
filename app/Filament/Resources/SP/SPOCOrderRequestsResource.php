<?php

namespace App\Filament\Resources\SP;

use App\Filament\Resources\OC\OCPurchaseOrdersResource;
use App\Filament\Resources\SP\SPOCOrderRequestsResource\Pages;
use App\Filament\Resources\SP\SPOCOrderRequestsResource\RelationManagers;
use App\Models\OC\OC_purchase_orders;
use App\Models\SP\SP_oc_order_requests;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SPOCOrderRequestsResource extends Resource
{
    protected static ?string $model = SP_oc_order_requests::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Ordenes de Compra';
    protected static ?string $modelLabel = 'Solicitudes de compra';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('business_id')
                    ->label('Negocio')
                    ->relationship('empresa', 'Empresa')
                    ->required(),
                Forms\Components\Select::make('brand_id')
                    ->label('Marca')
                    ->relationship('marca', 'Marca')
                    ->required(),
                Forms\Components\Select::make('branch_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'Sucursal')
                    ->required(),
                Forms\Components\TextInput::make('typeOfBranch_id')
                    ->required(),
                Forms\Components\Select::make('buyers_id')
                    ->label('Buyers')
                    ->relationship('comprador', 'Nombre')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('empresa.Empresa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('marca.Marca')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sucursal.Sucursal')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('typeOfBranch_id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comprador.Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section_id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ordenCompra.order_id')
                    ->searchable()
                    ->url(fn ($record) => ($record->ordenCompra) ? (OCPurchaseOrdersResource::getNavigationUrl(). '/' . $record->ordenCompra->order_id) : null , true),
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
            RelationManagers\DetalleOrdenCompraRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSPOCOrderRequests::route('/'),
            'create' => Pages\CreateSPOCOrderRequests::route('/create'),
            'edit' => Pages\EditSPOCOrderRequests::route('/{record}/edit'),
        ];
    }
}
