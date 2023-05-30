<?php

namespace App\Actions;

use App\ValueObjects\Card;
use App\ValueObjects\ExternalId;
use Illuminate\Filesystem\FilesystemManager;

class SaveCardToSpace
{
    public function __construct(
        private FilesystemManager $filesystemManager,
    ) {}

    public function handle(ExternalId $externalId, Card $card): void
    {
        $disk = $this->filesystemManager->disk('digitalocean');

        $disk->put(
            'cards/' . $externalId,
            $card->content(),
        );
    }
}
