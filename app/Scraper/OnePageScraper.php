<?php

namespace App\Scraper;

use App\Models\Lead;
use App\Scraper\Traits\HasDomainScraper;
use App\Scraper\Traits\HasEmailScraper;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use GuzzleHttp\Client;

class OnePageScraper
{
    use HasEmailScraper;
    use HasDomainScraper;

    public $scraperId;
    public $url;

    public function __construct($scraperId, $url)
    {
        $this->scraperId = $scraperId;
        $this->url = $url;
    }

    public function scrape()
    {
        $leads = [];
        $domains = [];

        $content = file_get_contents("http://localhost:3000/api/article?full-content=yes&url=" . $this->url);
        $json = json_decode($content, true);
        if (!isset($json['fullContent'])) {
            return;
        }

        $fullContent = $json['fullContent'];

        $dom = new \DOMDocument();
        @$dom->loadHTML($fullContent);

        try {
            $scrapEmails = $this->scrapeEmails($dom);
            $leads = array_merge($leads, $scrapEmails);
        } catch (\Exception $e) {
            // error
        }

        if (!empty($leads)) {
            // save leads
            foreach ($leads as $lead) {
                $findLead = Lead::where('email', $lead['email'])->where('scraper_id', $this->scraperId)->first();
                if (!$findLead) {
                    $newLead = new Lead();
                    $newLead->scraper_id = $this->scraperId;
                    $newLead->email = $lead['email'];
                    $newLead->scraped_from_url = $lead['url'];
                    $newLead->scraped_from_domain = $lead['domain'];
                    $newLead->save();
                }
            }
        }

        try {
            $scrapDomains = $this->scrapeDomains($dom);
            $domains = array_merge($domains, $scrapDomains);
        } catch (\Exception $e) {
            // error
        }

        return [
            'leads' => $leads,
            'domains' => $domains,
        ];
    }
}
