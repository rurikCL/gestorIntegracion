<?php

namespace App\Filament\Resources\MA\MASubOrigenesResource\Pages;

use App\Filament\Resources\MA\MASubOrigenesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMASubOrigenes extends EditRecord
{
    protected static string $resource = MASubOrigenesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
