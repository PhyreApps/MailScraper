<?php

namespace App\Scraper;

use App\Models\Lead;
use App\Scraper\Traits\HasDomainScraper;
use App\Scraper\Traits\HasEmailScraper;
use App\Scraper\Traits\HasLinksScraper;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use GuzzleHttp\Client;

class OnePageScraper
{
    use HasLinksScraper;
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

        $url = "http://parsecat.com:3000/api/article?full-content=yes&url=" .$this->url;

//        echo 'Scraping: ' . $url . PHP_EOL;

        try {
            $content  = file_get_contents($url);
            $json = json_decode($content, true);
        } catch (\Exception $e) {
            // error
            return;
        }

        if (!isset($json['fullContent'])) {
            return;
        }


        $fullContent = $json['fullContent'];

        $dom = str_get_html($fullContent);

        try {
            $scrapEmails = $this->scrapeEmails($dom);
            $leads = array_merge($leads, $scrapEmails);
        } catch (\Exception $e) {
            // error
        }
        try {
            $scrapDomains = $this->scrapeDomains($dom);
            $domains = array_merge($domains, $scrapDomains);
        } catch (\Exception $e) {
            // error
        }

        $links = [];
        try {
            $scrapLinks = $this->scrapeLinks($dom, $this->url);
            $links = array_merge($links, $scrapLinks);
        } catch (\Exception $e) {
            // error
        }

        return [
            'leads' => $leads,
            'domains' => $domains,
            'links' => $links,
        ];
    }
}
