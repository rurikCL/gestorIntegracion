<?php

namespace App\Filament\Resources\TK;

use App\Filament\Resources\TK\TKTicketsManagerResource\Pages;
use App\Filament\Resources\TK\TKTicketsManagerResource\RelationManagers;
use App\Models\TK\TK_categories;
use App\Models\TK\TK_sub_categories;
use App\Models\TK\TK_Tickets;
use App\Models\TK\TKTicketsManager;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TKTicketsManagerResource extends Resource
{
    protected static ?string $model = TK_Tickets::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Ticketera';
    protected static ?string $pluralLabel = 'Tickets';
    protected static ?string $navigationLabel = 'Tickets TI';
    protected static ?string $modelLabel = 'Ticket';

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
                Tables\Columns\BadgeColumn::make('state_')
                    ->default(fn($record) => match ($record->state) {
                        1 => 'Abierto',
                        2 => 'En Proceso',
                        3 => 'Cerrado',
                    })
                    ->color(fn($record) => match ($record->state) {
                        1 => 'info',
                        2 => 'warning',
                        3 => 'success',
                    })->label('Estado'),
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


            ])
            ->filters([
                /*Tables\Filters\SelectFilter::make('category')
                    ->relationship('categoria', 'name'),
                Tables\Filters\SelectFilter::make('subCategory')
                    ->relationship('subCategoria', 'name')
                    ->multiple(),*/
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'Baja' => 'Baja',
                        'Normal' => 'Normal',
                        'Alta' => 'Alta',
                    ])
                    ->label('Prioridad'),
                Tables\Filters\SelectFilter::make('state')
                    ->options([
                        '1' => 'Abierto',
                        '2' => 'En Proceso',
                        '3' => 'Cerrado',
                    ])->multiple()
                    ->label('Estado'),
                Tables\Filters\Filter::make('subcategory')
                    ->form([
                        Forms\Components\Select::make('categoryID')
                            ->options(fn() => \App\Models\TK\TK_categories::all()->pluck('name', 'id'))
                            ->label('Categoria')
                            ->reactive(),
                        Forms\Components\Select::make('subCategory')
                            ->options(function (callable $get) {
                                return $get('categoryID') ? (TK_sub_categories::where('category_id', $get('categoryID'))->pluck('name', 'id') ?? ['1', 'Sin Datos']) : ['1', 'Sin Datos'];
                            })
                            ->label('Sub Categoria')
                            ->reactive(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['subCategory'] != null) {
                            $query->where('subCategory', $data['subCategory']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['subCategory'] != null)
                            return 'Categoria : ' . TK_categories::find($data['categoryID'])->name . ' / ' . TK_sub_categories::find($data['subCategory'])->name;
                        else return null;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
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
