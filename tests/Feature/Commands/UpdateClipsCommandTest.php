<?php

namespace Tests\Feature\Commands;

use Closure;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Clip;
use Tests\Stubs\TwitchStub;
use Domain\Enums\ClipStateEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\InteractsWithTime;
use App\Jobs\DisableClipFromExternalIdJob;
use App\Jobs\UpdateClipFromFetchedClipJob;
use Tests\Traits\MockTwitchBearerTokenCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class UpdateClipsCommandTest extends TestCase
{
    use RefreshDatabase;
    use MockTwitchBearerTokenCache;
    use InteractsWithTime;

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
            return $job->fetchedClip->externalId->value === $clip->external_id;
        });
    }

    /**
     * @test
     */
    public function it_able_to_update_only_recent_clips(): void
    {
        $this->freezeTime();

        $clip = Clip::factory()
            ->state(new Sequence(
                ['published_at' => Carbon::now()],
                ['published_at' => Carbon::now()->subMinutes(181)],
            ))
            ->count(2)
            ->create();

        Queue::fake();

        $recentClip = $clip->first();

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeClip([
                    'id' => $recentClip->external_id,
                ]),
            ]], 200),
        ]);

        $this->artisan('app:update-clips-command --recent')->assertSuccessful();

        Queue::assertPushed(UpdateClipFromFetchedClipJob::class, 1);
        Queue::assertNotPushed(DisableClipFromExternalIdJob::class);

        Queue::assertPushed(function (UpdateClipFromFetchedClipJob $job) use ($recentClip) {
            return $job->fetchedClip->externalId->value === $recentClip->external_id;
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
            return $job->externalId->value === $clip->external_id;
        });
    }

    /**
     * @test
     * @dataProvider notUpdatableStateProvider
     */
    public function some_states_are_not_updatable(Closure $state): void
    {
        Clip::factory()
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
