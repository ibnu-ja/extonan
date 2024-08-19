<?php

namespace App\Models;

use App\Publishable;
use Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;
use Plank\Mediable\Mediable;
use Spatie\Sluggable\HasSlug;

abstract class BasePost extends Model
{
    use HasSlug,
        HasDrafts,
        Searchable,
        HasFactory,
        Mediable,
        Publishable;

    /*
    |
    | Attributes that should be appended.
    |
    */
    protected $appends = ['can'];

    /*
    |
    | Relationships
    |
    */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /*
    |
    | Attributes
    |
    */

    public function can(): Attribute
    {
        return Attribute::get(fn() => [
            'update' => Auth::check() && Auth::user()->can('update', $this),
            'publish' => Auth::check() && Auth::user()->can('publish', $this),
            'delete' => Auth::check() && Auth::user()->can('delete', $this),
        ]);
    }
}
