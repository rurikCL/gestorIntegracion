<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAOrigenesResource\Pages;
use App\Filament\Resources\MA\MAOrigenesResource\RelationManagers;
use App\Models\MA\MA_Origenes;
use App\Models\MA\MAOrigenes;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MAOrigenesResource extends Resource
{
    protected static ?string $model = MA_Origenes::class;
    protected static ?string $modelLabel = 'Origen';
    protected static ?string $navigationLabel = 'Origenes';
    protected static ?string $pluralLabel = 'Origenes';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Origen')
                    ->label('Origen')
                    ->required(),
                Forms\Components\Toggle::make('ActivoInput'),
                Forms\Components\Toggle::make('Visible'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Origen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('ActivoInput')
                    ->label('Activo')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('Visible')
                    ->label('Visible')
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
            'subOrigen' => RelationManagers\SubOrigenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMAOrigenes::route('/'),
            'create' => Pages\CreateMAOrigenes::route('/create'),
            'edit' => Pages\EditMAOrigenes::route('/{record}/edit'),
        ];
    }
}
