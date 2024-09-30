<?php

namespace App\Filament\Resources\ScraperResource\Pages;

use App\Filament\Resources\ScraperResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScraper extends EditRecord
{
    protected static string $resource = ScraperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
