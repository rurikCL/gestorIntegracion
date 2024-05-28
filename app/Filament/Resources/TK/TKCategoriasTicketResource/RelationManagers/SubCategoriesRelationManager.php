<?php

namespace App\Filament\Resources\TK\TKCategoriasTicketResource\RelationManagers;

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
                Forms\Components\Select::make('SLA')
                    ->options(function (callable $get) {
                        if ($get('Prioridad') == 'Bajo')
                            return ['24'];
                        if ($get('Prioridad') == 'Medio')
                            return ['16'];
                        if ($get('Prioridad') == 'Urgente')
                            return ['4'];

                        return [4];
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('Area'),
                Tables\Columns\TextColumn::make('Prioridad'),
                Tables\Columns\TextColumn::make('SLA')
                    ->label('SLA (Horas)'),
                Tables\Columns\ToggleColumn::make('Activa'),
                Tables\Columns\TextColumn::make('sumTickets')
                ->default(fn($record) => TK_Tickets::where('subCategory', $record->id)->count())

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                        $data['EventoCreacionID'] = 1;
                        $data['UsuarioCreacionID'] = Auth::user()->id;

                        return $data;
                    }),
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
