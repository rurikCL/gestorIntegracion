<?php

namespace App\Filament\Resources\API\ApiSolicitudesResource\Pages;

use App\Filament\Resources\API\ApiSolicitudesResource;
use App\Filament\Resources\API\APISolicitudesResource\Widgets\StatsOverview;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiSolicitudes extends ListRecords
{
    protected static string $resource = ApiSolicitudesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets() : array
    {
        return [StatsOverview::class];
    }
}
