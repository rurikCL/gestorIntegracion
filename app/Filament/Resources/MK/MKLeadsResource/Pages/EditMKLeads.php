<?php

namespace App\Filament\Resources\MK\MKLeadsResource\Pages;

use App\Filament\Resources\MK\MKLeadsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMKLeads extends EditRecord
{
    protected static string $resource = MKLeadsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
