<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\Pages;

use App\Filament\Resources\MA\MAUsuariosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMAUsuarios extends ListRecords
{
    protected static string $resource = MAUsuariosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
