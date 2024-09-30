<?php

namespace App\Console\Commands;

use App\Jobs\RunScraper;
use App\Models\Domain;
use App\Models\Scraper;
use App\Scraper\MainPageScraper;
use App\Scraper\OnePageScraper;
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

            $mpScraper = new MainPageScraper($findScraper->id);
            $result = $mpScraper->scrape();
            $this->saveResult($findScraper->id, $result);

            if (isset($result['links'])) {
                foreach ($result['links'] as $link) {
                    $opScraper = new OnePageScraper($findScraper->id, $link);
                    $result = $opScraper->scrape();
                    $this->saveResult($findScraper->id, $result);
                }
            }

            if (isset($result['paginationLinks'])) {
                foreach ($result['paginationLinks'] as $link) {
                   $opScraper = new OnePageScraper($findScraper->id, $link);
                   $result = $opScraper->scrape();
                   $this->saveResult($findScraper->id, $result);
                }
            }

        }
    }

    public function saveResult($scraperId, $result)
    {
        if (isset($result['domains'])) {
            foreach ($result['domains'] as $domain) {
                $findDomain = Domain::where('domain', $domain)->where('scraper_id', $scraperId)->first();
                if (!$findDomain) {
                    $newDomain = new Domain();
                    $newDomain->scraper_id = $scraperId;
                    $newDomain->domain = $domain;
                    $newDomain->save();
                }
            }
        }
    }
}
