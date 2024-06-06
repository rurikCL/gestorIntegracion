<?php

namespace App\Filament\Resources\MA\MACargosPerfilResource\Pages;

use App\Filament\Resources\MA\MACargosPerfilResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMACargosPerfils extends ListRecords
{
    protected static string $resource = MACargosPerfilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
