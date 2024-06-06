<?php

namespace App\Filament\Resources\MA\MAPerfilesResource\Pages;

use App\Filament\Resources\MA\MAPerfilesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMAPerfiles extends ListRecords
{
    protected static string $resource = MAPerfilesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
