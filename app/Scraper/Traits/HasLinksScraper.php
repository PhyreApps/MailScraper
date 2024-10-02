<?php

namespace App\Scraper\Traits;

use voku\helper\HtmlDomParser;

trait HasLinksScraper
{
    public function scrapeLinks($dom, $fromUrl)
    {
        $mainDomain = parse_url($fromUrl, PHP_URL_HOST);
        $mainDomain = 'http://' . $mainDomain;

        $links = [];
        $getLinks = $dom->find('a');

        if (!empty($getLinks)) {
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

        $links = array_unique($links);

        return $links;
    }
}
