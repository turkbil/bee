<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'tag_id';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'color',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Varsayılan olarak isim kolonunu kullan.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Belirtilen etiket tipine göre ilişkili modelleri getir.
     */
    public function taggedItems(string $morphClass): MorphToMany
    {
        return $this->morphedByMany($morphClass, 'taggable')->withTimestamps();
    }

    /**
     * İsimlerden etiket kimlikleri üret ve eşle.
     *
     * @param  array<int, string>  $names
     * @param  string|null  $type
     */
    public static function syncFromNames(array $names, ?string $type = null): array
    {
        return collect($names)
            ->filter(fn ($name) => filled($name))
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->map(function (string $name) use ($type) {
                $slug = Str::slug($name);

                if ($slug === '') {
                    return null;
                }

                /** @var \App\Models\Tag $tag */
                $tag = static::query()->firstOrCreate(
                    [
                        'slug' => $slug,
                    ],
                    [
                        'name' => $name,
                        'type' => $type,
                    ]
                );

                $needsUpdate = false;

                if ($tag->name !== $name) {
                    $tag->name = $name;
                    $needsUpdate = true;
                }

                if ($type !== null && $tag->type !== $type) {
                    $tag->type = $type;
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    $tag->save();
                }

                return $tag->getKey();
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
