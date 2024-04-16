<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SisIntegracionesHeadersResource\Pages;
use App\Filament\Resources\SisIntegracionesHeadersResource\RelationManagers;
use App\Models\SisIntegracionesHeaders;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SisIntegracionesHeadersResource extends Resource
{
    protected static ?string $model = SisIntegracionesHeaders::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Integracion Roma';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('FechaCreacion')
                    ->required(),
                Forms\Components\TextInput::make('EventoCreacionID')
                    ->required(),
                Forms\Components\TextInput::make('UsuarioCreacionID')
                    ->required(),
                Forms\Components\DateTimePicker::make('FechaActualizacion'),
                Forms\Components\TextInput::make('EventoActualizacionID'),
                Forms\Components\TextInput::make('UsuarioActualizacionID'),
                Forms\Components\TextInput::make('IntegracionID')
                    ->required(),
                Forms\Components\TextInput::make('MetodoID')
                    ->required(),
                Forms\Components\TextInput::make('Header')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
//                Tables\Columns\TextColumn::make('FechaCreacion')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('EventoCreacionID'),
//                Tables\Columns\TextColumn::make('UsuarioCreacionID'),
//                Tables\Columns\TextColumn::make('FechaActualizacion')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('EventoActualizacionID'),
//                Tables\Columns\TextColumn::make('UsuarioActualizacionID'),
                Tables\Columns\TextColumn::make('integracion.Integracion'),
                Tables\Columns\TextColumn::make('metodo.Metodo'),
                Tables\Columns\TextColumn::make('Header'),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageSisIntegracionesHeaders::route('/'),
        ];
    }
}
