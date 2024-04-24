<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AgenteRelationManager extends RelationManager
{
    protected static string $relationship = 'agente';

    protected static ?string $recordTitleAttribute = 'Categoria Ticket';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Categoria Ticket')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subCategory.category.name')
                    ->searchable()
                    ->label('Categoria'),
                Tables\Columns\TextColumn::make('subCategory.name')
                    ->searchable()
                    ->label('Sub Categoria'),

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
