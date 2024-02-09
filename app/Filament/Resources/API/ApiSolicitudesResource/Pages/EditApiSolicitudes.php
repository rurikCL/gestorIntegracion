<?php

namespace App\Filament\Resources\API\ApiSolicitudesResource\Pages;

use App\Filament\Resources\API\ApiSolicitudesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiSolicitudes extends EditRecord
{
    protected static string $resource = ApiSolicitudesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
