<?php

namespace App\Scraper\Traits;

trait HasEmailScraper
{
    public function scrapeEmails($dom)
    {
        $content = $dom->textContent;

        $emails = [];
        $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        preg_match_all($emailPattern, $content, $emails);

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
                $lead[] = [
                    'name'=> ucwords($name),
                    'email' => $email,
                    'url' => $this->url,
                    'domain' => parse_url($this->url, PHP_URL_HOST)
                ];
            }
        }

        return $lead;
    }
}
