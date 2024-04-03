<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAUsuariosResource\Pages;
use App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;
use App\Models\MA\MA_Usuarios;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MAUsuariosResource extends Resource
{
    protected static ?string $model = MA_Usuarios::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $navigationLabel = 'Usuarios Roma';
    protected static ?string $label = 'Usuario Roma';
    protected static ?string $pluralLabel = 'Usuarios Roma';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('Foto')
                    ->image()
                    ->directory('fotos'),
                Forms\Components\TextInput::make('Nombre'),
                Forms\Components\TextInput::make('Rut')
                ->hint('*Formato de rut sin puntos ni guion'),
                Forms\Components\TextInput::make('Email'),
                Forms\Components\TextInput::make('Celular'),
                Forms\Components\Select::make('PerfilID')
                    ->relationship('perfil', 'Perfil'),
                Forms\Components\Select::make('CargoID')->name('cargoUsuario')
                    ->options(fn () => \App\Models\MA\MA_Cargos::all()->pluck('Cargo', 'ID')),
                Forms\Components\Toggle::make('Disponible'),
                Forms\Components\Toggle::make('Activo'),
                Forms\Components\TextInput::make('Clave')
                    ->password(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')->searchable(),
                Tables\Columns\ImageColumn::make('Foto')
                    ->label('Foto'),

                Tables\Columns\TextColumn::make('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('Rut')->searchable(),
                Tables\Columns\TextColumn::make('Email')->searchable(),
//                Tables\Columns\TextColumn::make('Celular')->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('perfil.Perfil')->label('Perfil'),
                Tables\Columns\TextColumn::make('cargo.Cargo')->label('Cargo'),
                Tables\Columns\BooleanColumn::make('Disponible'),
                Tables\Columns\BooleanColumn::make('Activo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Activo')
                    ->form([
                        Forms\Components\Toggle::make('Activo')
                            ->default(true)
                    ])->query(function (Builder $query, array $data): Builder {
                        if ($data['Activo'] != null) {
                            $query->where('Activo', $data['Activo']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['Activo'] != null)
                            return 'Activos ';
                        else return null;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\CreateAction::make('Link Roma')
                ->url(fn (MA_Usuarios $record) => "https://apps1.pompeyo.cl/?id=" .$record->ID ."&token=6461433ef90325a215111f2af1464b2d09f2ba23", true)
                ->label('Link Roma')
                    ->icon('heroicon-o-link')
                ->color('success')
                ->visible(Auth::user()->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            'sucursales' => RelationManagers\SucursalesRelationManager::class,
            'agente' => RelationManagers\AgenteRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMAUsuarios::route('/'),
            'create' => Pages\CreateMAUsuarios::route('/create'),
            'edit' => Pages\EditMAUsuarios::route('/{record}/edit'),
        ];
    }
}
