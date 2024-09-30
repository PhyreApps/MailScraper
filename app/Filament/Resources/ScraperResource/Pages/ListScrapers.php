<?php

namespace App\Filament\Resources\ScraperResource\Pages;

use App\Filament\Resources\ScraperResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScrapers extends ListRecords
{
    protected static string $resource = ScraperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
