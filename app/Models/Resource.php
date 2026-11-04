<?php

namespace App\Models;

use App\Enums\ResourceType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property array<array-key, mixed> $value
 * @property ResourceType $type
 * @property int $post_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Post|null $post
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereValue($value)
 *
 * @mixin \Eloquent
 */
class Resource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'type'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    protected function casts(): array
    {
        return [
            'metadata' => 'object',
            'value' => 'array',
            'type' => ResourceType::class,
        ];
    }
}
