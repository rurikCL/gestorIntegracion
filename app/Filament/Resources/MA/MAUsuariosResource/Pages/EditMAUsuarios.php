<?php

namespace App\Filament\Resources\MA\MAUsuariosResource\Pages;

use App\Filament\Resources\MA\MAUsuariosResource;
use App\Models\MA\MA_Usuarios;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditMAUsuarios extends EditRecord
{
    protected static string $resource = MAUsuariosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('link')
                ->label("Link Roma")
                ->url(fn () => "https://apps1.pompeyo.cl/?id=" .$this->record->ID ."&token=6461433ef90325a215111f2af1464b2d09f2ba23", true)
                ->icon('heroicon-o-link')
                ->color('success')
                ->visible(Auth::user()->isAdmin()),
        ];
    }
}
