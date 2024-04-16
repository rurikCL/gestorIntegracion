<?php

namespace App\Filament\Resources\SIS\SISStockFullResource\Pages;

use App\Filament\Resources\SIS\SISStockFullResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSISStockFulls extends ListRecords
{
    protected static string $resource = SISStockFullResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
