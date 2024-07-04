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
                Forms\Components\Select::make('buyers_id')
                    ->label('Buyers')
                    ->relationship('comprador', 'Nombre')
                    ->required(),
                Forms\Components\Select::make('contact_id')
                    ->label('Contacto')
                    ->relationship('contacto', 'Nombre')
                    ->required(),
                Forms\Components\Select::make('state')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Proceso' => 'En Proceso',
                        'Finalizado' => 'Finalizado'
                    ])->required(),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Aprobadores Ordenes de Compra")
                        ->schema([
                            Forms\Components\Repeater::make('NivelesOrdenesCompra')
                                ->relationship('approvals')
                                ->label(false)
                                ->schema([
                                    Forms\Components\Select::make('level')
                                        ->options([
                                            1 => 'Nivel 1',
                                            2 => 'Nivel 2',
                                            3 => 'Nivel 3',
                                            4 => 'Nivel 4',
                                            5 => 'Nivel 5',
                                        ]),
                                    Forms\Components\Select::make('user_id')
                                        ->relationship('usuarios', 'Nombre')
//                                    ->searchable(),
//                                Forms\Components\TextInput::make('min'),
//                                Forms\Components\TextInput::make('max'),
                                ])
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get): array {
                                    $data['branchOffice_id'] = $get('ID');
                                    $data['min'] = 2 * ($data['level'] - 1);
                                    $data['max'] = 2 * ($data['level'] - 1) + 1;

                                    return $data;
                                })
//                            ->maxItems(10)
                                ->columns(2),
                        ]),
                ])->columnSpan(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('empresa.Empresa'),
                Tables\Columns\TextColumn::make('gerencia.Gerencia'),
                Tables\Columns\TextColumn::make('sucursal.Sucursal')
                ->description(fn($record) => $record->tipoSucursal->TipoSucursal ?? ''),
                Tables\Columns\TextColumn::make('comprador.Nombre'),
                Tables\Columns\TextColumn::make('contacto.Nombre'),
                Tables\Columns\ViewColumn::make('state')->view('components.state'),

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
            'view' => Pages\ViewOCPurchaseOrders::route('/{record}'),
        ];
    }
}
