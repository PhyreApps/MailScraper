<?php

namespace App\Scraper\Traits;

trait HasLinksScraper
{
    public function scrapeLinks($dom, $fromUrl)
    {

        $mainDomain = parse_url($fromUrl, PHP_URL_HOST);
        $mainDomain = 'http://' . $mainDomain;

        $links = [];
        $getLinks = $dom->getElementsByTagName('a');
        if ($getLinks->length > 0) {
            foreach ($getLinks as $link) {
                $href = $link->getAttribute('href');
                if (filter_var($href, FILTER_VALIDATE_URL)) {
                    $links[] = $href;
                } else {
                    if (!str_contains($href, 'http://')) {
                        $href = $mainDomain . $href;
                    } elseif (!str_contains($href, 'https://')) {
                        $href = $mainDomain . $href;
                    }
                    if (filter_var($href, FILTER_VALIDATE_URL)) {
                        $links[] = $href;
                    }
                }
            }
        }

        return $links;
    }
}
