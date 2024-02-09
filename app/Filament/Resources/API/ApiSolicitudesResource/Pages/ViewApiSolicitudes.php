<?php

namespace App\Filament\Resources\API\ApiSolicitudesResource\Pages;

use App\Filament\Resources\API\ApiSolicitudesResource;
use App\Http\Controllers\Api\ApiSolicitudController;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApiSolicitudes extends ViewRecord
{
    protected static string $resource = ApiSolicitudesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('reprocesar')
                ->label("Reprocesar")
                ->action(fn()  =>  ApiSolicitudController::reprocesarJob($this->record))
                ->icon('heroicon-s-refresh')
                ->color('success')
                ->requiresConfirmation(),
        ];
    }
}
