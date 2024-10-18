<?php

namespace App\Filament\Resources\SP\SPProvidersResource\Pages;

use App\Filament\Resources\SP\SPProvidersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSPProviders extends ListRecords
{
    protected static string $resource = SPProvidersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
