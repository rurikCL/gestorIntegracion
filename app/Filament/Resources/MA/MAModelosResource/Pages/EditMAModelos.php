<?php

namespace App\Filament\Resources\MA\MAModelosResource\Pages;

use App\Filament\Resources\MA\MAModelosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMAModelos extends EditRecord
{
    protected static string $resource = MAModelosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
