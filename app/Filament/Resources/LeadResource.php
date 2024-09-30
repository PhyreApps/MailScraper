<?php

namespace App\Filament\Resources;

use App\Filament\Exports\LeadExporter;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Domain;
use App\Models\Lead;
use App\Models\Scraper;
use Filament\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scraper.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scraped_from_domain')
                    ->searchable()
                    ->sortable(),
            ])
            ->headerActions([
//                Tables\Actions\ExportAction::make()
//                    ->exporter(LeadExporter::class)
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('scraper_id')
                    ->label('Scraper')
                    ->options(Scraper::all()->pluck('name', 'id')->toArray()),

                Tables\Filters\Filter::make('custom')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['exclude_domains'],
                                function (Builder $query, $excludeDomains) {
                                    // We must exclude email by domain provided in the filter
                                    $query->whereNotIn('email', function ($subquery) use ($excludeDomains) {
                                        $subquery->select('email')
                                            ->from('leads') // Adjust the table name if it's different
                                            ->where(function ($q) use ($excludeDomains) {
                                                foreach ($excludeDomains as $domain) {
                                                    $q->orWhere('email', 'like', '%' . $domain . '%');
                                                }
                                            });
                                    });
                                }
                            );
                    })
                    ->form([
                        Forms\Components\Select::make('exclude_domains')
                            ->label('Exclude domains')
                            ->multiple()
                            ->options(function () {
                                $domains = Domain::all()->pluck('domain', 'domain')->toArray();
                                if (!empty($domains)) {
                                    return $domains;
                                } else {
                                    return [
                                        '0' => 'No domains found'
                                    ];
                                }
                            })
                            ->placeholder('Select a domain')
                            ->required()
                    ])

            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(LeadExporter::class),

                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLeads::route('/'),
//            'create' => Pages\CreateLead::route('/create'),
          //  'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
