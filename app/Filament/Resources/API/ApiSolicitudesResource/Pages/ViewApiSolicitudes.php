<?php

namespace App\Filament\Resources\API\ApiSolicitudesResource\Pages;

use App\Filament\Resources\API\ApiSolicitudesResource;
use App\Http\Controllers\Api\ApiSolicitudController;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApiSolicitudes extends ViewRecord
{
    protected static string $resource = ApiSolicitudesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('reprocesar')
                ->label("Reprocesar")
                ->action(fn()  =>  ApiSolicitudController::reprocesarJob($this->record))
                ->icon('heroicon-m-arrow-path')
                ->color('success')
                ->requiresConfirmation(),
        ];
    }
}
