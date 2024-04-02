<?php

namespace App\Filament\Resources\API;

use App\Filament\Resources\API;
use App\Filament\Resources\API\ApiProveedoresResource\RelationManagers\RespuestasTipoRelationManager;
use App\Models\Api\ApiProveedores;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use SendGrid\Mail\Section;

class ApiProveedoresResource extends Resource
{
    protected static ?string $model = ApiProveedores::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Orquestador API';
    protected static ?string $navigationLabel = 'APIs / Endpoints';
    protected static ?string $pluralLabel = 'APIs';
    protected static ?string $modelLabel = 'API';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make("API")->schema([
                        Forms\Components\TextInput::make('Nombre')
                            ->required(),
                        Forms\Components\Select::make('ProveedorID')
                            ->relationship('integracion', 'Integracion')
                            ->required(),
                        Forms\Components\Select::make('Tipo')
                            ->options([
                                'auth1' => 'auth1',
                                'auth2' => 'auth2',
                                'data' => 'data',
                            ])
                            ->default('data')
                            ->disablePlaceholderSelection()
                            ->required(),
                        Forms\Components\Select::make('Metodo')
                            ->options([
                                'POST' => 'POST',
                                'GET' => 'GET',
                                'PUT' => 'PUT',
                                'SOAP' => 'SOAP'
                            ])
                            ->default('POST')
                            ->disablePlaceholderSelection()
                            ->required(),
                        Forms\Components\TextInput::make('Url')
                            ->required()->columnSpan(2),
                        Forms\Components\TextInput::make('User'),
                        Forms\Components\TextInput::make('Password'),
                        Forms\Components\Textarea::make('Header')->columnSpan(2),

                    ]),
                ]),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Entrada")->schema([
                        Forms\Components\Select::make('TipoEntrada')
                            ->options([
                                'urlparam' => 'urlparam',
                                'param' => 'param',
                                'json' => 'json',
                                'xml' => 'xml'
                            ])
                            ->default('json')
                            ->disablePlaceholderSelection()
                            ->required(),
                        Forms\Components\Textarea::make('Params')
                            ->rows(8),
                        Forms\Components\Textarea::make('Json'),
                    ]),
                ]),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make("Respuesta")->schema([
                        Forms\Components\Select::make('TipoRespuesta')
                            ->options([
                                'json' => 'json',
                                'xml' => 'xml'
                            ])
                            ->default('json')
                            ->disablePlaceholderSelection()
                            ->required(),
                        Forms\Components\TextInput::make('IndiceError'),
                        Forms\Components\TextInput::make('IndiceExito'),
                        Forms\Components\TextInput::make('IndiceRespuesta'),
                        Forms\Components\TextInput::make('IndiceExpiracion'),
                        Forms\Components\TextInput::make('TiempoExpiracion'),
                        Forms\Components\TextInput::make('Token'),

                        Forms\Components\TextInput::make('Timeout')
                            ->default(0)
                            ->required(),
                    ])->columns(3),

                ])->columnSpan(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('API ID'),
                Tables\Columns\TextColumn::make('ProveedorID')->label('Proveedor ID'),
                Tables\Columns\TextColumn::make('integracion.Integracion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Nombre'),
//                    ->description(fn (ApiProveedores $record): string => $record->Url),
                Tables\Columns\TextColumn::make('Tipo'),
                Tables\Columns\TextColumn::make('Metodo'),
                Tables\Columns\TextColumn::make('TipoEntrada'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RespuestasTipoRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => API\ApiProveedoresResource\Pages\ListApiProveedores::route('/'),
            'create' => API\ApiProveedoresResource\Pages\CreateApiProveedores::route('/create'),
            'view' => API\ApiProveedoresResource\Pages\ViewApiProveedores::route('/{record}'),
            'edit' => API\ApiProveedoresResource\Pages\EditApiProveedores::route('/{record}/edit'),
        ];
    }
}
