<?php

namespace App\Filament\Resources\TDP\TDPWebPompeyoSucursalesResource\Pages;

use App\Filament\Resources\TDP\TDPWebPompeyoSucursalesResource;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTDPWebPompeyoSucursales extends ManageRecords
{
    protected static string $resource = TDPWebPompeyoSucursalesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                $data['EventoCreacionID'] = 1;
                $data['UsuarioCreacionID'] = 1;

                return $data;
            }),
        ];
    }
}
