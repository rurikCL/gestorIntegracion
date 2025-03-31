<?php

namespace App\Filament\Resources\FLU;

use App\Filament\Resources\FLU\FLUMonitorResource\Pages;
use App\Filament\Resources\FLU\FLUMonitorResource\RelationManagers;
use App\Models\FLU\FLUMonitor;
use App\Models\FLU_Monitor;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FLUMonitorResource extends Resource
{
    protected static ?string $model = FLU_Monitor::class;
    protected static ?string $navigationGroup = 'Flujos';
    protected static ?string $modelLabel = 'Monitor de Flujo';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('FlujoID')
                        ->relationship('flujo', 'Nombre'),
                    Forms\Components\TextInput::make('Accion'),
                    Forms\Components\TextInput::make('Estado'),
                    Forms\Components\TextInput::make('Mensaje'),
                    Forms\Components\DateTimePicker::make('FechaInicio'),
                    Forms\Components\DateTimePicker::make('FechaTermino'),
                    Forms\Components\TextInput::make('Duracion'),

                ])->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flujo.Nombre'),
                Tables\Columns\TextColumn::make('Accion'),
                Tables\Columns\TextColumn::make('Estado')->badge(),
                Tables\Columns\TextColumn::make('Mensaje'),
                Tables\Columns\TextColumn::make('FechaInicio'),
                Tables\Columns\TextColumn::make('FechaTermino'),
                Tables\Columns\TextColumn::make('Duracion')->suffix(" seg"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('FlujoID')
                    ->label("Flujo")
                    ->relationship('flujo', "Nombre"),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFLUMonitors::route('/'),
            'create' => Pages\CreateFLUMonitor::route('/create'),
            'edit' => Pages\EditFLUMonitor::route('/{record}/edit'),
        ];
    }
}
