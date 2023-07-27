<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

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
}
