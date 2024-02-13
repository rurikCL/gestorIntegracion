<?php

namespace App\Filament\Resources\SP\OCProductosResource\Pages;

use App\Filament\Resources\SP\OCProductosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOCProductos extends ListRecords
{
    protected static string $resource = OCProductosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
