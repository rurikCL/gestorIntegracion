<?php

namespace App\Filament\Resources\MA\MAClientesResource\Pages;

use App\Filament\Resources\MA\MAClientesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMAClientes extends EditRecord
{
    protected static string $resource = MAClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
