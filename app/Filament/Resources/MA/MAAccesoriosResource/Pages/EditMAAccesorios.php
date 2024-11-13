<?php

namespace App\Filament\Resources\MA\MAAccesoriosResource\Pages;

use App\Filament\Resources\MA\MAAccesoriosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMAAccesorios extends EditRecord
{
    protected static string $resource = MAAccesoriosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
