<?php

namespace App\Filament\Resources\VT\VTVentasResource\Pages;

use App\Filament\Resources\VT\VTVentasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVTVentas extends ListRecords
{
    protected static string $resource = VTVentasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
