<?php

namespace App\Filament\Resources\FLU;

use App\Filament\Resources\FLU\FLUCargasResource\Pages;
use App\Filament\Resources\FLU\FLUCargasResource\RelationManagers;
use App\Filament\Resources\FLU\FLUCargasResource\Widgets\FluCargas;

use App\Models\FLU\FLU_Cargas;
use App\Models\FLU\FLU_Flujos;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use SendGrid\Mail\Section;

class FLUCargasResource extends Resource
{
    protected static ?string $model = FLU_Cargas::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

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
                        Forms\Components\TextInput::make('Registros')->label('Total Registros')->disabled(),
                        Forms\Components\TextInput::make('RegistrosCargados')->disabled(),
                        Forms\Components\TextInput::make('RegistrosFallidos')->disabled(),
                        Forms\Components\DateTimePicker::make('FechaCarga')->disabled(),

                        Forms\Components\Select::make('Estado')
                            ->options([
                                0 => 'Pendiente',
                                1 => 'Procesando',
                                2 => 'Procesado',
                                3 => 'Fallido',
                            ])->default(0),
                        Forms\Components\Toggle::make('OnDemand')->default(false)->hidden(),
                    ])->columns(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flujo.Nombre')->searchable(),

                Tables\Columns\TextColumn::make('File')->limit(20),
                Tables\Columns\TextColumn::make('FechaCarga')->date('d/m/Y'),
                Tables\Columns\IconColumn::make('Estado')->options([
                    'heroicon-o-clock' => 0,
                    'heroicon-o-table' => 1,
                    'heroicon-o-check-circle' => 2,
                    'heroicon-o-x-circle' => 3,
                ])->colors([
                    'warning'=>0,
                    'info'=>1,
                    'success' =>2,
                    'danger'=>3
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
