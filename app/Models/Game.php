<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Domain\Enums\ClipStateEnum;

class Game extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'uuid',
        'external_id',
        'name',
        'active_clip_count',
        'created_at',
    ];

    protected $casts = [
        'external_id' => 'string',
    ];

    public function clips(): HasMany
    {
        return $this->hasMany(Clip::class);
    }

    public function activeClips(): HasMany
    {
        return $this
            ->hasMany(Clip::class)
            ->where('state', ClipStateEnum::Ok);
    }

    /**
     * @return array<string, string>
     */
    public function toSearchableArray(): array
    {
        return [
            'game' => $this->name,
        ];
    }
}
