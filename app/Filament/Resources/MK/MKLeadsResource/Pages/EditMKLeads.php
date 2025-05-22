<?php

namespace App\Filament\Resources\MK\MKLeadsResource\Pages;

use App\Filament\Resources\MK\MKLeadsResource;
use Filament\Notifications\Notification;
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

    protected function afterSave(){
        if ($this->record->wasChanged('Email')) {
            $this->record->LandBotID = 0;
            $this->record->save();

            Notification::make()
                ->title('Cliente corregido')
                ->info()
                ->send();
        }


        return true;
    }
}
