<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAAccesoriosResource\Pages;
use App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource;
use App\Models\MA\MA_Accesorios;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\VT\VT_ElementosFinanciadosSubTipos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Psl\Type\null;

class MAAccesoriosResource extends Resource
{
    protected static ?string $model = MA_Accesorios::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Accesorio';
    protected static ?string $pluralLabel = 'Accesorios';
    protected static ?string $navigationGroup = 'Elementos Financiados';


    public static function canAccess(): bool
    {
        return auth()->user()->isRole(['admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informacion de Accesorio')
                    ->schema([
                        /*Forms\Components\TextInput::make('Marca')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $marca = MA_Marcas::where('Marca', 'like', "%$state%")->first();
                                    if ($marca) $set('MarcaID', $marca->ID);
                                }
                            }),
                        Forms\Components\TextInput::make('Modelo')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $modelo = MA_Modelos::where('Modelo', 'like', "%$state%")->first();
                                    if ($modelo) $set('ModeloID', $modelo->ID);
                                }
                            }),*/

                        Forms\Components\Select::make('MarcaID')
                            ->relationship('marca', 'Marca')
                            ->live()
                            ->reactive()
                            ->label("Marca")
                            ->searchable()
//                            ->after(fn($record) => ($record) ? MA_Marcas::where('Marca', $record->Marca)->pluck('ID') : null)
                            ->afterStateUpdated(function ($state, Set $set) {
//                                $set('Marca', MA_Marcas::find($state)->Marca);
                            }),
                        /*Forms\Components\Select::make('ModeloID')
//                            ->relationship('modelo', 'Modelo')
                            ->options(function (callable $get) {
                                if ($get('MarcaID')) {
                                    return MA_Marcas::find($get('MarcaID'))->modelos->pluck('Modelo', 'ID') ?? null;
                                } else {
                                    return null;
                                }
                            })
                            ->reactive()
                            ->live()
                            ->searchable()
                            ->label("Modelo")
                            ->default(fn($record) => ($record) ? MA_Modelos::where('Modelo', $record->Modelo)->pluck('ID') : null)
                            ->afterStateUpdated(function ($state, Set $set) {
//                                $set('Modelo', MA_Modelos::find($state)->Modelo);
                            }),*/

                        Forms\Components\Select::make('SubTipoID')
                            ->options(fn()=> VT_ElementosFinanciadosSubTipos::where('TipoID', 3)
                                ->where('Activo', 1)->pluck('SubTipo', 'ID') ?? null)
                            ->reactive()
                            ->label("Subtipo")
                        ->prefixAction(Forms\Components\Actions\Action::make('CrearSubtipo')
                        ->url(fn($record) => ($record->ordenCompra) ? (VTElementosFinanciadosSubTiposResource::getNavigationUrl() . '/create' ) : null, true)),
//                        Forms\Components\TextInput::make('TipoTxt'),

                        Forms\Components\TextInput::make('Familia'),
                        Forms\Components\TextInput::make('SKU'),
                        Forms\Components\TextInput::make('Descripcion'),
                        Forms\Components\TextInput::make('PrecioCosto')
                        ->label('Precio Costo (Neto)'),
                        Forms\Components\TextInput::make('PrecioCostoRoma')
                            ->label('Precio Costo (Bruto)')
                            ->readOnly(),
                        Forms\Components\TextInput::make('PrecioVenta')
                        ->label('Precio Venta (Bruto)'),
                        Forms\Components\Toggle::make('Activo'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('marca.Marca')->searchable()->sortable(),
//                Tables\Columns\TextColumn::make('modelo.Modelo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('subtipo.SubTipo'),
                Tables\Columns\TextColumn::make('Familia'),
                Tables\Columns\TextColumn::make('TipoTxt')->searchable(),
                Tables\Columns\TextColumn::make('SKU'),
                Tables\Columns\TextColumn::make('Descripcion'),
                Tables\Columns\TextColumn::make('PrecioCosto'),
                Tables\Columns\TextColumn::make('PrecioCostoRoma'),
                Tables\Columns\TextColumn::make('PrecioVenta'),
                Tables\Columns\BooleanColumn::make('Activo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('MarcaID')
                ->relationship('marca', 'Marca'),
                Tables\Filters\SelectFilter::make('ModeloID')
                ->relationship('modelo', 'Modelo'),


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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMAAccesorios::route('/'),
            'create' => Pages\CreateMAAccesorios::route('/create'),
            'edit' => Pages\EditMAAccesorios::route('/{record}/edit'),
        ];
    }
}
