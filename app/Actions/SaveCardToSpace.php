<?php

namespace App\Actions;

use App\ValueObjects\Card;
use Illuminate\Filesystem\FilesystemManager;
use App\Models\Game;

class SaveCardToSpace
{
    public function __construct(
        private FilesystemManager $filesystemManager,
    ) {}

    public function handle(Game $game, Card $card): void
    {
        $disk = $this->filesystemManager->disk('digitalocean');

        $disk->put(
            'cards/' . $game->uuid,
            $card->content(),
        );
    }
}
