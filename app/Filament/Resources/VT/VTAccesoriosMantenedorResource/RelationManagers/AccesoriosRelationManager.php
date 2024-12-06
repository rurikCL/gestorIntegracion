<?php

namespace App\Filament\Resources\VT\VTAccesoriosMantenedorResource\RelationManagers;

use App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\VT\VT_ElementosFinanciadosSubTipos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccesoriosRelationManager extends RelationManager
{
    protected static string $relationship = 'accesorios';
    protected static ?string $modelLabel = 'Accesorio';
    protected static ?string $modelLabelPlural = 'Accesorios';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Descripcion')
                    ->label('Descripcion (SKU)')
                    ->columnSpanFull(),
                Forms\Components\Select::make('MarcaID')
                    ->relationship('marca', 'Marca')
                    ->label("Marca")
                    ->searchable(),

                Forms\Components\TextInput::make('Familia'),
                Forms\Components\TextInput::make('SKU')->columnSpanFull(),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('PrecioCosto')
                            ->label('Precio Costo (Neto)'),
                        Forms\Components\TextInput::make('PrecioCostoRoma')
                            ->label('Precio Costo (Bruto)')
                            ->readOnly(),
                        Forms\Components\TextInput::make('PrecioVenta')
                            ->label('Precio Venta (Bruto)'),
                    ])->columns(3)
                ->columnSpanFull(),

                Forms\Components\Toggle::make('Activo')
                ->default(true),

            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Accesorios')
            ->columns([
                Tables\Columns\TextColumn::make('marca.Marca')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('modelo.Modelo')->searchable()->sortable(),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
