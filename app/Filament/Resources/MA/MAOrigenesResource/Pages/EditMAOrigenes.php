<?php

namespace App\Filament\Resources\MA\MAOrigenesResource\Pages;

use App\Filament\Resources\MA\MAOrigenesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMAOrigenes extends EditRecord
{
    protected static string $resource = MAOrigenesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
