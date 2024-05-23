<?php

namespace App\Filament\Resources\FLU;

use App\Filament\Resources\FLU;
use App\Filament\Resources\FLUNotificacionesResource\Pages;
use App\Filament\Resources\FLUNotificacionesResource\RelationManagers;
use App\Models\FLU\FLU_Notificaciones;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FLUNotificacionesResource extends Resource
{
    protected static ?string $model = FLU_Notificaciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Flujos';

    protected static ?string $modelLabel = 'Notificaciones';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ID_Ref')
                    ->label('ID Referencia'),
                Select::make('ID_Flujo')
                    ->relationship('flujo', 'Nombre')
                    ->label('Flujo'),
                Toggle::make('Notificado')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID_Ref')
                    ->label('ID Referencia'),
                Tables\Columns\TextColumn::make('flujo.Nombre')
                    ->label('Flujo'),
                Tables\Columns\BooleanColumn::make('Notificado')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ID_Flujo')
                ->relationship('flujo', 'Nombre')
                ->label('Flujo')
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
            'index' => FLU\FLUNotificacionesResource\Pages\ListFLUNotificaciones::route('/'),
            'create' => FLU\FLUNotificacionesResource\Pages\CreateFLUNotificaciones::route('/create'),
            'edit' => FLU\FLUNotificacionesResource\Pages\EditFLUNotificaciones::route('/{record}/edit'),
        ];
    }
}
