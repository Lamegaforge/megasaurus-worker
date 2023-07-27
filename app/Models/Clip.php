<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ClipStateEnum;
use Laravel\Scout\Searchable;

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

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
        ];
    }
}
