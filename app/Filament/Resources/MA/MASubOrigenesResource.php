<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MASubOrigenesResource\Pages;
use App\Filament\Resources\MA\MASubOrigenesResource\RelationManagers;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MA\MASubOrigenes;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MASubOrigenesResource extends Resource
{
    protected static ?string $model = MA_SubOrigenes::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $modelLabel = 'SubOrigen';
    protected static ?string $navigationLabel = 'Sub Origenes';
    protected static ?string $pluralLabel = 'Sub Origenes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('OrigenID')
                    ->label('Origen')
                    ->relationship('origen', 'Origen'),
                Forms\Components\TextInput::make('SubOrigen')
                    ->label('SubOrigen')
                    ->required(),
                Forms\Components\Toggle::make('ActivoInput'),
                Forms\Components\TextInput::make('Alias')
                    ->label('Alias')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('SubOrigen')
                    ->label('SubOrigen')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('origen.Origen')
                    ->label('Origen')
                    ->sortable()
                    ->searchable(),


                Tables\Columns\TextColumn::make('Alias')
                    ->label('Alias')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('ActivoInput')
                    ->label('Activo')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMASubOrigenes::route('/'),
            'create' => Pages\CreateMASubOrigenes::route('/create'),
            'edit' => Pages\EditMASubOrigenes::route('/{record}/edit'),
        ];
    }
}
