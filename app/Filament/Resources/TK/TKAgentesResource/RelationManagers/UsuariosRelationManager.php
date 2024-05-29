<?php

namespace App\Filament\Resources\TK\TKAgentesResource\RelationManagers;

use App\Models\MA\MA_Usuarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsuariosRelationManager extends RelationManager
{
    protected static string $relationship = 'usuarioAgente';
    protected static ?string $modelLabel = 'Usuario';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('UsuarioID')
                    ->options(fn () => MA_Usuarios::where('Activo',1)->pluck('Nombre', 'ID'))
                    ->label('Nombre de Usuario')
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('Activo')
                ->inline(false)
                ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Usuarios')
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('usuario.Nombre')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\BooleanColumn::make('Activo')
                    ->label('Activo'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
