<?php

namespace App\Filament\Resources\MA\MASucursalesResource\Pages;

use App\Filament\Resources\MA\MASucursalesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMASucursales extends EditRecord
{
    protected static string $resource = MASucursalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }



    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
