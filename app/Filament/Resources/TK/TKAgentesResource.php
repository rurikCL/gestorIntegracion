<?php

namespace App\Filament\Resources\TK;

use App\Filament\Resources\TK\TKAgentesResource\Pages;
use App\Filament\Resources\TK\TKAgentesResource\RelationManagers;
use App\Models\TK\TK_Agentes;
use App\Models\TK\TK_Agentes_Usuarios;
use App\Models\TK\TKAgentes;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TKAgentesResource extends Resource
{
    protected static ?string $model = TK_Agentes::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';    protected static ?string $navigationGroup = 'Ticketera';
    protected static ?string $modelLabel = 'Agente';
    protected static ?string $navigationLabel = 'Agentes / Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')->schema([
                    Forms\Components\TextInput::make('Nombre'),
                    Forms\Components\TextInput::make('Descripcion'),
                    Forms\Components\Select::make('Area')
                        ->options([
                            'Desarrollo' => 'Desarrollo',
                            'TI' => 'TI',
                        ]),
                    Forms\Components\Toggle::make('Activo')
                        ->inline(false)
                        ->default(true)
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('Nombre')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('Descripcion')
                    ->searchable()
                    ->label('Descripcion'),
                Tables\Columns\TextColumn::make('Area'),
                Tables\Columns\BadgeColumn::make('countUsuarios')
                    ->default(fn($record) => $record->usuarioAgente->count())
                    ->label('Usuarios'),

                Tables\Columns\BooleanColumn::make('Activo')
                    ->label('Activo'),
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
            'usuarios' => RelationManagers\UsuariosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTKAgentes::route('/'),
            'create' => Pages\CreateTKAgentes::route('/create'),
            'edit' => Pages\EditTKAgentes::route('/{record}/edit'),
        ];
    }
}
