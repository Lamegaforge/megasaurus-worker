<?php

namespace Tests\Feature\Commands;

use Closure;
use Tests\TestCase;
use App\Models\Clip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Stubs\TwitchStub;
use Tests\Traits\MockTwitchBearerTokenCache;
use Illuminate\Support\Facades\Queue;
use Domain\Enums\ClipStateEnum;
use App\Jobs\UpdateClipFromFetchedClipJob;
use App\Jobs\DisableClipFromExternalIdJob;

class UpdateClipsCommandTest extends TestCase
{
    use RefreshDatabase;
    use MockTwitchBearerTokenCache;

    /**
     * @test
     * @dataProvider updatableStateProvider
     */
    public function it_able_to_update_a_clip(Closure $state): void
    {
        $clip = Clip::factory()
            ->withState($state())
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
     * @dataProvider updatableStateProvider
     */
    public function it_able_to_disable_a_clip(Closure $state): void
    {
        $clip = Clip::factory()
            ->withState($state())
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

    /**
     * @test
     * @dataProvider notUpdatableStateProvider
     */
    public function some_states_are_not_updatable(Closure $state): void
    {
        $clip = Clip::factory()
            ->withState($state())
            ->create();

        Queue::fake();

        $this->artisan('app:update-clips-command')->assertSuccessful();

        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    /** 
     * At this time, Laravel is not yet bootstrapped
     * Using a closure is a simple trick to delayed instantiation
     */
    public static function updatableStateProvider(): array
    {
        return [
            [fn () => ClipStateEnum::Ok],
            [fn () => ClipStateEnum::Suspicious],
        ];
    }

    public static function notUpdatableStateProvider(): array
    {
        return [
            [fn () => ClipStateEnum::Disable],
        ];
    }

}
