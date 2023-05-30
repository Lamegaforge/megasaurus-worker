<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateClipFromFetchedClipJob;
use App\Jobs\DisableClipFromExternalIdJob;
use Domain\Models\Clip;
use App\Actions\FetchClipsFromExternalIds;
use Illuminate\Support\Collection;
use Domain\Enums\ClipStateEnum;
use App\ValueObjects\ExternalId;

class UpdateClipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-clips-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Clip::whereIn('state', [ClipStateEnum::Ok, ClipStateEnum::Suspicious])
            ->chunk(100, function ($clips) {

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
