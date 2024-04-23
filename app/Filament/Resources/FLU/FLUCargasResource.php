<?php

namespace App\Filament\Resources\FLU;

use App\Filament\Resources\FLU\FLUCargasResource\Pages;
use App\Filament\Resources\FLU\FLUCargasResource\RelationManagers;
use App\Filament\Resources\FLU\FLUCargasResource\Widgets\FluCargas;

use App\Models\FLU\FLU_Cargas;
use App\Models\FLU\FLU_Flujos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use SendGrid\Mail\Section;

class FLUCargasResource extends Resource
{
    protected static ?string $model = FLU_Cargas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Flujos';
    protected static ?string $modelLabel = 'Carga de Archivos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Archivo')
                    ->schema([
                        Forms\Components\FileUpload::make('File')
                            ->preserveFilenames()
                            ->enableDownload()
                            ->required()
                            ->hintHelp('El formato del archivo debe ser CSV'),
                        Forms\Components\Select::make('ID_Flujo')
//                            ->relationship('flujo', 'Nombre')
                            ->options(
                                fn() => FLU_Flujos::where('Tipo', 'CARGA')
//                            ->where('Role', Auth::user()->role)
                                    ->pluck('Nombre', 'ID')->toArray()
                            )->label('Flujo de carga')->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Informacion de la carga')
                    ->schema([
                        Forms\Components\Placeholder::make('Registros')
                            ->label('Total Registros')
                            ->content(fn($record) => $record->Registros ?? 0),
                        Forms\Components\Placeholder::make('RegistrosCargados')
                            ->label('Registros Cargados')
                            ->content(fn($record) => $record->RegistrosCargados ?? 0),
                        Forms\Components\Placeholder::make('RegistrosFallidos')
                            ->label('Registros Fallidos')
                            ->content(fn($record) => $record->RegistrosFallidos ?? 0),
                        Forms\Components\Placeholder::make('FechaCarga')
                            ->label('Fecha de Carga')
                            ->content(fn($record) => $record->FechaCarga ?? now()),

                        Forms\Components\Placeholder::make('Estado')
                            ->label('Estado')
                            ->content(fn($record) => $record->Estado ?? 'Pendiente'),
                        Forms\Components\Toggle::make('OnDemand')->default(false)->hidden(),
                    ])->columns(3)
                    ->hidden(fn($record) => !$record ?? true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flujo.Nombre')->searchable(),

                Tables\Columns\TextColumn::make('File')->limit(20)->tooltip(fn($record) => $record->File),
                Tables\Columns\TextColumn::make('FechaCarga')->date('d/m/Y'),
                Tables\Columns\IconColumn::make('Estado')->options([
                    'heroicon-o-clock' => 'Pendiente',
                    'heroicon-o-table-cells' => 'Procesando',
                    'heroicon-o-check-circle' => 'Procesado',
                    'heroicon-o-x-circle' => 'Fallido',
                ])->colors([
                    'warning' => 'Pendiente',
                    'info' => 'Procesando',
                    'success' => 'Procesado',
                    'danger' => 'Fallido'
                ]),
                Tables\Columns\TextColumn::make('Registros'),
                Tables\Columns\TextColumn::make('RegistrosCargados')->label('Cargados'),
                Tables\Columns\TextColumn::make('RegistrosFallidos')->label('Fallidos'),
                Tables\Columns\TextColumn::make('creadoPor.name')->label('Creado por'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('FechaCarga', 'desc');
    }

    public static function getWidgets(): array
    {
        return [
            FluCargas::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFLUCargas::route('/'),
        ];
    }


}
