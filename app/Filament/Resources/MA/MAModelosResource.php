<?php

namespace App\Filament\Resources\MA;

use App\Filament\Exports\MA\MAModelosExporter;
use App\Filament\Resources\MA\MAModelosResource\Pages;
use App\Filament\Resources\MA\MAModelosResource\RelationManagers;
use App\Models\MA\MA_Modelos;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MAModelosResource extends Resource
{
    protected static ?string $model = MA_Modelos::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Modelo';
    protected static ?string $navigationLabel = 'Modelos / Versiones';
    protected static ?string $pluralLabel = 'Modelos';
    protected static ?string $navigationGroup = 'Administracion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Modelo')->schema([
                        Forms\Components\TextInput::make('Modelo')
                            ->label('Modelo')
                            ->required(),
                        Forms\Components\Select::make('MarcaID')
                            ->label('Marca')
                            ->required()
                            ->relationship('marca', 'Marca'),
                        Forms\Components\TextInput::make('RutaFichaTecnica')
                            ->label('RutaFichaTecnica'),
                    ]),
                ]),
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Configuracion')->schema([
                        Forms\Components\Toggle::make('Activo'),
                        Forms\Components\Toggle::make('ActivoUsados'),
                        Forms\Components\Toggle::make('ActivoNuevo'),
                    ])->columns(3),
                    Forms\Components\Section::make('Alias')->schema([
                        Forms\Components\TextInput::make('H_TannerID')
                            ->label('H_TannerID')
                            ->default(0),
                        Forms\Components\TextInput::make('H_KiaID')
                            ->label('H_KiaID')
                            ->default(0),
                        Forms\Components\TextInput::make('H_IntouchID')
                            ->label('H_IntouchID')
                            ->default(0),
                        Forms\Components\TextInput::make('H_Texto')
                            ->label('H_Texto'),
                    ])->columns(2),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID'),

                Tables\Columns\TextColumn::make('Modelo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('marca.Marca')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('Activo')
                    ->label('Activo')
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('ActivoUsados')
                    ->label('ActivoUsados')
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('ActivoNuevo')
                    ->label('ActivoNuevo')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('contVersiones')
                    ->label('Versiones')
                    ->default(fn($record) => $record->versiones->count()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('MarcaID')
                    ->relationship('marca', 'Marca')
                    ->searchable()
                    ->label('Marca')
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(MAModelosExporter::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->disabled(!Auth::user()->isRole(['admin', 'marketing'])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->disabled(!Auth::user()->isRole(['admin'])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            'versiones' => RelationManagers\VersionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMAModelos::route('/'),
            'create' => Pages\CreateMAModelos::route('/create'),
            'edit' => Pages\EditMAModelos::route('/{record}/edit'),
        ];
    }
}
