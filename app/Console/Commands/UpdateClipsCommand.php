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

                $externalClipIdList = $clips->pluck('external_id');

                $fetchedClips = app(FetchClipsFromExternalIds::class)->handle($externalClipIdList);

                [$clipsToUpdate, $clipsToDisable] = $this->partitionClips($externalClipIdList, $fetchedClips);

                $this->updateClip($clipsToUpdate, $fetchedClips);
                $this->disableClip($clipsToDisable);
            });

        return SELF::SUCCESS;
    }

    /**
     * if one of the clips of the chunk no longer appears in the api fetch
     * it means it should be disabled
     */
    private function partitionClips(Collection $externalClipIdList, Collection $fetchedClips): Collection
    {
        return $externalClipIdList->partition(function ($externalClipId) use ($fetchedClips) {
            return $fetchedClips->contains('externalId', $externalClipId);
        });
    }

    private function updateClip(Collection $externalClipIdList, Collection $fetchedClips): void
    {
        foreach ($externalClipIdList as $externalClipId) {

            $fetchedClip = $fetchedClips->where('externalId', $externalClipId)->first();

            UpdateClipFromFetchedClipJob::dispatch($fetchedClip)->onQueue('update-clip');
        }
    }

    private function disableClip(Collection $externalClipIdList): void
    {
        foreach ($externalClipIdList as $externalClipId) {

            $externalId = new ExternalId($externalClipId);

            DisableClipFromExternalIdJob::dispatch($externalId)->onQueue('disable-clip');
        }
    }
}
