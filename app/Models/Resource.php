<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    use HasFactory;

    protected $casts = ['metadata' => 'object', 'value' => 'array'];

    protected $fillable = ['name', 'value', 'type'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
