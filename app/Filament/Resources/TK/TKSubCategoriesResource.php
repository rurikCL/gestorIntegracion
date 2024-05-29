<?php

namespace App\Filament\Resources\TK;

use App\Filament\Resources\TK\TKSubCategoriesResource\Pages;
use App\Filament\Resources\TK\TKSubCategoriesResource\RelationManagers;
use App\Models\TK\TK_sub_categories;
use App\Models\TK\TK_Tickets;
use App\Models\TK\TKSubCategories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TKSubCategoriesResource extends Resource
{
    protected static ?string $model = TK_sub_categories::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ticketera';
    protected static ?string $navigationLabel = 'Sub Categorias ';
    protected static ?string $modelLabel = 'SubCategoria';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')->schema([
                    Forms\Components\TextInput::make('name'),
                    Forms\Components\Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label('Categoria')
                        ->required(),
                    Forms\Components\Select::make('AgenteID')
                        ->relationship('agente', 'Nombre')
                        ->label('Agente')
                        ->required(),
                    Forms\Components\Select::make('SubAgenteID')
                        ->relationship('subAgente', 'Nombre')
                        ->label('Sub Agente')
                        ->required(),
                    Forms\Components\Select::make('Prioridad')
                        ->options([
                            'Bajo' => 'Bajo',
                            'Medio' => 'Medio',
                            'Urgente' => 'Urgente',
                        ])->reactive(),
                    Forms\Components\Toggle::make('Activo')
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('agente.Nombre')
                    ->label('Agente'),
                Tables\Columns\TextColumn::make('subAgente.Nombre')
                    ->label('Sub Agente'),
                Tables\Columns\TextColumn::make('Prioridad'),
                Tables\Columns\TextColumn::make('SLA')
                    ->label('SLA (Horas)'),
                Tables\Columns\ToggleColumn::make('Activo')
                    ->default(true),
                Tables\Columns\BadgeColumn::make('sumTickets')
                    ->default(fn($record) => TK_Tickets::where('subCategory', $record->id)->count())
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTKSubCategories::route('/'),
            'create' => Pages\CreateTKSubCategories::route('/create'),
            'edit' => Pages\EditTKSubCategories::route('/{record}/edit'),
        ];
    }
}
