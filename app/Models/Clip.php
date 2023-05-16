<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clip extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'external_game_id',
        'creator_id',
        'url',
        'title',
        'views',
        'title',
        'published_at',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'external_game_id', 'external_id');
    }
}
