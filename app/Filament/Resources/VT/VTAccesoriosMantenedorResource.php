<?php

namespace App\Filament\Resources\VT;

use App\Filament\Resources\VT\VTAccesoriosMantenedorResource\Pages;
use App\Filament\Resources\VT\VTAccesoriosMantenedorResource\RelationManagers;
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

class VTAccesoriosMantenedorResource extends Resource
{
    protected static ?string $model = VT_ElementosFinanciadosSubTipos::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Mantenedor Accesorios';
    protected static ?string $navigationGroup = 'Elementos Financiados';

    public static function canAccess(): bool
    {
        return auth()->user()->isRole(['admin', 'analista']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('SubTipo')
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\Toggle::make('Activo')
                            ->default(true)
                            ->inline(false),
                    ])->columns(4),
                Forms\Components\Section::make('')
                    ->schema([
                        /*Forms\Components\Toggle::make('USADOS')
                            ->label('USADOS'),*/
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
                    ])->columns(4),
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Toggle::make('ConsiderarReporte')
                            ->inline(false),
                        Forms\Components\Toggle::make('TieneInstalacionAcc')
                            ->inline(false),
                        Forms\Components\TextInput::make('TiempoInstalacion')
                            ->numeric()
                            ->suffix("hrs")
                            ->columnSpan(2),
                    ])->columns(4)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->where('TipoID', 3);
            })
            ->columns([
                Tables\Columns\TextColumn::make('SubTipo')->searchable(),
                Tables\Columns\ToggleColumn::make('Activo')->label('Activo'),
//                Tables\Columns\BooleanColumn::make('USADOS')->label('USADOS'),
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
                Tables\Filters\SelectFilter::make('SubTipo')
                    ->options(fn() => VT_ElementosFinanciadosSubTipos::where('TipoID', 3)
                        ->pluck('SubTipo', 'SubTipo')->toArray())
                    ->searchable(),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('SubTipo', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            VTAccesoriosMantenedorResource\RelationManagers\AccesoriosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => VTAccesoriosMantenedorResource\Pages\ListVTAccesoriosMantenedor::route('/'),
            'create' => VTAccesoriosMantenedorResource\Pages\CreateVTAccesoriosMantenedor::route('/create'),
            'edit' => VTAccesoriosMantenedorResource\Pages\EditVTAccesoriosMantenedor::route('/{record}/edit'),
        ];
    }
}
