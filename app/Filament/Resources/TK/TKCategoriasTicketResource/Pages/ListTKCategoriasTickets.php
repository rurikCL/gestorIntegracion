<?php

namespace App\Filament\Resources\TK\TKCategoriasTicketResource\Pages;

use App\Filament\Resources\TK\TKCategoriasTicketResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTKCategoriasTickets extends ListRecords
{
    protected static string $resource = TKCategoriasTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
