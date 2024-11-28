<?php

namespace App\Filament\Resources\MK;

use App\Filament\Resources\MA\MAClientesResource;
use App\Filament\Resources\MK\MKLeadsResource\Pages;
use App\Filament\Resources\MK\MKLeadsResource\RelationManagers;
use App\Http\Controllers\Api\ApiSolicitudController;
use App\Http\Controllers\Api\LeadController;
use App\Models\MA\MA_Canales;
use App\Models\MA\MA_Clientes;
use App\Models\MA\MA_Gerencias;
use App\Models\MA\MA_Marcas;
use App\Models\MA\MA_Modelos;
use App\Models\MA\MA_Origenes;
use App\Models\MA\MA_SubOrigenes;
use App\Models\MA\MA_Usuarios;
use App\Models\MK\MK_Leads;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MKLeadsResource extends Resource
{
    protected static ?string $model = MK_Leads::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $modelLabel = 'Leads';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make("Lead")->schema([
                    Forms\Components\TextInput::make('ID')
                        ->label('ID')->disabled(),
                    Forms\Components\Select::make('EstadoID')
                        ->relationship('estadoLead2', 'Estado')
                        ->label('Estado Lead'),
                    Forms\Components\Select::make('SucursalID')
                        ->relationship('sucursal2', 'Sucursal')
                        ->reactive()
                        ->searchable()
                        ->label('Sucursal')
                        ->columnSpan(2),
                    Forms\Components\Select::make('OrigenID')
                        ->options(MA_Origenes::all()->pluck('Origen', 'ID')->toArray())
                        ->reactive()
//                        ->relationship('origen2', 'Origen')
                        ->label('Origen'),
                    Forms\Components\Select::make('SubOrigenID')
                        ->options(function (callable $get) {
                            return $get('OrigenID') ? (MA_Origenes::find($get('OrigenID'))->suborigen->pluck('SubOrigen', 'ID') ?? ['1', 'Sin Datos']) : ['1', 'Sin Datos'];
                        })
//                        ->relationship('suborigen2', 'SubOrigen')
                        ->label('Sub Origen'),
                    Forms\Components\Select::make('CanalID')
                        ->options(MA_Canales::all()->pluck('Canal', 'ID')->toArray())
                        ->label('Canal'),
                    Forms\Components\MarkdownEditor::make('Comentario')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('LinkInteres')
                        ->label('Link de Interes')
                        ->columnSpanFull(),

                ])->columns(2)->compact(),
                Section::make('Otra informacion')->schema([

                    Forms\Components\TextInput::make('IDExterno')->label('ID Externo'),
                    Forms\Components\TextInput::make('IDHubspot')->label('ID Hubspot'),
                    Forms\Components\TextInput::make('ConcatID')->label('ID Concat'),

                    Forms\Components\TextInput::make('SubEstadoID'),
                    Forms\Components\TextInput::make('IntegracionID'),
                    Forms\Components\TextInput::make('LandBotID'),

                    Forms\Components\TextInput::make('Venta')->label('ID Venta'),
                    Forms\Components\TextInput::make('CotizacionID')->label('ID Cotizacion'),
                    Forms\Components\TextInput::make('FechaReAsignado'),

                    Forms\Components\Toggle::make('Financiamiento'),
                    Forms\Components\Toggle::make('Asignado'),
                    Forms\Components\Toggle::make('Llamado'),
                    Forms\Components\Toggle::make('Agendado'),
                    Forms\Components\Toggle::make('Cotizado'),
                    Forms\Components\Toggle::make('Vendido'),
                    Forms\Components\Toggle::make('Contesta'),
                    Forms\Components\Toggle::make('Contactado'),
                    Forms\Components\Toggle::make('LogEstado'),
                ])->columns(3)->collapsible()

            ])->columnSpan(2),
            Forms\Components\Group::make()->schema([
                Section::make('Cliente')->schema([
                    Forms\Components\Select::make('ClienteID')
                        ->relationship('cliente2', 'Nombre')
                        ->label('Nombre Cliente')
                        ->searchable(),
                    Forms\Components\Placeholder::make('cliente2.Rut')
                        ->label('Rut')
                        ->content(fn(MK_Leads $record): ?string => $record->cliente->Rut),
                    Forms\Components\Placeholder::make('cliente2.Email')
                        ->label('Email')
                        ->content(fn(MK_Leads $record): ?string => $record->cliente->Email),
                    Forms\Components\Placeholder::make('cliente2.Telefono')
                        ->label('Telefono')
                        ->content(fn(MK_Leads $record): ?string => $record->cliente->Telefono ?? 'Sin Telefono'),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('EditarCliente')
                        ->url(fn(MK_Leads $record): ?string => MAClientesResource::getNavigationUrl() .'/' .($record->ClienteID ?? '') . '/edit')
                        ->slideOver()
                            ->label('Editar Cliente'),
                    ]),

                ]),

                Section::make('Cliente Lead')->schema([
                    Forms\Components\TextInput::make('Rut'),
                    Forms\Components\TextInput::make('Nombre'),
                    Forms\Components\TextInput::make('Email'),
                    Forms\Components\TextInput::make('Telefono'),
                    Forms\Components\TextInput::make('Direccion'),
                ])->collapsed()
                    ->compact(),

                Section::make('Vendedor')->schema([
                    Forms\Components\Select::make('VendedorID')
                        ->options(function (callable $get) {
                            return MA_Usuarios::sucursalAsignada($get('SucursalID'))->pluck('Nombre', 'ID') ?? ['1', 'Sin Vendedor'];
                        }),
//                        ->relationship('vendedor', 'Nombre')->searchable(),
                    Forms\Components\Placeholder::make('vendedor.Email')
                        ->label('Email')
                        ->content(fn(MK_Leads $record): ?string => $record->vendedor->Email ?? 'Sin Email'),
                    Forms\Components\Placeholder::make('vendedor.Telefono')
                        ->label('Telefono')
                        ->content(fn(MK_Leads $record): ?string => $record->vendedor->Telefono ?? 'Sin Telefono'),
                ]),
                Section::make('Vehiculo')->schema([

                    Forms\Components\Select::make('MarcaID')
                        ->options(MA_Gerencias::all()->pluck('Gerencia', 'MarcaAsociada')->toArray())
                        ->reactive()
                        ->label('Marca'),
                    Forms\Components\Select::make('ModeloID')
                        ->options(function (callable $get) {
                            return MA_Marcas::find($get('MarcaID'))->modelos->pluck('Modelo', 'ID') ?? ['1', 'Sin Modelo'];
                        })
                        ->reactive()
                        ->label('Modelo'),
                    Forms\Components\Select::make('VersionID')
                        ->options(function (callable $get) {
                            return MA_Modelos::find($get('ModeloID'))->versiones->pluck('Version', 'ID') ?? ['1', 'Sin Version'];
                        })
                        ->columnSpan(2)
                        ->label('Version'),
                ])->columns(2),

            ]),


        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID')->color('gray')
