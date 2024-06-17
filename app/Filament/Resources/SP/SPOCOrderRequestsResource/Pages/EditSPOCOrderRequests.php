<?php

namespace App\Filament\Resources\SP\SPOCOrderRequestsResource\Pages;

use App\Filament\Resources\SP\SPOCOrderRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSPOCOrderRequests extends EditRecord
{
    protected static string $resource = SPOCOrderRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
