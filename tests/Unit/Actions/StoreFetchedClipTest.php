<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\ValueObjects\FetchedClip;
use Domain\Models\Clip;
use Domain\Models\Game;
use Domain\Models\Author;
use App\Actions\StoreFetchedClip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Stubs\TwitchStub;
use Illuminate\Testing\Assert;
use Illuminate\Support\Facades\Queue;
use App\Jobs\FinalizeGameCreationJob;

class StoreFetchedClipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_store_fetched_clip(): void
    {
        Queue::fake();

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
        $this->assertTrue($clip->game->wasRecentlyCreated);

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
            'game' => [
                'external_id' => '789456',
            ],
        ], $clip->toArray());
    }

    /**
     * @test
     */
    public function it_able_to_store_fetched_clip_already_saved(): void
    {
        Queue::fake();

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
        Queue::fake();

        $author = Author::factory()->create();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'creator_id' => $author->external_id,
            ]),
        );

        $clip = app(StoreFetchedClip::class)->handle($fetchedClip);

        $this->assertFalse($clip->author->wasRecentlyCreated);
    }

    /**
     * @test
     */
    public function it_able_to_store_fetched_clip_with_an_game_already_saved(): void
    {
        Queue::fake();

        $game = Game::factory()->create();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'game_id' => $game->external_id,
            ]),
        );

        $clip = app(StoreFetchedClip::class)->handle($fetchedClip);

        $this->assertFalse($clip->game->wasRecentlyCreated);

        Queue::assertNotPushed(FinalizeGameCreationJob::class);
    }

    /**
     * @test
     */
    public function it_raises_appropriate_event_to_finalize_game_creation(): void
    {
        Queue::fake();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip(),
        );

        $clip = app(StoreFetchedClip::class)->handle($fetchedClip);

        Queue::assertPushed(function (FinalizeGameCreationJob $job) use ($clip) {
            return $job->uuid === $clip->game->uuid;
        });
    }
}