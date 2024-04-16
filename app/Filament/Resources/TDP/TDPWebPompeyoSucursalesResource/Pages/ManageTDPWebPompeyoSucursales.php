<?php

namespace App\Filament\Resources\TDP\TDPWebPompeyoSucursalesResource\Pages;

use App\Filament\Resources\TDP\TDPWebPompeyoSucursalesResource;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use HubSpot\Http\Auth;

class ManageTDPWebPompeyoSucursales extends ManageRecords
{
    protected static string $resource = TDPWebPompeyoSucursalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
                $data['EventoCreacionID'] = 1;
                $data['UsuarioCreacionID'] = \Illuminate\Support\Facades\Auth::user()->id;

                return $data;
            }),
        ];
    }
}
