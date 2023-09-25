<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Clip;
use Domain\Enums\ClipStateEnum;
use Illuminate\Console\Command;
use App\ValueObjects\ExternalId;
use Illuminate\Support\Collection;
use App\Actions\FetchClipsFromExternalIds;
use App\Jobs\DisableClipFromExternalIdJob;
use App\Jobs\UpdateClipFromFetchedClipJob;

class UpdateClipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-clips-command {--recent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * 100 is the limit for searching clips on the twitch api
     */
    protected int $chunk = 100;

    /**
     * We also fetch suspect clips because the author can fix the name of the clip
     *
     * @var ClipStateEnum[]
     */
    protected array $states = [
        ClipStateEnum::Ok, 
        ClipStateEnum::Suspicious,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = Clip::whereIn('state', $this->states);

        $query->when($this->option('recent'), function ($query) {
            $query->where('published_at', '>=', Carbon::now()->subHours(3));
        });

        $query->chunk($this->chunk, function ($clips) {

            $savedExternalClipIdList = $clips->pluck('external_id');

            $fetchedClips = app(FetchClipsFromExternalIds::class)->handle($savedExternalClipIdList);

            $fetchedClips = $fetchedClips->groupBy('externalId.value');

            [$clipsToUpdate, $clipsToDisable] = $this->partitionClips($savedExternalClipIdList, $fetchedClips);

            $this->updateClip($clipsToUpdate, $fetchedClips);
            $this->disableClip($clipsToDisable);
        });

        return SELF::SUCCESS;
    }

    /**
     * if one of the saved clips of the chunk no longer appears in the api fetch
     * it means it should be disabled
     */
    private function partitionClips(Collection $savedExternalClipIdList, Collection $fetchedClips): Collection
    {
        return $savedExternalClipIdList->partition(function ($savedExternalClipId) use ($fetchedClips) {
            return $fetchedClips->has($savedExternalClipId);
        });
    }

    private function updateClip(Collection $clipsToUpdate, Collection $fetchedClips): void
    {
        foreach ($clipsToUpdate as $clipToUpdate) {

            $fetchedClip = $fetchedClips->get($clipToUpdate)->first();

            UpdateClipFromFetchedClipJob::dispatch($fetchedClip)->onQueue('update-clip');
        }
    }

    private function disableClip(Collection $clipsToDisable): void
    {
        foreach ($clipsToDisable as $clipToDisable) {

            $externalId = new ExternalId($clipToDisable);

            DisableClipFromExternalIdJob::dispatch($externalId)->onQueue('disable-clip');
        }
    }
}
