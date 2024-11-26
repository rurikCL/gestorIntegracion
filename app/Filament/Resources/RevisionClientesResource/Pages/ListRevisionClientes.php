<?php

namespace App\Filament\Resources\RevisionClientesResource\Pages;

use App\Filament\Resources\RevisionClientesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRevisionClientes extends ListRecords
{
    protected static string $resource = RevisionClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
