<?php

namespace App\Filament\Exports;

use App\Models\Lead;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class LeadExporter extends Exporter
{
    protected static ?string $model = Lead::class;

    public static function getColumns(): array
    {
        return [
//            ExportColumn::make('id')
//                ->label('ID'),
            ExportColumn::make('email'),
            ExportColumn::make('first_name'),
//            ExportColumn::make('last_name'),
            ExportColumn::make('phone'),
//            ExportColumn::make('address'),
//            ExportColumn::make('city'),
//            ExportColumn::make('state'),
//            ExportColumn::make('zip'),
//            ExportColumn::make('country'),
//            ExportColumn::make('scraper_id'),
            ExportColumn::make('scraped_from_url'),
            ExportColumn::make('scraped_from_domain'),
//            ExportColumn::make('created_at'),
//            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your lead export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
