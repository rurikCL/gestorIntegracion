<?php

namespace App\Filament\Resources\TDP;

use App\Filament\Resources\TDP\TDPWebPompeyoSucursalesResource\Pages;
use App\Filament\Resources\TDP\TDPWebPompeyoSucursalesResource\RelationManagers;
use App\Models\TDP\TDP_WebPompeyoSucursales;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TDPWebPompeyoSucursalesResource extends Resource
{
    protected static ?string $model = TDP_WebPompeyoSucursales::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Sucursales Web';
    protected static ?string $navigationGroup = 'Marketing';


    protected static ?string $navigationLabel = "Sucursales Web";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Sucursal')
                    ->required(),
                Forms\Components\Select::make('SucursalID')
                    ->relationship('sucursal', 'Sucursal'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Sucursal')->searchable(),
                Tables\Columns\TextColumn::make('sucursal.Sucursal')->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('SucursalID')
                    ->label("Sucursal")
                    ->relationship('sucursal', "Sucursal"),
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
            'index' => Pages\ManageTDPWebPompeyoSucursales::route('/'),
        ];
    }
}
