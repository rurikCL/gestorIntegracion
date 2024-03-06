<?php

namespace App\Filament\Resources\TK;

use App\Filament\Resources\TK\TKCategoriasTicketResource\Pages;
use App\Filament\Resources\TK\TKCategoriasTicketResource\RelationManagers;
use App\Models\TK\TK_categories;
use App\Models\TK\TKCategoriasTicket;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TKCategoriasTicketResource extends Resource
{
    protected static ?string $model = TK_categories::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationLabel = 'Categorias Ticket';
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
