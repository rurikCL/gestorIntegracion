<?php

namespace App\Filament\Resources\MA\MASucursalesResource\Pages;

use App\Filament\Resources\MA\MASucursalesResource;
use App\Models\RC\RC_cashier_approvers;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;


class CreateMASucursales extends CreateRecord
{
    protected static string $resource = MASucursalesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['FechaCreacion'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['EventoCreacionID'] = 1;
        $data['UsuarioCreacionID'] = Auth::user()->id;

        /*foreach ($data["Niveles"] as $key => $value) {
            if ($value["level"] <> $data["level"]) {
                $resultado = RC_cashier_approvers::updateOrCreate(
                    [
                        "branch_office_id" => $data["branch_office_id"],
                        "user_id" => $data["user_id"],
                        "level" => $value["level"],
                    ],
                    [
                        "FechaCreacion" => Carbon::now()->format('Y-m-d H:i:s'),
                        "EventoCreacionID" => 1,
                        "UsuarioCreacionID" => 1,
                        "branch_office_id" => $data["branch_office_id"],
                        "user_id" => $data["user_id"],
                        "level" => $value["level"],
                        "min" => $value["min"],
                        "max" => $value["max"],
                    ]
                );

                if ($resultado) {
                    Notification::make()
                        ->title('Nivel Guardado')
                        ->success()
                        ->send();
                }
            }
        }*/
        return $data;
    }
}
