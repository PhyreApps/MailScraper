<?php

namespace Tests\Unit;

use App\Scraper\Traits\HasEmailScraper;
use PHPUnit\Framework\TestCase;

class EmailScraperTest extends TestCase
{
    use HasEmailScraper;

    /**
     * A basic test example.
     */
    public function test_scraper(): void
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('<div class="mt-16 lg:mt-10" data-v-0a876191=""><p class="font-bold font-display" data-v-0a876191="">307 West High Street</p><p class="font-bold font-display" data-v-0a876191="">Elizabethtown, PA 17022</p><p class="font-bold font-display" data-v-0a876191=""><a href="mailto:howdy@inovat.com" data-v-0a876191="">howdy@inovat.com</a></p><p class="font-bold font-display" data-v-0a876191=""><a href="tel:717.367.5446" data-v-0a876191="">717.367.5446</a></p></div>');

        $scrapEmails = $this->scrapeEmails($dom);

        $this->assertIsArray($scrapEmails);
        $this->assertCount(1, $scrapEmails);

        $this->assertEquals('Howdy', $scrapEmails[0]['name']);
        $this->assertEquals('howdy@inovat.com', $scrapEmails[0]['email']);
    }
}
