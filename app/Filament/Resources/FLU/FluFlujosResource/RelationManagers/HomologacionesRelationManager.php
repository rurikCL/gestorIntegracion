<?php

namespace App\Filament\Resources\FLU\FluFlujosResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HomologacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'homologaciones';

    protected static ?string $recordTitleAttribute = 'Homologacion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('CodHomologacion'),
                Forms\Components\TextInput::make('ValorIdentificador')->required(),
                Forms\Components\TextInput::make('ValorRespuesta')->required(),
                Forms\Components\TextInput::make('ValorNombre'),
                Forms\Components\Toggle::make('Activo')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('CodHomologacion')->searchable(),
                Tables\Columns\TextColumn::make('ValorIdentificador')->searchable(),
                Tables\Columns\TextColumn::make('ValorRespuesta')->searchable(),
                Tables\Columns\TextColumn::make('ValorNombre')->searchable(),
                Tables\Columns\ToggleColumn::make('Activo'),
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
