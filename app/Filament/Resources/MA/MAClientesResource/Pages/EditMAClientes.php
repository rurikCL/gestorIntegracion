<?php

namespace App\Filament\Resources\MA\MAClientesResource\Pages;

use App\Filament\Resources\MA\MAClientesResource;
use Filament\Notifications\Notification;
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

    protected function afterSave()
    {
        if ($this->record->wasChanged('Email')) {
            $this->record->Correccion = 0;
            $this->record->save();

            Notification::make()
                ->title('Cliente corregido')
                ->info()
                ->send();
        }


        return true;
    }
}
