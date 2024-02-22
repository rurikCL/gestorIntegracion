<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\Pages;

use App\Filament\Resources\MA\MAUsuariosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMAUsuarios extends EditRecord
{
    protected static string $resource = MAUsuariosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
