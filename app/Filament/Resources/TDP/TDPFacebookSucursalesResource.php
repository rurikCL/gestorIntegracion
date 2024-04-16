<?php

namespace App\Filament\Resources\TDP;

use App\Filament\Resources\TDP\TDPFacebookSucursalesResource\Pages;
use App\Filament\Resources\TDP\TDPFacebookSucursalesResource\RelationManagers;
use App\Models\TDP\TDP_FacebookSucursales;
use App\Models\TDP\TDPFacebookSucursales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TDPFacebookSucursalesResource extends Resource
{
    protected static ?string $model = TDP_FacebookSucursales::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Sucursales Facebook';
    protected static ?string $navigationGroup = 'Marketing';


    protected static ?string $navigationLabel = "Sucursales Facebook";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Sucursal')
                    ->required(),
                Forms\Components\Select::make('SucursalID')
                    ->relationship('sucursal', 'Sucursal'),
                Forms\Components\Select::make('MarcaID')
                    ->relationship('marca', 'Marca'),
                Forms\Components\Select::make('GerenciaID')
                    ->relationship('gerencia', 'Gerencia'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Sucursal')->searchable(),
                Tables\Columns\TextColumn::make('sucursal.Sucursal')->searchable(),
                Tables\Columns\TextColumn::make('marca.Marca')->searchable(),
                Tables\Columns\TextColumn::make('gerencia.Gerencia')->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('SucursalID')
                    ->label("Sucursal")
                    ->relationship('sucursal', "Sucursal"),
                Tables\Filters\SelectFilter::make('GerenciaID')
                    ->label("Gerencia")
                    ->relationship('gerencia', "Gerencia"),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTDPFacebookSucursales::route('/'),
        ];
    }
}
