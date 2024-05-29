<?php

namespace App\Filament\Resources\TK\TKCategoriasTicketResource\RelationManagers;

use App\Filament\Resources\TK\TKSubCategoriesResource;
use App\Models\TK\TK_Tickets;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SubCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'sub_categories';

    protected static ?string $recordTitleAttribute = 'Sub Categoria';
    protected static ?string $modelLabel = "Sub Categoria";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nombre')
                    ->maxLength(255),
                Forms\Components\Select::make('Area')
                    ->options([
                        'Desarrollo' => 'Desarrollo',
                        'Ingreso de Colaboradores' => 'Ingreso de Colaboradores',
                        'TI' => 'TI',
                    ]),
                Forms\Components\Select::make('Prioridad')
                    ->options([
                        'Bajo' => 'Bajo',
                        'Medio' => 'Medio',
                        'Urgente' => 'Urgente',
                    ])->reactive(),
                Forms\Components\Toggle::make('Activo')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('agente.Area')
                    ->label('Area'),
                Tables\Columns\TextColumn::make('agente.Nombre')
                    ->label('Agente'),
                Tables\Columns\TextColumn::make('subAgente.Nombre')
                    ->label('Sub Agente'),
                Tables\Columns\TextColumn::make('Prioridad'),
                Tables\Columns\TextColumn::make('SLA')
                    ->label('SLA (Horas)'),
                Tables\Columns\ToggleColumn::make('Activo')
                ->sortable(),
                Tables\Columns\BadgeColumn::make('sumTickets')
                    ->default(fn($record) => TK_Tickets::where('subCategory', $record->id)->count())

            ])
            ->filters([
                Tables\Filters\Filter::make('Activo')
                    ->form([
                        Forms\Components\Toggle::make('Activo')
                            ->default(true)
                            ->inline(false)
                    ])->query(function (Builder $query, array $data): Builder {
                        if ($data['Activo'] != null) {
                            $query->where('Activo', $data['Activo']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['Activo'] != null)
                            return 'Activos ';
                        else return null;
                    }),            ])
            ->headerActions([
                Tables\Actions\Action::make('Crear Sub categoria')
                    ->url(fn($record) => TKSubCategoriesResource::getNavigationUrl() . '/create')
                    ->visible(fn() => Auth::user()->isAdmin())
            ])
            ->actions([
                Tables\Actions\Action::make('Editar')
                    ->url(fn($record) => TKSubCategoriesResource::getNavigationUrl() . '/' . $record->id . '/edit')
                    ->icon('heroicon-s-pencil-square')
                    ->visible(fn() => Auth::user()->isAdmin())

//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
