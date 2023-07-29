<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\FetchClipsFromExternalIds;
use App\Models\Clip;

class DebugClipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-clip-command {hook}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hook = $this->argument('hook');

        $clip = Clip::where('uuid', $hook)
            ->orWhere('external_id', $hook)
            ->firstOrFail();

        $fetchedClips = app(FetchClipsFromExternalIds::class)->handle(
            collect($clip->external_id),
        );

        dd($fetchedClips);
    }
}
