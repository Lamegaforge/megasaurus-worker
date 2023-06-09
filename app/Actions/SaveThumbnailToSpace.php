<?php

namespace App\Actions;

use App\ValueObjects\Thumbnail;
use Illuminate\Filesystem\FilesystemManager;
use Domain\Models\Clip;

class SaveThumbnailToSpace
{
    public function __construct(
        private FilesystemManager $filesystemManager,
    ) {}

    public function handle(Clip $clip, Thumbnail $thumbnail): void
    {
        $disk = $this->filesystemManager->disk('digitalocean');

        $disk->put(
            'thumbnails/' . $clip->uuid,
            $thumbnail->content(),
        );
    }
}
