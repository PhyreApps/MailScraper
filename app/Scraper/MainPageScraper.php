<?php

namespace App\Scraper;

use App\Models\Scraper;
use App\Scraper\Traits\HasDomainScraper;
use App\Scraper\Traits\HasEmailScraper;
use App\Scraper\Traits\HasLinksScraper;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use GuzzleHttp\Client;

class MainPageScraper
{
    use HasEmailScraper;
    use HasDomainScraper;
    use HasLinksScraper;

    public $scraperId;

    public function __construct($scraperId)
    {
        $this->scraperId = $scraperId;
    }

    public function scrape()
    {
        $findScraper = Scraper::find($this->scraperId);

        try {
            $content  = file_get_contents("http://localhost:3000/api/article?full-content=yes&url=" .$findScraper->url);
            $json = json_decode($content, true);
        } catch (\Exception $e) {
            // error
            return;
        }

        if (!isset($json['fullContent'])) {
            return;
        }

        $fullContent = $json['fullContent'];

        $dom = new \DOMDocument();
        @$dom->loadHTML($fullContent);

        $links = [];
        try {
            $scrapLinks = $this->scrapeLinks($dom, $findScraper->url);
            $links = array_merge($links, $scrapLinks);
        } catch (\Exception $e) {
            // error
        }

        // Try to detect pagination links
        $paginationLinks = [];
        if (!empty($links)) {
            foreach ($links as $link) {
                if (str_contains($link, 'page=')) {
                    $paginationLinks[] = $link;
                } else if (str_contains($link, '?page=')) {
                    $paginationLinks[] = $link;
                }
            }
        }

        $domains = [];
        try {
            $scrapDomains = $this->scrapeDomains($dom);
            $domains = array_merge($domains, $scrapDomains);
        } catch (\Exception $e) {
            // error
        }

        $leads = [];
        try {
            $scrapEmails = $this->scrapeEmails($dom);
            $leads = array_merge($leads, $scrapEmails);
        } catch (\Exception $e) {
            // error
        }

        return [
            'paginationLinks' => $paginationLinks,
            'links' => $links,
            'domains' => $domains,
            'leads'=>$leads
        ];

    }

}
