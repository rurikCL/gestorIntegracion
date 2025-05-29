<?php

namespace App\Filament\Resources\FLU;

use App\Filament\Resources\FLU\FLUHomologacionResource\Pages;
use App\Filament\Resources\FLU\FLUHomologacionResource\RelationManagers;
use App\Models\FLU\FLU_Homologacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FLUHomologacionResource extends Resource
{
    protected static ?string $model = FLU_Homologacion::class;
    protected static ?string $navigationGroup = 'Flujos';
    protected static ?string $modelLabel = 'Homologaciones';

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('FlujoID')
                    ->relationship('flujo', 'Nombre'),
                Forms\Components\TextInput::make('CodHomologacion'),
                Forms\Components\TextInput::make('ValorIdentificador')->required(),
                Forms\Components\TextInput::make('ValorRespuesta')->required(),
                Forms\Components\TextInput::make('ValorNombre'),
                Forms\Components\Toggle::make('Activo')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flujo.Nombre'),
                Tables\Columns\TextColumn::make('CodHomologacion')->searchable(),
                Tables\Columns\TextColumn::make('ValorIdentificador')->searchable(),
                Tables\Columns\TextColumn::make('ValorRespuesta')->searchable(),
                Tables\Columns\TextColumn::make('ValorNombre')->searchable(),
                Tables\Columns\ToggleColumn::make('Activo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('FlujoID')
                    ->label("Flujo")
                    ->relationship('flujo', "Nombre"),
                Tables\Filters\SelectFilter::make('CodHomologacion')
                    ->options(
                        fn() => FLU_Homologacion::groupBy('CodHomologacion')->pluck('CodHomologacion','CodHomologacion')->toArray()
                    )
                    ->label("Codigo"),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFLUHomologacions::route('/'),
        ];
    }
}
