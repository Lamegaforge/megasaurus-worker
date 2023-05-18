<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
    ];

    protected $casts = [
        'external_id' => 'string',
    ];
}
