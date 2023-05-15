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
            $thumbnail->name,
            $thumbnail->content(),
        );
    }
}
