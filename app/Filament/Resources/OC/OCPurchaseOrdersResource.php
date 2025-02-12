<?php

namespace App\Filament\Resources\OC;

use App\Filament\Resources\OC\OCPurchaseOrdersResource\Pages;
use App\Filament\Resources\OC\OCPurchaseOrdersResource\RelationManagers;
use App\Models\MA\MA_Sucursales;
use App\Models\OC\OC_purchase_orders;
use App\Models\OC\OCPurchaseOrders;
use App\Models\OC_DetalleOrdenCompra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SendGrid\Mail\Section;

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
                Forms\Components\Section::make('')->schema([
                    Forms\Components\TextInput::make('id')
                        ->label('ID')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Fecha Creacion'),
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
                    Forms\Components\Select::make('provider')
                        ->relationship('proveedor', 'name')
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('buyers_id')
                        ->label('Buyers')
                        ->relationship('comprador', 'Nombre')
                        ->required(),
                    Forms\Components\Select::make('contact_id')
                        ->label('Contacto')
                        ->relationship('contacto', 'Nombre'),
                    Forms\Components\Select::make('state')
                        ->label('Estado')
                        ->options([
                            '1' => 'Pendiente',
                            '2' => 'Aprobado',
                            '3' => 'Rechazado',
                            '4' => 'En Asignacion Precio',
                            '5' => 'En Orden Compra',
                            '6' => 'Anulado',
                        ])->required(),
                ])->columns(2),


                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Articulos')
                            ->schema([
                                Forms\Components\Repeater::make('ArticulosOrdenCompra')
                                    ->relationship('articulos')
                                    ->label(false)
                                    ->schema([
                                        Forms\Components\Select::make('ocCategory_id')
                                            ->relationship('categoria', 'name')
                                            ->columnSpan(3)
                                            ->label('Categoria'),
                                        Forms\Components\Select::make('ocSubCategory_id')
                                            ->relationship('subcategoria', 'name')
                                            ->columnSpan(3)
                                            ->label('Sub Categoria'),
                                        Forms\Components\Select::make('ocProduct_id')
                                            ->relationship('producto', 'name')
                                            ->columnSpan(3)
                                            ->label('Producto'),
                                        Forms\Components\TextInput::make('description')->label('Descripcion')
                                            ->columnSpan(3),
                                        Forms\Components\Select::make('branch_id')
                                            ->options(fn() => MA_Sucursales::where('Activa', 1)->pluck('Sucursa', 'ID')),
                                        Forms\Components\TextInput::make('amount')->label('Monto')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $set('totalPrice', $state * $get('unitPrice'));
                                            }),
                                        Forms\Components\TextInput::make('unitPrice')
                                            ->label('Precio')
                                            ->reactive(),
                                        Forms\Components\TextInput::make('totalPrice')
                                            ->label('Total')
                                            ->reactive(),
                                        Forms\Components\TextInput::make('taxAmount')
                                            ->label('Impuesto'),
                                    ])
                                    ->columns(3)
                                    ->itemLabel(fn($record) => $record["id"])
                                    ->grid(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Aprobadores')
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
                                            ])
                                            ->label('Nivel'),
                                        Forms\Components\Select::make('approver_id')
                                            ->relationship('usuarios', 'Nombre')
                                            ->label('Aprobador')
                                            ->searchable(),
                                        Forms\Components\ToggleButtons::make('state')
                                            ->label('Estado')
                                            ->options([
                                                '2' => 'Espera',
                                                '1' => 'Pendiente',
                                                '0' => 'Aprobado',
                                            ])
                                            ->colors([
                                                '2' => 'info',
                                                '1' => 'warning',
                                                '0' => 'success',
                                            ])
                                            ->icons([
                                                '2' => 'heroicon-o-bolt',
                                                '1' => 'heroicon-o-clock',
                                                '0' => 'heroicon-o-check-circle',
                                            ])
                                            ->inline()
                                            ->grouped()
                                    ])
                                    ->deletable(auth()->user()->isAdmin())
                                    ->addable(auth()->user()->isAdmin())
                                    ->columns(3),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
//                Tables\Columns\ViewColumn::make('state')
//                    ->view('components.state'),
                Tables\Columns\ToggleColumn::make('state'),
                Tables\Columns\TextColumn::make('empresa.Empresa'),
                Tables\Columns\TextColumn::make('gerencia.Gerencia'),
                Tables\Columns\TextColumn::make('sucursal.Sucursal')
                    ->searchable()
                    ->description(fn($record) => $record->tipoSucursal->TipoSucursal ?? ''),
                Tables\Columns\TextColumn::make('comprador.Nombre')->searchable(),
                Tables\Columns\TextColumn::make('contacto.Nombre')->searchable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->options(fn() => MA_Sucursales::where('Activa', 1)->pluck('Sucursal', 'id'))
                    ->searchable()
                    ->label('Sucursal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
