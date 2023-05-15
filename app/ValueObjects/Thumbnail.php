<?php

namespace App\ValueObjects;

readonly final class Thumbnail
{
    public function __construct(
        public string $name,
        public string $url,
    ) {}

    public function content()
    {
        return file_get_contents($this->url);
    }
}
