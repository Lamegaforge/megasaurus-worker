<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\Clip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Stubs\TwitchStub;
use Tests\Traits\MockTwitchBearerTokenCache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\StoreFetchedClipJob;

class FetchClipsCommandTest extends TestCase
{
    use RefreshDatabase;
    use MockTwitchBearerTokenCache;

    /**
     * @test
     */
    public function it_able_to_dispatch_clips_in_jobs(): void
    {
        Queue::fake();

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeClip(),
                TwitchStub::makeClip(),
                TwitchStub::makeClip(),
            ]], 200),
        ]);

        $this->artisan('app:fetch-clips-command')->assertSuccessful();

        Queue::assertPushed(StoreFetchedClipJob::class, 3);
    }

    /**
     * @test
     */
    public function it_able_to_not_send_saved_clips(): void
    {
        $this->markTestSkipped('feature currently dormant');

        Queue::fake();

        $clip = Clip::factory()->create();

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeClip([
                    'id' => $clip->external_id,
                ]),
            ]], 200),
        ]);

        $this->artisan('app:fetch-clips-command')->assertSuccessful();

        Queue::assertNotPushed(StoreFetchedClipJob::class, 3);
    }
}
