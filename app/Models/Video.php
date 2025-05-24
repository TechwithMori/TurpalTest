<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends Model
{
    use HasFactory;

    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(Experience::class);
    }
}
