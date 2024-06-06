<?php

namespace App\Filament\Resources\MA\MACargosResource\Pages;

use App\Filament\Resources\MA\MACargosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMACargos extends ListRecords
{
    protected static string $resource = MACargosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
