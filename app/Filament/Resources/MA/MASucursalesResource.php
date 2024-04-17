<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MASucursalesResource\ApproversRelationManager;
use App\Filament\Resources\MA\MASucursalesResource\Pages;
use App\Filament\Resources\MA\MASucursalesResource\RelationManagers;
use App\Models\MA\MA_Sucursales;
use App\Models\MA\MASucursales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MASucursalesResource extends Resource
{
    protected static ?string $model = MA_Sucursales::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $modelLabel = 'Sucursales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Sucursal")->schema([
                        Forms\Components\TextInput::make('ID')
                            ->reactive()
                            ->hidden(),
                        Forms\Components\TextInput::make('Sucursal')
                            ->reactive()
                            ->required(),
                        Forms\Components\TextInput::make('Direccion')
                            ->required(),
                        Forms\Components\Select::make('TipoSucursalID')
                            ->relationship('tipoSucursal', 'TipoSucursal')
                            ->required(),
                        Forms\Components\Select::make('GerenciaID')
                            ->relationship('gerencia', 'Gerencia')
                            ->required(),
                    ])->columns(2),
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Visibilidad")->schema([
                        Forms\Components\Toggle::make('Activa')->default(true),
                        Forms\Components\Toggle::make('Visible')->default(true),
                        Forms\Components\Toggle::make('VisibleOC')->default(true)->label("Visible OC"),
                        Forms\Components\Toggle::make('VisibleCC')->default(true)->label("Visible CC"),
                    ]),
                ])->inlineLabel(),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Aprobadores")->schema([
                        Forms\Components\Repeater::make('Niveles')
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
                                        6 => 'Nivel 6',
                                        7 => 'Nivel 7',
                                        8 => 'Nivel 8',
                                        9 => 'Nivel 9',
                                        10 => 'Nivel 10',
                                    ]),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('usuarios', 'Nombre')
                                    ->searchable(),
                                Forms\Components\TextInput::make('min'),
                                Forms\Components\TextInput::make('max'),
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get): array {
                                $data['branch_office_id'] = $get('ID');

                                return $data;
                            })
                            ->maxItems(10)
//                            ->cloneable()
                            ->columns(4),
                    ]),
                ])->columnSpan(3),
            ])->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Sucursal')
                    ->searchable()
                    ->description(fn(MA_Sucursales $record) => $record->Direccion),
                Tables\Columns\IconColumn::make('Activa')->boolean(),
                Tables\Columns\IconColumn::make('Visible')->boolean(),
                Tables\Columns\IconColumn::make('VisibleOC')->label("Visible OC")->boolean(),
                Tables\Columns\IconColumn::make('VisibleCC')->label("Visible CC")->boolean(),
                Tables\Columns\TextColumn::make('tipoSucursal.TipoSucursal'),
                Tables\Columns\TextColumn::make('gerencia.Gerencia'),
                Tables\Columns\TextColumn::make('H_Texto'),
                Tables\Columns\TextColumn::make('CountAprobadores')->placeholder(fn(MA_Sucursales $record) => count($record->aprobadores))->label("Aprobadores"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make()
                ->disabled(!Auth::user()->isRole(['admin', 'marketing'])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                ->disabled(!Auth::user()->isAdmin()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
//            ApproversRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMASucursales::route('/'),
            'create' => Pages\CreateMASucursales::route('/create'),
            'edit' => Pages\EditMASucursales::route('/{record}/edit'),
        ];
    }
}
