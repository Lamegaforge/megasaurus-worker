<?php

namespace App\Actions;

use App\Models\Author;
use App\ValueObjects\FetchedAuthor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StoreAuthorFromFetchedAuthor
{
    public function handle(FetchedAuthor $fetchedAuthor): Author
    {
        $lockName = $this->getLockName($fetchedAuthor);

        /** 
         * use of the lock to avoid duplication constraints during a creation
         * that can be caused by multiple workers
         */
        return Cache::lock($lockName, 3)->block(2, function () use ($fetchedAuthor) {
            return Author::firstOrCreate([
                'external_id' => $fetchedAuthor->externalId,
            ], [
                'name' => $fetchedAuthor->name,
                'uuid' => (string) Str::uuid(),
            ]);
        });
    }

    private function getLockName(FetchedAuthor $fetchedAuthor): string
    {
        return 'store-author-' . $fetchedAuthor->externalId->value;
    }
}
