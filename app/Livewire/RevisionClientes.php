<?php

namespace App\Livewire;

use App\Models\MA\MA_Clientes;
use App\Models\Rutas;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use function Psl\Math\max_by;

class RevisionClientes extends Component implements HasTable, HasForms
{

    use InteractsWithTable, InteractsWithForms;

    public $clientes;
    public function render()
    {
        return view('livewire.revision-clientes');
    }

    public function table(Table $table) : Table
    {
        return $table
            ->query(
               MA_Clientes::duplicados(1)
            )
            ->columns([
                TextColumn::make('Rut')->sortable()->searchable(),
                TextColumn::make('countClientes')
                    ->default(fn($record) => $record->clientes->count() ?? 0)
                    ->label('Duplicados'),
            ])
            ->actions([
                //
            ])
            ->filters([

            ]);

    }

    public function mount()
    {

    }
}
