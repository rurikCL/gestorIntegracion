<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SisIntegracionesResource\Pages;
use App\Filament\Resources\SisIntegracionesResource\RelationManagers;
use App\Models\SisIntegraciones;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SisIntegracionesResource extends Resource
{
    protected static ?string $model = SisIntegraciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Orquestador API';
    protected static ?string $modelLabel = 'Integraciones';

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
                Forms\Components\TextInput::make('Integracion')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')->label('ID'),
                Tables\Columns\TextColumn::make('Integracion'),
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
            'index' => Pages\ManageSisIntegraciones::route('/'),
        ];
    }
}
