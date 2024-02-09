<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAUsuariosResource\Pages;
use App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;
use App\Models\MA\MA_Usuarios;
use App\Models\MA\MAUsuarios;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MAUsuariosResource extends Resource
{
    protected static ?string $model = MA_Usuarios::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $modelLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ManageMAUsuarios::route('/'),
        ];
    }
}
