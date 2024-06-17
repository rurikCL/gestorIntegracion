<?php

namespace App\Filament\Resources\SP\SPOCOrderRequestsResource\Pages;

use App\Filament\Resources\SP\SPOCOrderRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSPOCOrderRequests extends ListRecords
{
    protected static string $resource = SPOCOrderRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
