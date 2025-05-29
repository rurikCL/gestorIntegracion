<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RevisionClientesResource\Pages;
use App\Filament\Resources\RevisionClientesResource\RelationManagers;
use App\Models\MA\MA_Clientes;
use App\Models\MK\MK_Leads;
use App\Models\RevisionClientes;
use App\Models\VT\VT_Cotizaciones;
use App\Models\VT_Ventas;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hoa\Iterator\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class RevisionClientesResource extends Resource
{
    protected static ?string $model = MA_Clientes::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $modelLabel = 'Clientes Duplicados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('ID'),
                        Forms\Components\TextInput::make('Rut'),
                        Forms\Components\TextInput::make('Nombre'),
                        Forms\Components\TextInput::make('Apellido'),
                        Forms\Components\TextInput::make('Direccion'),
                        Forms\Components\TextInput::make('Telefono'),
                        Forms\Components\TextInput::make('Email'),
                    ])->columns(2),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Repeater::make('Clientes')
                            ->relationship('clientesDuplicados')
                            ->schema([
                                Forms\Components\TextInput::make('ID')
                                    ->readOnly()
                                    ->reactive(),
                                Forms\Components\TextInput::make('Nombre'),
                                Forms\Components\TextInput::make('Apellido'),
                                Forms\Components\TextInput::make('Rut'),
                                Forms\Components\TextInput::make('Email'),
                                Forms\Components\TextInput::make('Telefono'),

                                /*Forms\Components\Placeholder::make('LeadsCount')
                                ->content(fn($record)=>$record->leads->count()),
                                Forms\Components\Placeholder::make('CotizacionesCount')
                                ->content(fn($record)=>$record->cotizaciones->count()),
                                Forms\Components\Placeholder::make('VentasCount')
                                ->content(fn($record)=>$record->ventas->count()),*/


                                Forms\Components\Placeholder::make('VentasCount')
                                    ->content(fn($record) => VT_Ventas::where('ClienteID', $record->ID)->count()),

                                Forms\Components\Placeholder::make('LeadsCount')
                                    ->content(fn($record) => MK_Leads::where('ClienteID', $record->ID)->count()),

                                Forms\Components\Placeholder::make('CotizacionesCount')
                                    ->content(fn($record) => VT_Cotizaciones::where('ClienteID', $record->ID)->count()),

                            ])->columns(3)
                            ->addable(false)
                            ->deletable(false)
                            ->itemLabel(fn($state)=>$state["Nombre"])
                            ->extraItemActions([
//                            Forms\Components\Actions\Action::make('delete')
                            ]),

                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                MA_Clientes::select('Rut', 'Nombre', 'ID', DB::raw('count(*) as cantidad'))
                    ->groupBy('Rut')
                    ->havingRaw('count(*) > 1')
            )
            ->columns([
                Tables\Columns\TextColumn::make('Rut')->searchable(),
                Tables\Columns\TextColumn::make('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('Email')->searchable(),
                Tables\Columns\TextColumn::make('cantidad'),

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
            'index' => Pages\ListRevisionClientes::route('/'),
            'create' => Pages\CreateRevisionClientes::route('/create'),
            'edit' => Pages\EditRevisionClientes::route('/{record}/edit'),
        ];
    }
}
