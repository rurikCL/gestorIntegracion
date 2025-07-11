<?php

namespace App\Filament\Resources\API\ApiSolicitudesResource\RelationManagers;

use App\Filament\Resources\API\ApiSolicitudesResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SolicitudesRelationManager extends RelationManager
{
    protected static string $relationship = 'subsolicitudes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make("Peticion")->schema([
                        Forms\Components\Textarea::make('Peticion')
                            ->rows(10),
                        Forms\Components\Textarea::make('Respuesta')
                            ->rows(10),
                    ])->columns(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Solicitudes')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\IconColumn::make('Exito')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ReferenciaID')
                    ->label('ID Referencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('integracion.Integracion')
                    ->description(fn($record) => $record->proveedores->Nombre ?? '')
                    ->label('Integracion'),
                Tables\Columns\TextColumn::make('flujo.Nombre')
                    ->label('Flujo'),
//                Tables\Columns\TextColumn::make('Prioridad'),
//                Tables\Columns\TextColumn::make('Peticion'),
//                Tables\Columns\TextColumn::make('Respuesta'),
                Tables\Columns\TextColumn::make('FechaPeticion')
                    ->dateTime("d/m/Y H:i:s"),
                Tables\Columns\TextColumn::make('FechaResolucion')->label('Hora resolucion')
                    ->dateTime("H:i:s"),
                Tables\Columns\TextColumn::make('CodigoRespuesta'),
                Tables\Columns\TextColumn::make('contadorHijos')
                ->state(fn($record) => $record->subsolicitudes->count())
                    ->label('Subsolicitudes')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\DeleteAction::make(),
            Tables\Actions\Action::make('Revisar')
                ->url(fn($record) => ApiSolicitudesResource::getNavigationUrl() . "/". $record->id)
                ->icon('heroicon-o-pencil-square')
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
