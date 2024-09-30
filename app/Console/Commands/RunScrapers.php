<?php

namespace App\Console\Commands;

use App\Jobs\RunScraper;
use App\Models\Scraper;
use App\Scraper\MainPageScraper;
use Illuminate\Console\Command;

class RunScrapers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run-scrapers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $findScraper = Scraper::where('status', 'QUEUED')->first();
        if ($findScraper) {
            $this->info('Running scraper: ' . $findScraper->name);

            $validUrl = filter_var($findScraper->url, FILTER_VALIDATE_URL);
            if (!$validUrl) {
                $this->error('Invalid URL: ' . $findScraper->url);
                $findScraper->status = 'FAILED';
                $findScraper->save();
                return;
            }

            $mpScraper = new MainPageScraper($findScraper->url);
            $mpScraper->scrape();

        }
    }
}
