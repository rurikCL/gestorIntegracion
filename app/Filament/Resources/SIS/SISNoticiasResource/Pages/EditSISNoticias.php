<?php

namespace App\Filament\Resources\SIS\SISNoticiasResource\Pages;

use App\Filament\Resources\SIS\SISNoticiasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSISNoticias extends EditRecord
{
    protected static string $resource = SISNoticiasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
