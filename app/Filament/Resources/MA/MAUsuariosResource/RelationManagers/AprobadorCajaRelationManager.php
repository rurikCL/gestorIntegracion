<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;

use App\Filament\Resources\MA\MASucursalesResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AprobadorCajaRelationManager extends RelationManager
{
    protected static string $relationship = 'aprobadorCaja';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_office_id')
                    ->relationship('sucursales', 'Sucursal'),

                Forms\Components\Select::make('level')
                    ->options([
                        '1' => 'Nivel 1',
                        '2' => 'Nivel 2',
                        '3' => 'Nivel 3',
                        '4' => 'Nivel 4',
                        '5' => 'Nivel 5',
                    ]),
                Forms\Components\TextInput::make('min'),
                Forms\Components\TextInput::make('max'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('AprobacionCaja')
            ->columns([
                Tables\Columns\TextColumn::make('sucursales.Sucursal'),
                Tables\Columns\TextColumn::make('level')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('editaSucursal')
                    ->url(fn($record): ?string => MASucursalesResource::getNavigationUrl() . '/' . $record->branch_office_id . '/edit')
                ->openUrlInNewTab()
                ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
