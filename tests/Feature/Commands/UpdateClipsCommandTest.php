<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\Clip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Stubs\TwitchStub;
use Tests\Traits\MockTwitchBearerTokenCache;
use Illuminate\Support\Facades\Queue;
use App\Enums\ClipStateEnum;
use App\Jobs\UpdateClipFromFetchedClipJob;
use App\Jobs\DisableClipFromExternalIdJob;

class UpdateClipsCommandTest extends TestCase
{
    use RefreshDatabase;
    use MockTwitchBearerTokenCache;

    /**
     * @test
     */
    public function it_able_to_update_a_clip(): void
    {
        $clip = Clip::factory()
            ->withState(ClipStateEnum::Ok)
            ->create();

        Queue::fake();

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeClip([
                    'id' => $clip->external_id,
                ]),
            ]], 200),
        ]);

        $this->artisan('app:update-clips-command')->assertSuccessful();

        Queue::assertPushed(UpdateClipFromFetchedClipJob::class, 1);
        Queue::assertNotPushed(DisableClipFromExternalIdJob::class);

        Queue::assertPushed(function (UpdateClipFromFetchedClipJob $job) use ($clip) {
            return $job->fetchedClip->external_id === $clip->external_id;
        });
    }

    /**
     * @test
     */
    public function it_able_to_disable_a_clip(): void
    {
        $clip = Clip::factory()
            ->withState(ClipStateEnum::Ok)
            ->create();

        Queue::fake();

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                //
            ]], 200),
        ]);

        $this->artisan('app:update-clips-command')->assertSuccessful();

        Queue::assertNotPushed(UpdateClipFromFetchedClipJob::class);
        Queue::assertPushed(DisableClipFromExternalIdJob::class, 1);

        Queue::assertPushed(function (DisableClipFromExternalIdJob $job) use ($clip) {
            return $job->externalId === $clip->external_id;
        });
    }
}
