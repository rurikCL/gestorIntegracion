<?php

namespace App\Filament\Resources\MK\MKLeadsResource\Pages;

use App\Filament\Resources\MK\MKLeadsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMKLeads extends ListRecords
{
    protected static string $resource = MKLeadsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
