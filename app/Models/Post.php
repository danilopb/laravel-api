<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $slug
 * @property int $created_by
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static PostFactory factory(...$parameters)
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereContent($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereCreatedBy($value)
 * @method static Builder|Post whereDeletedAt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereSlug($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 */
class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'content', 'slug', 'file', 'created_by'];
    public string $storage_name = 'posts';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCreatedAtForHumanAttribute(): string
    {
        if ($this->created_at) {
            return Carbon::parse($this->created_at)->diffForHumans(Carbon::now());
        }
        return '';
    }

    public function getUpdatedAtForHumanAttribute(): string
    {
        if ($this->updated_at) {
            return Carbon::parse($this->updated_at)->diffForHumans(Carbon::now());
        }
        return '';
    }
}
