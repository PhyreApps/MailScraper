<?php

namespace App\Console\Commands;

use App\Jobs\RunScraper;
use App\Models\Domain;
use App\Models\Lead;
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
     *
     * Execute the console command.
     */
    public function handle()
    {
        $findScraper = Scraper::where('status', 'QUEUED')->first();
        if ($findScraper) {
            $this->info('Running scraper: ' . $findScraper->name);

            $findScraper->status = 'RUNNING';
            $findScraper->save();

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

                    // Level 3 of scraping
                    if (isset($result['links'])) {
                        foreach ($result['links'] as $link) {
                            $opScraper = new OnePageScraper($findScraper->id, $link);
                            $result = $opScraper->scrape();
                            $this->saveResult($findScraper->id, $result);
                        }
                    }

                }
            }

            if (isset($result['paginationLinks'])) {
                foreach ($result['paginationLinks'] as $link) {
                   $opScraper = new OnePageScraper($findScraper->id, $link);
                   $result = $opScraper->scrape();
                   $this->saveResult($findScraper->id, $result);
                }
            }

            $findDomains = Domain::where('scraper_id', $findScraper->id)->get();
            if ($findDomains->count() > 0) {
                foreach ($findDomains as $domain) {
                    $mpScraper = new OnePageScraper($findScraper->id,'http://' . $domain->domain);
                    $result = $mpScraper->scrape();
                    $this->saveResult($findScraper->id, $result);
                }
            }

            $findScraper->status = 'COMPLETED';
            $findScraper->save();
        }
    }

    public function saveResult($scraperId, $result)
    {
        $findScraper = Scraper::find($scraperId);
        if (isset($result['leads'])) {
            // save leads
            foreach ($result['leads'] as $lead) {
                $findLead = Lead::where('email', $lead['email'])->where('scraper_id', $scraperId)->first();
                if (!$findLead) {
                    $newLead = new Lead();
                    $newLead->scraper_id = $scraperId;
                    $newLead->first_name = $lead['name'];
                    $newLead->email = $lead['email'];
                    $newLead->scraped_from_url = $lead['url'];
                    $newLead->scraped_from_domain = $lead['domain'];
                    $newLead->user_id = $findScraper->user_id;
                    $newLead->save();
                }
            }
        }

        if (isset($result['domains'])) {
            foreach ($result['domains'] as $domain) {
                $findDomain = Domain::where('domain', $domain)->where('scraper_id', $scraperId)->first();
                if (!$findDomain) {
                    $newDomain = new Domain();
                    $newDomain->scraper_id = $scraperId;
                    $newDomain->domain = $domain;
                    $newDomain->user_id = $findScraper->user_id;
                    $newDomain->save();
                }
            }
        }
    }
}
