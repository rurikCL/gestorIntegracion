<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MASubOrigenesResource\Pages;
use App\Filament\Resources\MA\MASubOrigenesResource\RelationManagers;
use App\Models\MA\MA_SubOrigenes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MASubOrigenesResource extends Resource
{
    protected static ?string $model = MA_SubOrigenes::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $modelLabel = 'SubOrigen';
    protected static ?string $navigationLabel = 'Sub Origenes';
    protected static ?string $pluralLabel = 'Sub Origenes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Select::make('OrigenID')
                            ->label('Origen')
                            ->relationship('origen', 'Origen'),
                        Forms\Components\TextInput::make('SubOrigen')
                            ->label('SubOrigen')
                            ->required(),
                        Forms\Components\TextInput::make('Alias')
                            ->label('Alias'),
                        Forms\Components\Toggle::make('ActivoInput')
                            ->label('Activo')
                            ->inline(false),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')
                    ->label('ID')
                    ->sortable(),
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
                Tables\Columns\BadgeColumn::make('countLeads')
                    ->default(fn($record) => $record->lead()->count()),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('OrigenID')
                    ->label('Origen')
                    ->relationship('origen', 'Origen'),
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
