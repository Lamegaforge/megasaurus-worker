<?php

namespace App\Actions;

use App\ValueObjects\Card;
use Illuminate\Filesystem\FilesystemManager;

class SaveCardToSpace
{
    public function __construct(
        private FilesystemManager $filesystemManager,
    ) {}

    public function handle(Card $card): void
    {
        $disk = $this->filesystemManager->disk('digitalocean');

        $disk->put(
            'cards/' . $card->name,
            $card->content(),
        );
    }
}
