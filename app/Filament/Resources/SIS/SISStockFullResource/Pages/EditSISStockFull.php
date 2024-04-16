<?php

namespace App\Filament\Resources\SIS\SISStockFullResource\Pages;

use App\Filament\Resources\SIS\SISStockFullResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSISStockFull extends EditRecord
{
    protected static string $resource = SISStockFullResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
