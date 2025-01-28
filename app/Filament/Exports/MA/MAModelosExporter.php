<?php

namespace App\Filament\Exports\MA;

use App\Models\MA\MA_Modelos;
use App\Models\MA\MAModelos;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MAModelosExporter extends Exporter
{
    protected static ?string $model = MA_Modelos::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('ID'),
            ExportColumn::make('Modelo'),
            ExportColumn::make('marca.Marca'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your m a modelos export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
