<?php

namespace App\Filament\Resources\MA\MASubOrigenesResource\Pages;

use App\Filament\Resources\MA\MASubOrigenesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMASubOrigenes extends ListRecords
{
    protected static string $resource = MASubOrigenesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
