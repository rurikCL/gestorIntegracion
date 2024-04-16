<?php

namespace App\Filament\Resources\TK;

use App\Filament\Resources\TK\TKAgentesResource\Pages;
use App\Filament\Resources\TK\TKAgentesResource\RelationManagers;
use App\Models\TK\TK_agents;
use App\Models\TK\TK_sub_categories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TKAgentesResource extends Resource
{
    protected static ?string $model = TK_agents::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Ticketera';
    protected static ?string $navigationLabel = 'Agentes';
    protected static ?string $pluralLabel = 'Agentes';
    protected static ?string $modelLabel = 'Agentes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('usuarioID')
                    ->relationship('usuario', 'Nombre'),
                Forms\Components\Select::make('categoryID')
                    ->options(fn () => \App\Models\TK\TK_categories::all()->pluck('name', 'id'))
                    ->reactive(),
                Forms\Components\Select::make('subCategoryID')
                    ->options(function (callable $get){
                        return $get('categoryID') ? (TK_sub_categories::where('category_id', $get('categoryID'))->pluck('name', 'id') ?? ['1', 'Sin Datos']) : ['1', 'Sin Datos'];
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('usuario.Nombre')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('subCategory.name')
                    ->searchable()
                    ->label('Sub Categoria'),
                Tables\Columns\TextColumn::make('subCategory.category.name')
                    ->searchable()
                    ->label('Categoria'),
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
            'index' => Pages\ListTKAgentes::route('/'),
            'create' => Pages\CreateTKAgentes::route('/create'),
            'edit' => Pages\EditTKAgentes::route('/{record}/edit'),
        ];
    }
}
