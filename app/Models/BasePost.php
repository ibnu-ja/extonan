<?php

namespace App\Models;

use App\Publishable;
use Auth;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Scope a query to only include owned/editable post for user.
     */
    public function scopeVisible(Builder $query): void
    {
        $user = auth()->user();

        $query->when($user, function (Builder $q) use ($user) {
            // Check if user has the 'post.read.any' permission
            $q->when($user->can('post.read.any', $this), function (Builder $q) {
                // Editor sees all unpublished posts
                $q->current();
            }, function (Builder $q) use ($user) {
                // Check if user has the 'post.read.self' permission
                $q->when($user->can('post.read.self', $this), function (Builder $q) use ($user) {
                    // Author and contributor see their own unpublished posts
                    $q->published()->orWhere(function (Builder $q) use ($user) {
                        return $q->current()->where('author_id', $user->getAuthIdentifier());
                    });
                });
            });
        }, function (Builder $q) {
            // Guest or user without permissions can only see published posts
            $q->withoutDrafts();
        });

        //dd($query->toRawSql());
    }
}
