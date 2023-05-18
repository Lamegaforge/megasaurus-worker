<?php

namespace App\Services;

use RuntimeException;

/** 
 * just to be able to mock the function
 */
class ContentFetcherService
{
    public function fetch(string $url): string
    {
        $content = file_get_contents($url);

        if ($content === false) {
            throw new RuntimeException('Unable to retrieve content : ' . $url);
        }

        return $content;
    }
}
