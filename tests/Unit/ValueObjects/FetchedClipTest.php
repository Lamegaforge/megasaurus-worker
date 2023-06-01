<?php

namespace Tests\Unit\ValueObjects;

use Tests\TestCase;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\FetchedAuthor;
use App\ValueObjects\Thumbnail;
use Tests\Stubs\TwitchStub;
use Carbon\Carbon;

class FetchedClipTest extends TestCase
{
    /**
     * @test
     */
    public function it_able_to_instantiate(): void
    {
        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip(),
        );

        $this->assertInstanceOf(FetchedClip::class, $fetchedClip);
        $this->assertEquals('SavageMoldyKoalaKappaClaus', $fetchedClip->externalId);
        $this->assertEquals('509658', $fetchedClip->externalGameId);
        $this->assertEquals('Jaime le futbole', $fetchedClip->title);
        $this->assertEquals('https://clips.twitch.tv/SavageMoldyKoalaKappaClaus', $fetchedClip->url);
        $this->assertEquals('157', $fetchedClip->views);
        $this->assertEquals('25', $fetchedClip->duration);

        $author = $fetchedClip->author;

        $this->assertInstanceOf(FetchedAuthor::class, $author);
        $this->assertEquals('519157370', $author->externalId);
        $this->assertEquals('Dig_Bill', $author->name);

        $thumbnail = $fetchedClip->thumbnail;

        $this->assertInstanceOf(Thumbnail::class, $thumbnail);
        $this->assertEquals('SavageMoldyKoalaKappaClaus', $thumbnail->id);
        $this->assertEquals(
            'https://clips-media-assets2.twitch.tv/AT-cm%7C933243563-preview-480x272.jpg', 
            $thumbnail->url,
        );

        $publishedAt = $fetchedClip->published_at;

        $this->assertInstanceOf(Carbon::class, $publishedAt);
        $this->assertEquals('2020-11-15 20:58:38', $publishedAt->format('Y-m-d H:i:s'));
    }
}
