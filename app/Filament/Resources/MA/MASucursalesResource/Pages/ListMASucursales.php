<?php

namespace App\Filament\Resources\MA\MASucursalesResource\Pages;

use App\Filament\Resources\MA\MASucursalesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMASucursales extends ListRecords
{
    protected static string $resource = MASucursalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
