<?php

namespace App\Filament\Resources\FLU;

use App\Filament\Resources\FLU\FluFlujosResource\Pages;
use App\Filament\Resources\FLU\FluFlujosResource\RelationManagers;
use App\Models\FLU\FLU_Flujos;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class FluFlujosResource extends Resource
{
    protected static ?string $model = FLU_Flujos::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Flujos';

    protected static ?string $modelLabel = 'Flujos';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ID')->disabled(),
                Forms\Components\TextInput::make('Nombre')
                    ->required(),
                Forms\Components\TextInput::make('Descripcion'),
                Forms\Components\Select::make('Tipo')
                    ->options([
                        'REFERENCIA' => 'Referencia',
                        'API' => 'API',
                        'FLUJO' => 'Flujo',
                        'CARGA' => 'Carga',
                        'ACTUALIZACION' => 'Actualizacion',
                    ])
                    ->default('FLUJO')
                    ->disablePlaceholderSelection()
                    ->required(),
                Forms\Components\Select::make('Trigger')
                    ->options([
                        'ROUTE' => 'Route',
                        'PROCESS' => 'Process',
                        'SCHEDULE' => 'Schedule',
                    ])
                    ->default('PROCESS')
                    ->disablePlaceholderSelection()
                    ->required(),
                Forms\Components\Textarea::make('Opciones'),
                Forms\Components\Select::make('Role')
                    ->options([
                        'user' => 'Usuario',
                        'salvin' => 'Salvin',
                        'markeging' => 'Marketing',
                        'admin' => 'Admin',
                    ])->default('user'),
                Forms\Components\Select::make('Recurrencia')
                    ->options([
                        'TRIGGER' => 'Trigger',
                        'DAILY' => 'Daily',
                        'HOUR' => 'Hour',
                        'MINUTES' => 'Minutes',
                    ])
                    ->default('TRIGGER')
                    ->disablePlaceholderSelection()
                    ->required(),
                Forms\Components\TextInput::make('RecurrenciaValor')
                ->default(0),
                Forms\Components\TextInput::make('MaxLote')->default(1),
                Forms\Components\TextInput::make('Reintentos')->default(1),
                Forms\Components\TextInput::make('TiempoEspera')->default(0),

                Forms\Components\Toggle::make('Activo')


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Nombre')
                ->description(fn (FLU_Flujos $record): string => $record->Descripcion),
//                Tables\Columns\TextColumn::make('Descripcion'),
                Tables\Columns\TextColumn::make('Tipo'),
                Tables\Columns\TextColumn::make('Trigger'),
//                Tables\Columns\TextColumn::make('Recurrencia'),
//                Tables\Columns\TextColumn::make('RecurrenciaValor'),
                Tables\Columns\ToggleColumn::make('Activo')->disabled(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HomologacionesRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFluFlujos::route('/'),
            'create' => Pages\CreateFluFLujos::route('/create'),
            'edit' => Pages\EditFluFlujos::route('/{record}/edit'),
        ];
    }
}
