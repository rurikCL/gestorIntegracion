<?php

namespace App\Filament\Resources\SIS\SISSolicitudesArbolAprobacionesResource\Pages;

use App\Filament\Resources\SIS\SISSolicitudesArbolAprobacionesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSISSolicitudesArbolAprobaciones extends EditRecord
{
    protected static string $resource = SISSolicitudesArbolAprobacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
