<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Domain\Enums\ClipStateEnum;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clip extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'uuid',
        'external_id',
        'external_game_id',
        'author_id',
        'game_id',
        'url',
        'title',
        'views',
        'duration',
        'state',
        'published_at',
    ];

    protected $casts = [
        'external_id' => 'string',
        'external_game_id' => 'string',
        'state' => ClipStateEnum::class,
        'views' => 'integer',
        'duration' => 'integer',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author->name,
            'game' => $this->game->name,
            'state' => $this->state->value,
        ];
    }
}
