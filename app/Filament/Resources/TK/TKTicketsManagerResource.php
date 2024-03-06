<?php

namespace App\Filament\Resources\TK;

use App\Filament\Resources\TK\TKTicketsManagerResource\Pages;
use App\Filament\Resources\TK\TKTicketsManagerResource\RelationManagers;
use App\Models\TK\TK_Tickets;
use App\Models\TK\TKTicketsManager;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TKTicketsManagerResource extends Resource
{
    protected static ?string $model = TK_Tickets::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title'),
                Forms\Components\Textarea::make('priority'),
                Forms\Components\Select::make('category')
                ->relationship('categoria', 'name'),
                Forms\Components\Select::make('subCategory')
                ->relationship('subCategoria', 'name'),
                Forms\Components\TextInput::make('management'),
                Forms\Components\TextInput::make('zone'),
                Forms\Components\TextInput::make('department'),
                Forms\Components\TextInput::make('applicant'),
                Forms\Components\TextInput::make('assigned'),
                Forms\Components\Textarea::make('detail'),
                Forms\Components\Select::make('state'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Titulo'),
                Tables\Columns\TextColumn::make('priority')
                    ->searchable()
                    ->label('Prioridad'),
                Tables\Columns\TextColumn::make('categoria.name')
                    ->searchable()
                    ->label('Categoria'),
                Tables\Columns\TextColumn::make('subCategoria.name')
                    ->searchable()
                    ->label('Sub Categoria'),
                Tables\Columns\TextColumn::make('management'),
                Tables\Columns\TextColumn::make('zone'),
                Tables\Columns\TextColumn::make('department'),
                Tables\Columns\TextColumn::make('applicant'),
                Tables\Columns\TextColumn::make('assigned'),
                Tables\Columns\TextColumn::make('detail'),
                Tables\Columns\TextColumn::make('state'),

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
            'index' => Pages\ListTKTicketsManagers::route('/'),
            'create' => Pages\CreateTKTicketsManager::route('/create'),
            'edit' => Pages\EditTKTicketsManager::route('/{record}/edit'),
        ];
    }
}
