<?php

namespace App\Filament\Resources\MA\MACargosPerfilResource\Pages;

use App\Filament\Resources\MA\MACargosPerfilResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMACargosPerfil extends EditRecord
{
    protected static string $resource = MACargosPerfilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
