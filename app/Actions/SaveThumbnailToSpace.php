<?php

namespace App\Actions;

use App\ValueObjects\Thumbnail;
use Illuminate\Filesystem\FilesystemManager;

class SaveThumbnailToSpace
{
    public function __construct(
        private FilesystemManager $filesystemManager,
    ) {}

    public function handle(Thumbnail $thumbnail): void
    {
        $disk = $this->filesystemManager->disk('digitalocean');

        $disk->put(
            'thumbnails/' . $thumbnail->id,
            $thumbnail->content(),
        );
    }
}
