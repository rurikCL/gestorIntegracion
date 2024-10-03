<?php

namespace App\Filament\Resources\SIS;

use App\Filament\Resources\SIS\SISSolicitudesArbolAprobacionesResource\Pages;
use App\Filament\Resources\SIS\SISSolicitudesArbolAprobacionesResource\RelationManagers;
use App\Models\SIS\SIS_SolicitudesArbolAprobaciones;
use App\Models\SIS\SISSolicitudesArbolAprobaciones;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SISSolicitudesArbolAprobacionesResource extends Resource
{
    protected static ?string $model = SIS_SolicitudesArbolAprobaciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Arbol de Aprobaciones';
    protected static ?string $navigationGroup = 'Administracion';

    // desactivado
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('UnidadNegocioID')
                    ->relationship('unidadNegocios', 'UnidadNegocio'),
                Forms\Components\Select::make('GerenciaID')
                    ->relationship('gerencia', 'Gerencia'),
                Forms\Components\Select::make('SucursalDerivadaID')
                    ->relationship('sucursalDerivada', 'Sucursal'),
                Forms\Components\Select::make('PerfilCreadorID')
                    ->relationship('perfilCreador', 'Perfil'),
                Forms\Components\Select::make('CargoCreadorID')
                    ->relationship('cargoCreador', 'Cargo'),
                Forms\Components\TextInput::make('NivelCreador'),
                Forms\Components\Select::make('PerfilResponsableID')
                    ->relationship('perfilResponsable', 'Perfil'),
                Forms\Components\Select::make('CargoResponsableID')
                    ->relationship('cargoResponsable', 'Cargo'),
                Forms\Components\TextInput::make('NivelResponsable'),
                Forms\Components\Select::make('UsuarioResponsableID')
                    ->relationship('usuarioResponsable', 'Nombre'),
                Forms\Components\TextInput::make('PasoFinal'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),
                Tables\Columns\TextColumn::make('unidadNegocios.UnidadNegocio'),
                Tables\Columns\TextColumn::make('gerencia.Gerencia')
                ->description(fn(SIS_SolicitudesArbolAprobaciones $record) => $record->sucursalDerivada->Sucursal ?? ''),
                Tables\Columns\TextColumn::make('perfilCreador.Perfil')
                ->description(fn(SIS_SolicitudesArbolAprobaciones $record) => $record->cargoCreador->Cargo ?? ''),
                Tables\Columns\TextColumn::make('NivelCreador'),
                Tables\Columns\TextColumn::make('perfilResponsable.Perfil')
                ->description(fn(SIS_SolicitudesArbolAprobaciones $record) => $record->cargoResponsable->Cargo ?? ''),
                Tables\Columns\TextColumn::make('NivelResponsable'),
                Tables\Columns\TextColumn::make('usuarioResponsable.Nombre')
                ->searchable(),
                Tables\Columns\TextColumn::make('PasoFinal'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unidadNegocios')
                ->relationship('unidadNegocios', 'UnidadNegocio')
                ->searchable(),
                Tables\Filters\SelectFilter::make('gerencia')
                ->relationship('gerencia', 'Gerencia')
                ->searchable(),
                Tables\Filters\SelectFilter::make('usuarioResponsable')
                ->relationship('usuarioResponsable', 'Nombre')
                ->searchable()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSISSolicitudesArbolAprobaciones::route('/'),
            'create' => Pages\CreateSISSolicitudesArbolAprobaciones::route('/create'),
            'edit' => Pages\EditSISSolicitudesArbolAprobaciones::route('/{record}/edit'),
        ];
    }
}