//                        ->icon('heroicon-o-document-duplicate')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('estadoLead.Estado')
                    ->colors([
                        'warning' => 'PENDIENTE',
                        'blue' => 'EN COTIZACIÓN',
                        'danger' => 'INUBICABLE',
                        'primary' => 'SIN INTENCIÓN DE COMPRA',
                        'orange' => 'LLAMAR NUEVAMENTE',
                        'secondary' => 'VIGENTE',
                        'success' => 'FACTURADO',
                        'red' => 'FRACASO',
                    ])->searchable()->sortable(),
                Tables\Columns\TextColumn::make('FechaCreacion')
                    ->dateTime('d/m/Y H:i:s'),
                Tables\Columns\TextColumn::make('Origen.Origen')->weight('medium'),
                Tables\Columns\TextColumn::make('SubOrigen.SubOrigen')->size('sm'),

                Tables\Columns\TextColumn::make('sucursal.Sucursal')->size('sm'),
//                  Tables\Columns\TextColumn::make('vendedor.Nombre'),
                Tables\Columns\TextColumn::make('marca.Marca')->weight('medium')->searchable(),
                Tables\Columns\TextColumn::make('modelo.Modelo')->size('sm')->searchable(),
//                        Tables\Columns\TextColumn::make('version.Version')->searchable(),
                Tables\Columns\TextColumn::make('IDHubspot')
                    ->label('ID Hubspot')
//                        ->copyable()->copyMessage('Lead ID copiado al portapapeles')->copyMessageDuration(1500)
                    ->searchable(),
            ])->defaultSort('ID', 'desc')
            ->filters([
                Tables\Filters\Filter::make('Fecha')
                    ->form([
                        DatePicker::make('FechaDesde')
                            ->default(Carbon::now()->startOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['FechaDesde'])) {
                            $query->where('FechaCreacion', '>=', $data['FechaDesde']);
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
                Tables\Filters\SelectFilter::make('OrigenID')
                    ->relationship('origen2', 'Origen'), Tables\Filters\SelectFilter::make('MarcaID')
                    ->relationship('marca2', 'Marca')->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make("Regla Lead Vendedor")
                    ->icon('heroicon-m-arrow-path')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            LeadController::reglaLead($record, true, false);
                        }
                    })->requiresConfirmation(),
                Tables\Actions\BulkAction::make("Regla Lead Sucursal")
                    ->icon('heroicon-m-arrow-path')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            LeadController::reglaLead($record, false, true);
                        }
                    })->requiresConfirmation()

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LogsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMKLeads::route('/'),
//            'create' => Pages\CreateMKLeads::route('/create'),
            'edit' => Pages\EditMKLeads::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }


}
