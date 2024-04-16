<?php

namespace App\Filament\Resources\TK\TKCategoriasTicketResource\Pages;

use App\Filament\Resources\TK\TKCategoriasTicketResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTKCategoriasTicket extends EditRecord
{
    protected static string $resource = TKCategoriasTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
