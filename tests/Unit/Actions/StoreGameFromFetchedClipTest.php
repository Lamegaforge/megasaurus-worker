<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\ValueObjects\FetchedClip;
use App\Models\Game;
use App\Actions\StoreGameFromFetchedClip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Stubs\TwitchStub;
use Illuminate\Support\Facades\Queue;
use App\Jobs\FinalizeGameCreationJob;

class StoreGameFromFetchedClipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_store_fetched_game(): void
    {
        Queue::fake();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'game_id' => '789456',
            ]),
        );

        $game = app(StoreGameFromFetchedClip::class)->handle($fetchedClip);

        $this->assertTrue($game->wasRecentlyCreated);

        $this->assertSame('789456', $game->external_id);
        $this->assertSame(0, $game->active_clip_count);

        Queue::assertPushed(function (FinalizeGameCreationJob $job) use ($game) {
            return $job->uuid === $game->uuid;
        });
    }

    /**
     * @test
     */ 
    public function it_able_to_store_fetched_game_already_saved (): void
    {
        Queue::fake();

        $game = Game::factory()->create([
            'active_clip_count' => 10,
        ]);

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'game_id' => $game->external_id,
            ]),
        );

        $game = app(StoreGameFromFetchedClip::class)->handle($fetchedClip);

        $this->assertFalse($game->wasRecentlyCreated);
        
        $this->assertSame(10, $game->active_clip_count);

        Queue::assertNotPushed(FinalizeGameCreationJob::class);
    }

    /**
     * @test
     */
    public function it_able_to_store_fetched_stray_game(): void
    {
        Queue::fake();

        $game = Game::factory()->create();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'game_id' => null,
            ]),
        );

        $game = app(StoreGameFromFetchedClip::class)->handle($fetchedClip);

        $this->assertSame('nowhere', $game->external_id);
        $this->assertSame('nowhere', $game->name);
    }
}
