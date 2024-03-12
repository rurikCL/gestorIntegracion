<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

class SucursalesRelationManager extends RelationManager
{
    protected static string $relationship = 'sucursales';

    protected static ?string $recordTitleAttribute = 'Sucursales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('SucursalID')
                    ->options(fn() => \App\Models\MA\MA_Sucursales::all()->pluck('Sucursal', 'ID'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('CargoID')
                    ->options(fn() => \App\Models\MA\MA_Cargos::all()->pluck('Cargo', 'ID'))
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('DisponibleLead'),
                Forms\Components\Toggle::make('Activo')->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sucursal.Sucursal')
                ->searchable(),
                Tables\Columns\SelectColumn::make('CargoID')->disabled(!Auth::user()->isAdmin())
                ->options(fn() => \App\Models\MA\MA_Cargos::all()->pluck('Cargo', 'ID')),
                Tables\Columns\ToggleColumn::make('DisponibleLead')->disabled(),
                Tables\Columns\ToggleColumn::make('Activo')->disabled(!Auth::user()->isAdmin()),
                Tables\Columns\TextColumn::make('FechaCreacion'),
                Tables\Columns\TextColumn::make('FechaActualizacion'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                        $data['EventoCreacionID'] = 1;
                        $data['UsuarioCreacionID'] = \Illuminate\Support\Facades\Auth::user()->id;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['FechaActualizacion'] = Carbon::now()->format('Y-m-d H:i:s');
                        $data['EventoActualizacion'] = 1;
                        $data['UsuarioActualizacion'] = \Illuminate\Support\Facades\Auth::user()->id;

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
