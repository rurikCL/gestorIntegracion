<?php

namespace App\Filament\Resources\TK\TKTicketsManagerResource\Pages;

use App\Filament\Resources\TK\TKTicketsManagerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTKTicketsManagers extends ListRecords
{
    protected static string $resource = TKTicketsManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
