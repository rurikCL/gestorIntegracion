<?php

namespace App\Filament\Resources\RevisionClientesResource\Pages;

use App\Filament\Resources\RevisionClientesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRevisionClientes extends EditRecord
{
    protected static string $resource = RevisionClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
