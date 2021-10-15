<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Artist extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'name_real',
        'name_trans',
        'birthdate',
        'birthplace',
        'desc',
        'sex',
        'meta',
    ];

    protected $casts = [
        'meta' => 'json',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
