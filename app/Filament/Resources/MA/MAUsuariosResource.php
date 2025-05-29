<?php

namespace App\Filament\Resources\MA;

use App\Filament\Resources\MA\MAUsuariosResource\Pages;
use App\Filament\Resources\MA\MAUsuariosResource\RelationManagers;
use App\Models\MA\MA_Usuarios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MAUsuariosResource extends Resource
{
    protected static ?string $model = MA_Usuarios::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';    protected static ?string $navigationGroup = 'Personas';
    protected static ?string $navigationLabel = 'Usuarios Roma';
    protected static ?string $label = 'Usuario Roma';
    protected static ?string $pluralLabel = 'Usuarios Roma';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informacion de usuario')
                    ->schema([
                        Forms\Components\TextInput::make('Nombre'),

                        Forms\Components\TextInput::make('Rut')
                            ->hintHelp('*Formato de rut sin puntos ni guion'),

                        Forms\Components\TextInput::make('Email'),
                        Forms\Components\TextInput::make('Celular'),

                        Forms\Components\Select::make('PerfilID')
                            ->relationship('perfil', 'Perfil'),

                        Forms\Components\Select::make('CargoID')->name('cargoUsuario')
                            ->options(fn() => \App\Models\MA\MA_Cargos::all()->pluck('Cargo', 'ID')),

                        Forms\Components\Select::make('SucursalID')
                            ->relationship('sucursal', 'Sucursal')
                            ->label('Sucursal asignada'),

                        Forms\Components\Select::make('SupervisorID')
                            ->relationship('supervisor', 'Nombre')
                            ->label('Supervisor asignado'),

                        Forms\Components\TextInput::make('Clave')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state)),

                        Forms\Components\Toggle::make('Disponible')
                            ->inline(false),

                        Forms\Components\Toggle::make('Activo'),
                    ])
                    ->columns(2),
                /*Forms\Components\FileUpload::make('Foto')
                    ->image()
                    ->directory('fotos'),*/


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')->searchable(),
                /*Tables\Columns\ImageColumn::make('Foto')
                    ->label('Foto'),*/

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
                Tables\Filters\Filter::make('Activo')
                    ->form([
                        Forms\Components\Toggle::make('Activo')
                            ->default(true)
                            ->inline(false)
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

                Tables\Filters\SelectFilter::make('SucursalID')
                    ->options(fn() => \App\Models\MA\MA_Sucursales::where('Activa', 1)->pluck('Sucursal', 'ID'))
                    ->label('Sucursal asignada')
                    ->searchable(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\CreateAction::make('Roma')
                    ->url(fn(MA_Usuarios $record) => "https://apps1.pompeyo.cl/?id=" . $record->ID . "&token=6461433ef90325a215111f2af1464b2d09f2ba23", true)
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
            RelationGroup::make('Sucursales', [
                'sucursales' => RelationManagers\SucursalesRelationManager::class,
            ]),
            RelationGroup::make('Aprobaciones', [
                'aprobadorOc' => RelationManagers\AprobadorocRelationManager::class,
                'aprobadorCaja' => RelationManagers\AprobadorCajaRelationManager::class,
            ]),
            'agente' => RelationManagers\AgenteRelationManager::class,
            'generadorOC' => RelationManagers\GeneradorOrdenCompraRelationManager::class,
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
