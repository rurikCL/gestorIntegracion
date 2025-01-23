<?php

namespace App\Filament\Resources\RC;

use App\Filament\Resources\RC\RCCashesResource\Pages;
use App\Filament\Resources\RC\RCCashesResource\RelationManagers;
use App\Models\RC\RC_cashes;
use App\Models\RC\RC_cashier_approvals;
use App\Models\RC\RCCashes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RCCashesResource extends Resource
{
    protected static ?string $model = RC_cashes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Caja Chica';
    protected static ?string $modelLabel = 'Solicitud Caja Chica';
    protected static ?string $navigationLabel = 'Solicitudes Caja Chica';
    protected static ?string $pluralLabel = 'Solicitudes Caja Chica';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('usuarios', 'Nombre')
                            ->searchable()
                            ->label('Solicitante'),
                        Forms\Components\Select::make('branch_office_id')
                            ->relationship('sucursales', 'Sucursal')
                            ->label('Sucursal'),
                        Forms\Components\TextInput::make('comment')
                            ->label('Comentario'),
                        Forms\Components\Select::make('status')
                            ->options([
                                '1' => 'Pendiente',
                                '2' => 'Aprobado',
                                '3' => 'Rechazado',
                                '4' => 'En Asignacion Precio',
                                '5' => 'En Orden Compra',
                                '6' => 'Anulado',
                            ])->required()
                            ->label('Estado'),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->label('Total'),
                    ])->columns(2),
//                Forms\Components\Section::make('')
//                    ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Articulos')
                            ->schema([
                                Forms\Components\Repeater::make('ArticulosCajaChica')
                                    ->relationship('articulos')
                                    ->label(false)
                                    ->schema([
                                        Forms\Components\TextInput::make('number_document')->label('Numero Documento'),
                                        Forms\Components\DatePicker::make('date')->label('Fecha'),
                                        Forms\Components\ToggleButtons::make('type_document')->label('Tipo Documento')
                                            ->options([
                                                1 => 'Boleta',
                                                2 => 'Factura',
                                                3 => 'Boleta Honorarios'
                                            ])->inline()->grouped(),
                                        Forms\Components\TextInput::make('provider')->label('Proveedor'),
                                        Forms\Components\TextInput::make('description')->label('Descripcion'),
                                        Forms\Components\Select::make('account_id')
                                            ->relationship('account', 'name')
                                            ->label('Cuenta Contable'),
                                        Forms\Components\TextInput::make('total')->numeric()->minValue(1)
                                            ->label('Total'),
                                        Forms\Components\ToggleButtons::make('state')
                                            ->options([
                                                1 => 'Aprobado',
                                                0 => 'Anulado'
                                            ])
                                            ->colors([
                                                0 => 'warning',
                                                1 => 'success',
                                            ])->inline()->grouped()
                                            ->label('Estado'),
                                    ])
                                    ->itemLabel(fn($state)=>$state["id"])
                                    ->grid(3),

                            ]),
                        Forms\Components\Tabs\Tab::make('Aprobadores')
                            ->schema([
                                Forms\Components\Repeater::make('NivelesCajaChica')
                                    ->relationship('aprobadores')
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
                                        Forms\Components\Select::make('cashier_approver_id')
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
                                            ->default(1)
                                            ->inline()
                                            ->grouped(),
                                    ])
                                    ->deletable(auth()->user()->isAdmin())
                                    ->addable(auth()->user()->isAdmin())
                                    ->columns(3),
                            ]),
                    ])->columnSpanFull(),
//                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable(),
                Tables\Columns\TextColumn::make('usuarios.Nombre')
                    ->label('Solicitante')->searchable(),
                Tables\Columns\TextColumn::make('sucursales.Sucursal')
                    ->label('Sucursal')->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('')
                    ->prefix('$')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                Tables\Columns\ViewColumn::make('status')
                    ->view('components.stateRCcash') // 1: pendiente, 2: proceso, 3: finalizado
                    ->label('Estado'),
//                Tables\Columns\TextColumn::make('comment')->label('Comentario'),

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
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRCCashes::route('/'),
            'create' => Pages\CreateRCCashes::route('/create'),
            'edit' => Pages\EditRCCashes::route('/{record}/edit'),
        ];
    }
}
