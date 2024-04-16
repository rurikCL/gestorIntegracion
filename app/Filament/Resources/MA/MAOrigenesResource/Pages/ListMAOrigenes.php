<?php

namespace App\Filament\Resources\MA\MAOrigenesResource\Pages;

use App\Filament\Resources\MA\MAOrigenesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMAOrigenes extends ListRecords
{
    protected static string $resource = MAOrigenesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
