<?php

namespace App\Scraper\Traits;

trait HasEmailScraper
{
    public function scrapeEmails($dom)
    {

//        $emails = [];
//        foreach ($dom->getElementsByTagName('a') as $node) {
//            $href = $node->getAttribute('href');
//            if (str_contains($href, 'mailto:')) {
//                $email = str_replace('mailto:', '', $href);
//              //  $emails[] = $email;
//            }
//        }

        $emails = [];
        $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        preg_match_all($emailPattern, $dom->saveHtml(), $emails);

        if (isset($emails[0])) {
            $emails = $emails[0];
        }

        $emails = array_unique($emails);

        $lead = [];
        if (!empty($emails)) {
            foreach ($emails as $email) {
                $name = '';
                if (str_contains($email, '@')) {
                    $name = explode('@', $email)[0];
                }
                $checkEmailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);
                if (!$checkEmailIsValid) {
                    continue;
                }
                $emailProvider = explode('@', $email)[1];
                $checkEmailProviderIsValid = checkdnsrr($emailProvider, 'MX');
                if (!$checkEmailProviderIsValid) {
                    continue;
                }
                $domain = parse_url($this->url, PHP_URL_HOST);
                $domain = str_replace('www.', '', $domain);
                $domain = trim($domain);

                $lead[] = [
                    'name'=> ucwords($name),
                    'email' => $email,
                    'url' => $this->url,
                    'domain' => $domain
                ];
            }
        }

        return $lead;
    }
}
