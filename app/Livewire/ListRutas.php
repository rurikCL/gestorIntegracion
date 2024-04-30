<?php

namespace App\Livewire;

use App\Http\Controllers\Api\ApiSolicitudController;
use App\Models\FLU\FLU_Flujos;
use App\Models\Rutas;
use Filament\Actions\CreateAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Matrix\Builder;

class ListRutas extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;
    public function render()
    {
        return view('livewire.list-rutas');
    }

    public function table(Table $table) : Table
    {
        return $table
            ->query(Rutas::query())
            ->columns([
                TextColumn::make('label')->sortable()->searchable(),
                TextColumn::make('descripcion')->sortable()->searchable(),
                TextColumn::make('ruta')->sortable()->searchable(),
            ])
            ->actions([
                //
                Action::make('ejecutar')
                    ->label('Ejecutar')
                    ->icon('heroicon-o-play')
                    ->openUrlInNewTab()
                    ->url(function (Rutas $ruta) {
                        return $ruta->ruta;
                    })
                ->requiresConfirmation()
            ])
            ->filters([
                //
            ]);

    }

}
