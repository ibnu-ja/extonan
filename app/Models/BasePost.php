<?php

namespace App\Models;

use App\Enums\Permission;
use App\Observers\RecordAuthorObserver;
use App\Observers\RecordPublishDateObserver;
use Auth;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;
use Plank\Mediable\Mediable;
use Spatie\Sluggable\HasSlug;

#[ObservedBy([RecordAuthorObserver::class, RecordPublishDateObserver::class])]
abstract class BasePost extends Model
{
    use HasDrafts,
        HasFactory,
        HasSlug,
        Mediable,
        Searchable;

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
        return Attribute::get(fn () => [
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
            $q->when(
                $user->can(Permission::POST_READ_ANY->value),
                // Editor sees all unpublished posts
                fn (Builder $q) => $q->current(),
                fn (Builder $q) => $q->when(
                    // Check if user has the 'post.read.self' permission
                    $user->can(Permission::POST_READ_SELF->value),
                    // Author and contributor see their own unpublished posts
                    fn (Builder $q) => $q->published()->orWhere(fn (Builder $q) => $q->current()->where('author_id', $user->getAuthIdentifier())),
                ),
            );
            // Guest or user without permissions can only see published posts
        }, fn (Builder $q) => $q->withoutDrafts());
    }
}
