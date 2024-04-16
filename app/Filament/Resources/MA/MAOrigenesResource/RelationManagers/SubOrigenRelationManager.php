<?php

namespace App\Filament\Resources\MA\MAOrigenesResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubOrigenRelationManager extends RelationManager
{
    protected static string $relationship = 'subOrigen';

    protected static ?string $recordTitleAttribute = 'SubOrigenes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('SubOrigen')
                    ->label('SubOrigen')
                    ->required(),
                Forms\Components\Toggle::make('ActivoInput'),
                Forms\Components\TextInput::make('Alias')
                    ->label('Alias')
                    ->inlineLabel()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('SubOrigen')
                    ->label('SubOrigen')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
