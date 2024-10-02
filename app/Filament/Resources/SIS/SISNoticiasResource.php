<?php

namespace App\Filament\Resources\SIS;

use App\Filament\Resources\SIS\SISNoticiasResource\Pages;
use App\Filament\Resources\SIS\SISNoticiasResource\RelationManagers;
use App\Models\SIS\SISNoticias;
use App\Models\SIS_Noticias;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SISNoticiasResource extends Resource
{
    protected static ?string $model = SIS_Noticias::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Noticias';
    protected static ?string $navigationGroup = 'Administracion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Titulo'),
                Forms\Components\TextInput::make('Ruta'),
                Forms\Components\TextInput::make('Descripcion'),
                Forms\Components\Toggle::make('Activo'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Titulo'),
                Tables\Columns\TextColumn::make('Descripcion'),
                Tables\Columns\ToggleColumn::make('Activa'),
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
            'index' => Pages\ListSISNoticias::route('/'),
            'create' => Pages\CreateSISNoticias::route('/create'),
            'edit' => Pages\EditSISNoticias::route('/{record}/edit'),
        ];
    }
}
