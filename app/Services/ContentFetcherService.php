<?php

namespace App\Services;

use RuntimeException;

/** 
 * Just to be able to mock the function.
 */
class ContentFetcherService
{
    public function fetch(string $url): string
    {
        /**
         * Sometimes Twitch CDN is capricious.
         */
        return retry(2, function () use ($url) {
            return $this->retrieveUrlContent($url);
        }, 1000);
    }

    private function retrieveUrlContent(string $url): string
    {
        $content = file_get_contents($url);

        if ($content === false) {
            throw new RuntimeException('Unable to retrieve content : ' . $url);
        }

        return $content;
    }
}
