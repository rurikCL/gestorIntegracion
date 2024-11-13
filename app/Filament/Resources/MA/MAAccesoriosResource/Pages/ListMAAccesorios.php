<?php

namespace App\Filament\Resources\MA\MAAccesoriosResource\Pages;

use App\Filament\Resources\MA\MAAccesoriosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMAAccesorios extends ListRecords
{
    protected static string $resource = MAAccesoriosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
