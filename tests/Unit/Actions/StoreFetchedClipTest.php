<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\ValueObjects\FetchedClip;
use App\Models\Clip;
use App\Models\Author;
use App\Actions\StoreFetchedClip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Stubs\TwitchStub;
use Illuminate\Testing\Assert;

class StoreFetchedClipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_store_fetched_clip(): void
    {
        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'id' => '123456',
                'game_id' => '789456',
                'title' => 'clip_name',
                'url' => 'clip_url',
                'creator_id' => '456',
                'creator_name' => 'author_name',
                'thumbnail_url' => 'https://thumbnail',
                'view_count' => 150,
                'duration' => 20,
                'created_at' => '2023-01-01',
            ]),
        );

        $clip = app(StoreFetchedClip::class)->handle($fetchedClip);

        $this->assertTrue($clip->wasRecentlyCreated);
        $this->assertTrue($clip->author->wasRecentlyCreated);

        Assert::assertArraySubset([
            'external_id' => '123456',
            'external_game_id' => '789456',
            'url' => 'clip_url',
            'title' => 'clip_name',
            'views' => 150,
            'duration' => 20,
            'state' => 'ok',
            'published_at' => '2022-12-31T23:00:00.000000Z',
            'author' => [
                'external_id' => '456',
                'name' => 'author_name',
            ],
        ], $clip->toArray());
    }

    /**
     * @test
     */
    public function it_able_to_store_fetched_clip_already_saved(): void
    {
        $clip = Clip::factory()->create();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'id' => $clip->external_id,
            ]),
        );

        $clip = app(StoreFetchedClip::class)->handle($fetchedClip);

        $this->assertFalse($clip->wasRecentlyCreated);
    }

    /**
     * @test
     */
    public function it_able_to_store_fetched_clip_with_an_author_already_saved(): void
    {
        $author = Author::factory()->create();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'creator_id' => $author->external_id,
            ]),
        );

        $clip = app(StoreFetchedClip::class)->handle($fetchedClip);

        $this->assertFalse($clip->author->wasRecentlyCreated);
    }
}