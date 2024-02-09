<?php

namespace App\Filament\Resources\SIS\SISSolicitudesArbolAprobacionesResource\Pages;

use App\Filament\Resources\SIS\SISSolicitudesArbolAprobacionesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSISSolicitudesArbolAprobaciones extends ListRecords
{
    protected static string $resource = SISSolicitudesArbolAprobacionesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
