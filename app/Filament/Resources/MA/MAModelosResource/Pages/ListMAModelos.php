<?php

namespace App\Filament\Resources\MA\MAModelosResource\Pages;

use App\Filament\Resources\MA\MAModelosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMAModelos extends ListRecords
{
    protected static string $resource = MAModelosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
