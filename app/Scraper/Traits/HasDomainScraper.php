<?php

namespace App\Scraper\Traits;

trait HasDomainScraper
{
    public function scrapeDomains($dom)
    {
        $links = [];
        $getLinks = $dom->find('a');
        if (!empty($getLinks)) {
            foreach ($getLinks as $link) {
                $href = $link->getAttribute('href');
                if (filter_var($href, FILTER_VALIDATE_URL)) {
                    $links[] = $href;
                }
            }
            $links = array_unique($links);
        }

        $domains = [];

        if (!empty($links)) {
            foreach ($links as $link) {
                $domain = parse_url($link, PHP_URL_HOST);
                $domain = str_replace('www.', '', $domain);
                $domain = trim($domain);
                if (empty($domain)) {
                    continue;
                }
                $domains[] = $domain;
            }
            $domains = array_unique($domains);
        }

        return $domains;

    }
}
