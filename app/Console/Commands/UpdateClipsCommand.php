<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateClipFromFetchedClipJob;
use App\Jobs\DisableClipFromExternalIdJob;
use App\Models\Clip;
use App\Actions\FetchClipsFromExternalIds;
use Illuminate\Support\Collection;
use App\Enums\ClipStateEnum;

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
        Clip::where('state', ClipStateEnum::Ok)
            ->chunk(100, function ($clips) {

                $externalClipIdList = $clips->pluck('external_id');

                $fetchedClips = app(FetchClipsFromExternalIds::class)->handle($externalClipIdList);

                $partitioned = $this->partitionClips($externalClipIdList, $fetchedClips);

                $this->updateFetchedClip($partitioned[0], $fetchedClips);
                $this->disableFetchedClip($partitioned[1]);
            });

        return SELF::SUCCESS;
    }

    private function partitionClips(Collection $externalClipIdList, Collection $fetchedClips): Collection
    {
        return $externalClipIdList->partition(function ($externalClipId) use ($fetchedClips) {
            return $fetchedClips->contains('external_id', $externalClipId);
        });
    }

    private function updateFetchedClip(Collection $externalClipIdList, Collection $fetchedClips): void
    {
        foreach ($externalClipIdList as $externalClipId) {

            $fetchedClip = $fetchedClips->where('external_id', $externalClipId)->first();

            UpdateClipFromFetchedClipJob::dispatch($fetchedClip)->onQueue('update-clip');
        }
    }

    private function disableFetchedClip(Collection $externalClipIdList): void
    {
        foreach ($externalClipIdList as $externalClipId) {
            DisableClipFromExternalIdJob::dispatch($externalClipId)->onQueue('delete-clip');
        }
    }
}
