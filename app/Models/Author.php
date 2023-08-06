<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name', 
        'external_id',
    ];

    protected $casts = [
        'external_id' => 'string',
    ];

    public function clips(): HasMany
    {
        return $this->hasMany(Clip::class);
    }
}
