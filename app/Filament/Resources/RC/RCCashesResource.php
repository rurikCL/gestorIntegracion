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
                        Forms\Components\Select::make('comment')
                            ->relationship('usuarios', 'Nombre')
                            ->label('Solicitante'),
                        Forms\Components\Select::make('comment')
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
                    ])->columns(2),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Aprobadores Caja Chica")
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
                                            '1' => 'Pendiente',
                                            '0' => 'Aprobado',
                                        ])
                                        ->colors([
                                            '1' => 'warning',
                                            '0' => 'success',
                                        ])
                                        ->icons([
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
                ])->columnSpan(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('usuarios.Nombre')->searchable(),
                Tables\Columns\TextColumn::make('sucursales.Sucursal')->searchable(),
                Tables\Columns\TextColumn::make('comment')->label('Comentario'),
                Tables\Columns\TextColumn::make('total'),
                Tables\Columns\TextColumn::make('status'),
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
