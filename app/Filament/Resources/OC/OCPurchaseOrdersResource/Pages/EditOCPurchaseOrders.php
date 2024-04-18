<?php

namespace App\Filament\Resources\OC\OCPurchaseOrdersResource\Pages;

use App\Filament\Resources\OC\OCPurchaseOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOCPurchaseOrders extends EditRecord
{
    protected static string $resource = OCPurchaseOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
