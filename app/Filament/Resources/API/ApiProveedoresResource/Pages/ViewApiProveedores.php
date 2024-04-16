<?php

namespace App\Filament\Resources\API\ApiProveedoresResource\Pages;

use App\Filament\Resources\API\ApiProveedoresResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApiProveedores extends ViewRecord
{
    protected static string $resource = ApiProveedoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
