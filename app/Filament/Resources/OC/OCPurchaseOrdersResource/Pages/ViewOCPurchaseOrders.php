<?php

namespace App\Filament\Resources\OC\OCPurchaseOrdersResource\Pages;

use App\Filament\Resources\OC\OCPurchaseOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOCPurchaseOrders extends ViewRecord
{
    protected static string $resource = OCPurchaseOrdersResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
