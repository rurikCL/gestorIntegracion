<?php

namespace App\Filament\Resources\API;

use App\Filament\Resources\API;
use App\Filament\Resources\API\ApiSolicitudesResource\RelationManagers\LogsRelationManager;
use App\Filament\Resources\API\APISolicitudesResource\Widgets\StatsOverview;
use App\Filament\Resources\ApiSolicitudesResource\Pages;
use App\Filament\Resources\ApiSolicitudesResource\RelationManagers;
use App\Http\Controllers\Api\ApiSolicitudController;
use App\Models\Api\ApiSolicitudes;
use App\Models\FLU\FLU_Flujos;
use App\Models\FLU\FLU_Notificaciones;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Position;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ApiSolicitudesResource extends Resource
{
    protected static ?string $model = ApiSolicitudes::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Orquestador API';

    protected static ?string $navigationLabel = 'Monitor trabajos';


    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make("Solicitud")->schema([
                    Forms\Components\TextInput::make('ReferenciaID')
                        ->required(),
                    Forms\Components\Select::make('Prioridad')
                        ->options([
                            1 => "Normal",
                            2 => "Media",
                            3 => "Alta"
                        ])
                        ->required(),
                    Forms\Components\DateTimePicker::make('FechaPeticion')
                        ->default(Carbon::now())
                        ->displayFormat("d/m/Y H:i")
                        ->required(),
                    Forms\Components\DateTimePicker::make('FechaResolucion')
                        ->displayFormat("d/m/Y H:i"),

                    Forms\Components\Select::make('ProveedorID')
                        ->relationship('integracion', 'Integracion')
                        ->required(),
                    Forms\Components\Select::make('ApiID')
                        ->relationship('Proveedores', 'Nombre')
                        ->label("Api")
                        ->required(),
                    Forms\Components\Select::make('FlujoID')
                        ->relationship('flujo', 'Nombre'),
                    Forms\Components\Toggle::make('Exito')
                        ->inline(false)
                        ->disabled(),
                    Forms\Components\Toggle::make('Reprocesa')
                        ->inline(false)
                        ->disabled(!Auth::user()->isAdmin()),
                    Forms\Components\TextInput::make('Reintentos')
                        ->disabled(!Auth::user()->isAdmin()),

                    Forms\Components\TextInput::make('idSolicitudPadre')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('verPadre')
                                ->icon('heroicon-s-eye')
                                ->url(fn($record) => $record->idSolicitudPadre ? ApiSolicitudesResource::getUrl('view', ['record' => $record->idSolicitudPadre]) : null)
                                ->visible(fn($record) => $record->idSolicitudPadre)
                        )->label("ID Solicitud Padre"),

                    Forms\Components\Placeholder::make('Notificacion')
                        ->content(fn($record) => ($record->notificacion)  ? "Notificado" : "Sin Notificacion")
                    ->label('Notificacion'),

                ])->columns(4),
            ])->columnSpan(2),
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make("Peticion")->schema([
                    Forms\Components\Textarea::make('Peticion')
                        ->rows(21),
                    Forms\Components\Textarea::make('PeticionHeader')
                        ->rows(10),
                ]),
            ]),
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make("Respuesta")->schema([
                    Forms\Components\TextInput::make('CodigoRespuesta')
//                            ->hint("200 = OK, 0 = Error")
                        ->disabled(),
                    Forms\Components\Textarea::make('Respuesta')
                        ->rows(30)
                        ->disabled(),
                ]),
            ]),

        ]);
    }


    public function isTableSearchable(): bool
    {
        return true;
    }

    public static function table(Table $table): Table
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('idSolicitudPadre', null);
            })
            ->columns([
                Tables\Columns\IconColumn::make('Exito')
                    ->boolean(),
                /*Tables\Columns\IconColumn::make('notificacion.Notificado')
                    ->boolean(),*/
                Tables\Columns\TextColumn::make('ReferenciaID')
                    ->label('ID Referencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('integracion.Integracion')
                    ->description(fn($record) => $record->proveedores->Nombre ?? '')
                    ->label('Integracion'),
                /*Tables\Columns\TextColumn::make('proveedores.Nombre')
                    ->label('Api'),*/
                Tables\Columns\TextColumn::make('flujo.Nombre')
                    ->label('Flujo'),
//                Tables\Columns\TextColumn::make('Prioridad'),
//                Tables\Columns\TextColumn::make('Peticion'),
//                Tables\Columns\TextColumn::make('Respuesta'),
                Tables\Columns\TextColumn::make('FechaPeticion')
                    ->dateTime("d/m/Y H:i:s"),
                Tables\Columns\TextColumn::make('FechaResolucion')->label('Hora resolucion')
                    ->dateTime("H:i:s"),
                Tables\Columns\TextColumn::make('CodigoRespuesta'),


                Tables\Columns\TextColumn::make('contadorHijos')
                    ->state(fn($record) => $record->subsolicitudes->count())
                    ->label('Subsolicitudes')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('updated_at')->label('Actualizado a')
//                    ->dateTime("H:i:s"),
            ])
            ->filters([
                Tables\Filters\Filter::make('Fecha')
                    ->form([
                        DatePicker::make('FechaDesde')
                            ->default(Carbon::now()->startOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['FechaDesde'])) {
                            $query->where('FechaPeticion', '>=', $data['FechaDesde']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['FechaDesde']) {
                            return null;
                        }

                        return 'Desde : ' . Carbon::parse($data['FechaDesde'])->format('d/m/Y');
                    })
                    ->label("Fecha"),

                Tables\Filters\SelectFilter::make('FlujoID')
                    ->label("Flujo")
                    ->relationship('flujo', "Nombre"),

                Tables\Filters\SelectFilter::make('Exito')
                    ->options([
                        1 => "Exitosas",
                        0 => "Fallidas"
                    ]),
                Tables\Filters\SelectFilter::make('notificacion.Notificado')
                    ->options([
                        1 => "Notificadas",
                        0 => "Sin Notificar"
                    ]),
                Tables\Filters\Filter::make('Excluidos')
                    ->form([
                        Select::make('Excluidos')
                            ->options(fn() => FLU_Flujos::all()->pluck('Nombre', 'ID')->toArray())
                            ->label("Excluidos")
                            ->multiple()
                    ])->query(function (Builder $query, array $data): Builder {
                        if (isset($data['Excluidos'])) {
                            return $query->whereNotIn('FlujoID', $data['Excluidos']);
                        } else return $query;
                    })->indicateUsing(function (array $data): ?string {
                        return ($data['Excluidos']) ? 'Registros Excluidos' : null;
                    }),

                Tables\Filters\SelectFilter::make('ProveedorID')
                    ->relationship('proveedores', 'Nombre')
            ], FiltersLayout::Modal)
            ->persistFiltersInSession()
            ->filtersFormColumns(3)

            ->actions([
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\Action::make('reprocesar')
//                    ->action(function ($record) {
//                        ApiSolicitudController::reprocesarJob($record);
//                    })
//                    ->icon('heroicon-s-refresh')
//                    ->label('')
//                    ->requiresConfirmation()
//                    ->tooltip('Reprocesar'),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('reprocesar')->label("Reprocesar Seleccionados")
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            ApiSolicitudController::reprocesarJob($record);
                        }
                    })
                    ->icon('heroicon-m-arrow-path')
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation(),
                Tables\Actions\BulkAction::make('eliminarNotificacion')->label("Borrar Seleccionados")
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            FLU_Notificaciones::where('ID_Ref', $record->ReferenciaID)
                                ->where('ID_Flujo', $record->FlujoID)
                                ->update(['Notificado' => 0]);

                            $record->delete();
                        }
                    })
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation(),
            ])
            ->actionsPosition(\Filament\Tables\Enums\ActionsPosition::BeforeColumns)
            ->poll('10s')
            ->defaultSort('FechaPeticion', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            LogsRelationManager::class,
            API\ApiSolicitudesResource\RelationManagers\SolicitudesRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [StatsOverview::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => API\ApiSolicitudesResource\Pages\ListApiSolicitudes::route('/'),
            'create' => API\ApiSolicitudesResource\Pages\CreateApiSolicitudes::route('/create'),
            'view' => API\ApiSolicitudesResource\Pages\ViewApiSolicitudes::route('/{record}'),
            'edit' => API\ApiSolicitudesResource\Pages\EditApiSolicitudes::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return ApiSolicitudes::SolicitudesPendientes();
    }

    public static function actions()
    {
        return [Forms\Components\Actions\Action::make('reprocesar')
            ->label("Reprocesar")
            ->action(function ($record) {
                ApiSolicitudController::reprocesarJob($record);
            })
            ->icon('heroicon-m-arrow-path')
            ->requiresConfirmation()
            ->tooltip('Reprocesar'),

            Forms\Components\Actions\Action::make('descargar')
                ->label("Descargar")
                ->action(function ($record) {
                    ApiSolicitudController::descargarJob($record);
                })
                ->icon('heroicon-s-download')
                ->requiresConfirmation()
                ->tooltip('Descargar'),
        ];
    }
}
