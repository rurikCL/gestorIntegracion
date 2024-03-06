<?php

namespace App\Filament\Resources\TK\TKTicketsManagerResource\Pages;

use App\Filament\Resources\TK\TKTicketsManagerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTKTicketsManager extends EditRecord
{
    protected static string $resource = TKTicketsManagerResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
