<?php

namespace App\Filament\Resources\MA\MAOrigenesResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SubOrigenRelationManager extends RelationManager
{
    protected static string $relationship = 'subOrigen';

    protected static ?string $recordTitleAttribute = 'SubOrigenes';
    protected static ?string $modelLabel = 'Sub Origen';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('SubOrigen')
                            ->label('Nombre Sub Origen')
                            ->required(),
                        Forms\Components\TextInput::make('Alias')
                            ->label('Alias (Hubspot)'),
                        Forms\Components\Toggle::make('ActivoInput')
                            ->label('Activo')
                            ->inline(false),
                    ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('SubOrigen')
                    ->label('SubOrigen')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('Alias')
                    ->label('Alias')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('ActivoInput')
                    ->label('Activo')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
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
