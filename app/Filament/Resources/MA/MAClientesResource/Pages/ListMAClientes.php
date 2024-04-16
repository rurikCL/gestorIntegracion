<?php

namespace App\Filament\Resources\MA\MAClientesResource\Pages;

use App\Filament\Resources\MA\MAClientesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMAClientes extends ListRecords
{
    protected static string $resource = MAClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
