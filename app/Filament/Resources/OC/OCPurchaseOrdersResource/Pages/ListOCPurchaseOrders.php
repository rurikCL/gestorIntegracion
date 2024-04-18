<?php

namespace App\Filament\Resources\OC\OCPurchaseOrdersResource\Pages;

use App\Filament\Resources\OC\OCPurchaseOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOCPurchaseOrders extends ListRecords
{
    protected static string $resource = OCPurchaseOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
