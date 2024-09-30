<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScraperResource\Pages;
use App\Filament\Resources\ScraperResource\RelationManagers;
use App\Models\Scraper;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScraperResource extends Resource
{
    protected static ?string $model = Scraper::class;

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Designers Leads Scrapper')
                    ->required()->columnSpanFull(),

                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->placeholder('https://example.com')
                    ->required()->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScrapers::route('/'),
            'create' => Pages\CreateScraper::route('/create'),
            'edit' => Pages\EditScraper::route('/{record}/edit'),
        ];
    }
}
