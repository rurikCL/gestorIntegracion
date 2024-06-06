<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MACargosResource\Pages;
use App\Filament\Resources\MA\MACargosResource\RelationManagers;
use App\Models\MA\MA_Cargos;
use App\Models\MA\MACargos;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MACargosResource extends Resource
{
    protected static ?string $model = MA_Cargos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Personas';
    protected static ?string $navigationLabel = 'Cargos';
    protected static ?string $modelLabel = 'Cargos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Cargo')
                    ->label('Cargo')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Cargo')
            ])
            ->filters([
                //
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
//            RelationManagers\CargosPerfilRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMACargos::route('/'),
            'create' => Pages\CreateMACargos::route('/create'),
            'edit' => Pages\EditMACargos::route('/{record}/edit'),
        ];
    }
}
