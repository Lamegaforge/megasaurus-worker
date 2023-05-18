<?php

namespace App\ValueObjects;

use App\Services\ContentFetcherService;

readonly final class Thumbnail
{
    public function __construct(
        public string $name,
        public string $url,
    ) {}

    public function content(): string
    {
        return app(ContentFetcherService::class)->fetch($this->url);
    }
}
