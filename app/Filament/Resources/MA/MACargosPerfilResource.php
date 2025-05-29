<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MACargosPerfilResource\Pages;
use App\Filament\Resources\MA\MACargosPerfilResource\RelationManagers;
use App\Models\MA\MA_CargosPerfil;
use App\Models\MA\MACargosPerfil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MACargosPerfilResource extends Resource
{
    protected static ?string $model = MA_CargosPerfil::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Personas';
    protected static ?string $navigationLabel = 'Cargos / Perfiles';
    protected static ?string $modelLabel = 'CargosPerfil';
    protected static ?string $pluralLabel = 'CargosPerfiles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Select::make('CargoID')
                            ->relationship('cargo', 'Cargo')
                            ->required(),
                        Forms\Components\TextInput::make('CargoRex'),
                        Forms\Components\TextInput::make('CargoHomologado'),
                        Forms\Components\TextInput::make('AreaNegocio'),
                        Forms\Components\Select::make('PerfilID')
                            ->relationship('perfil', 'Perfil')
                            ->required(),
                        Forms\Components\TextInput::make('HerramientaInformatica'),
                        Forms\Components\TextInput::make('NivelAprobacion')
                            ->numeric(),
                        Forms\Components\TextInput::make('Orden')
                            ->numeric(),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('cargo.Cargo'),
                Tables\Columns\TextColumn::make('CargoRex'),
//                Tables\Columns\TextColumn::make('CargoHomologado'),
                Tables\Columns\TextColumn::make('AreaNegocio'),
                Tables\Columns\TextColumn::make('perfil.Perfil'),
//                Tables\Columns\TextColumn::make('HerramientaInformatica'),
//                Tables\Columns\TextColumn::make('NivelAprobacion'),
//                Tables\Columns\TextColumn::make('Orden'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('CargoID')
                    ->relationship('cargo', 'Cargo')
                    ->label('Cargo'),
                Tables\Filters\SelectFilter::make('PerfilID')
                    ->relationship('perfil', 'Perfil')
                    ->label('Perfil'),
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
            'index' => Pages\ListMACargosPerfils::route('/'),
            'create' => Pages\CreateMACargosPerfil::route('/create'),
            'edit' => Pages\EditMACargosPerfil::route('/{record}/edit'),
        ];
    }
}
