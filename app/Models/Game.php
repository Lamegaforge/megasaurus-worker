<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Game extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'uuid',
        'external_id',
        'name',
        'created_at',
    ];

    protected $casts = [
        'external_id' => 'string',
    ];

    public function clips()
    {
        return $this->hasMany(Clip::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'game' => $this->name,
        ];
    }
}
