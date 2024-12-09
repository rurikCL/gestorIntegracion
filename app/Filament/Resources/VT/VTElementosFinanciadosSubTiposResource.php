<?php

namespace App\Filament\Resources\VT;

use App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource\Pages;
use App\Filament\Resources\VT\VTElementosFinanciadosSubTiposResource\RelationManagers;
use App\Models\VT\VT_ElementosFinanciadosSubTipos;
use App\Models\VT\VT_ElementosFinanciadosTipos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use function VeeWee\Xml\Dom\Xpath\Locator\query;

class VTElementosFinanciadosSubTiposResource extends Resource
{
    protected static ?string $model = VT_ElementosFinanciadosSubTipos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Elementos Financiados - Sub tipo';
    protected static ?string $navigationGroup = 'Elementos Financiados';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Select::make('TipoID')
                            ->options(fn() => (Auth::user()->isAdmin())
                                ? VT_ElementosFinanciadosTipos::where('ID', 3)->pluck('Tipo', 'ID')->toArray()
                                : VT_ElementosFinanciadosTipos::all()->pluck('Tipo', 'ID')->toArray()
                            )
                            ->default(3)
                            ->required(),
                        Forms\Components\TextInput::make('SubTipo')
                            ->required(),
                        Forms\Components\Toggle::make('Activo'),
                    ])->columns(2),
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Toggle::make('USADOS')
                            ->label('USADOS'),
                        Forms\Components\Toggle::make('KIA')
                            ->label('KIA'),
                        Forms\Components\Toggle::make('CITROEN')
                            ->label('CITROEN'),
                        Forms\Components\Toggle::make('DFSK')
                            ->label('DFSK'),
                        Forms\Components\Toggle::make('GEELY')
                            ->label('GEELY'),
                        Forms\Components\Toggle::make('MG')
                            ->label('MG'),
                        Forms\Components\Toggle::make('NISSAN')
                            ->label('NISSAN'),
                        Forms\Components\Toggle::make('OPEL')
                            ->label('OPEL'),
                        Forms\Components\Toggle::make('PEUGEOT')
                            ->label('PEUGEOT'),
                        Forms\Components\Toggle::make('SUBARU')
                            ->label('SUBARU'),
                    ])->columns(3),
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Toggle::make('TieneInstalacionAcc')
                            ->inline(false),
                        Forms\Components\TextInput::make('TiempoInstalacion'),
                        Forms\Components\Toggle::make('ConsiderarReporte')
                            ->inline(false),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo.Tipo')->searchable(),
                Tables\Columns\TextColumn::make('SubTipo')->searchable(),
                Tables\Columns\ToggleColumn::make('Activo')->label('Activo'),
                Tables\Columns\BooleanColumn::make('USADOS')->label('USADOS'),
                Tables\Columns\BooleanColumn::make('KIA')->label('KIA'),
                Tables\Columns\BooleanColumn::make('CITROEN')->label('CITROEN'),
                Tables\Columns\BooleanColumn::make('DFSK')->label('DFSK'),
                Tables\Columns\BooleanColumn::make('GEELY')->label('GEELY'),
                Tables\Columns\BooleanColumn::make('MG')->label('MG'),
                Tables\Columns\BooleanColumn::make('NISSAN')->label('NISSAN'),
                Tables\Columns\BooleanColumn::make('OPEL')->label('OPEL'),
                Tables\Columns\BooleanColumn::make('PEUGEOT')->label('PEUGEOT'),
                Tables\Columns\BooleanColumn::make('SUBARU')->label('SUBARU'),
                Tables\Columns\BooleanColumn::make('ConsiderarReporte'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('TipoID')
                    ->relationship('tipo', 'Tipo')
                    ->label('Tipo'),
                Tables\Filters\SelectFilter::make('SubTipo')
                    ->options(fn() => VT_ElementosFinanciadosSubTipos::pluck('SubTipo', 'SubTipo')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                /*Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVTElementosFinanciadosSubTipos::route('/'),
            'create' => Pages\CreateVTElementosFinanciadosSubTipos::route('/create'),
            'edit' => Pages\EditVTElementosFinanciadosSubTipos::route('/{record}/edit'),
        ];
    }
}
