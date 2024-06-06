<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAPerfilesResource\Pages;
use App\Filament\Resources\MA\MAPerfilesResource\RelationManagers;
use App\Models\MA\MA_Perfiles;
use App\Models\MA\MAPerfiles;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MAPerfilesResource extends Resource
{
    protected static ?string $model = MA_Perfiles::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Personas';
    protected static ?string $navigationLabel = 'Perfiles';
    protected static ?string $modelLabel = 'Perfiles';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Perfil')
                    ->label('Perfil')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Perfil')
                ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListMAPerfiles::route('/'),
            'create' => Pages\CreateMAPerfiles::route('/create'),
            'edit' => Pages\EditMAPerfiles::route('/{record}/edit'),
        ];
    }
}
