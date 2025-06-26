<?php

namespace App\Filament\Resources\MA\MAModelosResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VersionesRelationManager extends RelationManager
{
    protected static string $relationship = 'versiones';

    protected static ?string $recordTitleAttribute = 'Versiones';
    protected static ?string $modelLabel = 'Version';
    protected static ?string $pluralLabel = 'Versiones';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Version')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('H_TannerID')
                    ->maxLength(255),
                Forms\Components\TextInput::make('H_KiaID')
                    ->maxLength(255),
                Forms\Components\TextInput::make('H_ForumID')
                    ->maxLength(255),
                Forms\Components\Toggle::make('Activo'),
                Forms\Components\Toggle::make('ActivoUsados'),
                Forms\Components\Toggle::make('ActivoNuevo'),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Version')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('Activo')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('ActivoUsados')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('ActivoNuevo')
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Activo')
                    ->form([
                        Forms\Components\Toggle::make('Activo')
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
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                        $data['EventoCreacionID'] = 1;
                        $data['UsuarioCreacionID'] = Auth::user()->id;

                        if($data['H_TannerID'] == '') $data['H_TannerID'] = 0;
                        if($data['H_KiaID'] == '') $data['H_KiaID'] = 0;
                        if($data['H_ForumID'] == '') $data['H_ForumID'] = 0;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
