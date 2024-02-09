<?php

namespace App\Filament\Resources\MA\MASucursalesResource\Pages;

use App\Filament\Resources\MA\MASucursalesResource;
use App\Models\RC\RC_cashier_approvers;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMASucursales extends EditRecord
{
    protected static string $resource = MASucursalesResource::class;

    protected function getActions(): array
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
