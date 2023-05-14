<?php

namespace App\Actions;

use App\Models\Clip;
use App\Models\Author;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\FetchedAuthor;

class StoreFetchedClip
{
    public function handle(FetchedClip $fetchedClip): Clip
    {
        $author = $this->retrieveOrCreateAuthor($fetchedClip->author);

        $clip = $this->makeClip($fetchedClip);

        $clip->author()->associate($author);

        $clip->save();

        return $clip;
    }

    private function retrieveOrCreateAuthor(FetchedAuthor $fetchedAuthor): Author
    {
        return Author::firstOrCreate([
            'external_id' => $fetchedAuthor->external_id,
        ], [
            'name' => $fetchedAuthor->name,
        ]);
    }

    private function makeClip(FetchedClip $fetchedClip): Clip
    {
        return Clip::make([
            'external_id' => $fetchedClip->external_id,
            'url' => $fetchedClip->url,
            'title' => $fetchedClip->title,
            'views' => $fetchedClip->views,
            'published_at' => $fetchedClip->published_at,
        ]);
    }
}
