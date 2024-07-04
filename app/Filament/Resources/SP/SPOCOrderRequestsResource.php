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
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Fecha Creacion')
                            ->disabled(),
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
                        Forms\Components\TextInput::make('section_id'),
                    ])->columns(2),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Aprobadores Solicitud de Compra")
                        ->schema([
                            Forms\Components\Repeater::make('NivelesSolicitud')
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
                                    Forms\Components\Toggle::make('state')
                                        ->label('Aprobado')
                                        ->inline(false)
                                ])
                                ->deletable(false)
                                ->addable(false)
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get): array {
                                    $data['branchOffice_id'] = $get('ID');
                                    $data['min'] = 2 * ($data['level'] - 1);
                                    $data['max'] = 2 * ($data['level'] - 1) + 1;

                                    return $data;
                                })
//                            ->maxItems(10)
                                ->columns(3),
                        ]),
                ])->columnSpan(2),
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
                    ->url(fn($record) => ($record->ordenCompra) ? (OCPurchaseOrdersResource::getNavigationUrl() . '/' . $record->ordenCompra->order_id) : null, true),
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
