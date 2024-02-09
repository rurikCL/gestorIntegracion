<?php

namespace App\Http\Livewire;

use DragonCode\Support\Helpers\Boolean;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class UserTable extends DataTableComponent
{
    protected $model = User::class;

    public array $bulkActions = [
        'changeActive' => 'Activar',
        'changeDeactivate' => 'Desactivar',
    ];

    public function changeActive()
    {
        foreach ( $this->getSelected() as $item){
            User::where('id', $item)->update(['state' => 1]);
        }

        $this->clearSelected();
    }

    public function changeDeactivate()
    {
        foreach ( $this->getSelected() as $item){
            User::where('id', $item)->update(['state' => 0]);
        }

        $this->clearSelected();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable()
                ->sortable(),
            Column::make("Nombre", "name")
                ->searchable()
                ->sortable(),
            Column::make("Email", "email")
                ->searchable()
                ->sortable(),
            Column::make("Marca", "management.Gerencia")
                ->searchable()
                ->sortable(),
            BooleanColumn::make("Estado", "state")
        ];
    }
}
