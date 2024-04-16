<?php

namespace App\Filament\Resources\API\ApiProveedoresResource\Pages;

use App\Filament\Resources\API\ApiProveedoresResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiProveedores extends ListRecords
{
    protected static string $resource = ApiProveedoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
