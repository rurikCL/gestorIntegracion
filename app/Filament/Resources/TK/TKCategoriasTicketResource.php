<?php

namespace App\Filament\Resources\TK;

use App\Filament\Resources\TK\TKCategoriasTicketResource\Pages;
use App\Filament\Resources\TK\TKCategoriasTicketResource\RelationManagers;
use App\Models\TK\TK_categories;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TKCategoriasTicketResource extends Resource
{
    protected static ?string $model = TK_categories::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';    protected static ?string $navigationGroup = 'Ticketera';
    protected static ?string $pluralLabel = 'Categorias';
    protected static ?string $navigationLabel = 'Categorias ';
    protected static ?string $modelLabel = 'Categoria';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('countSubCategorias')
                    ->default(fn($record) => $record->sub_categories()->count())
                    ->label('Sub Categorias')
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
            'sub_categories' => RelationManagers\SubCategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTKCategoriasTickets::route('/'),
            'create' => Pages\CreateTKCategoriasTicket::route('/create'),
            'edit' => Pages\EditTKCategoriasTicket::route('/{record}/edit'),
        ];
    }
}
