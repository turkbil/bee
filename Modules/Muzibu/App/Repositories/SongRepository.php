<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Modules\Muzibu\App\Models\Song;

class SongRepository
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;

    public function __construct(private Song $model)
    {
        $this->cachePrefix = 'muzibu_songs';
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
    }

    /**
     * Get list of locales that have indexed slug columns (slug_tr, slug_en, etc.)
     */
    private function getIndexedSlugLocales(): array
    {
        return Cache::remember('song_indexed_slug_locales', 3600, function () {
            $columns = Schema::getColumnListing('muzibu_songs');
            $indexedLocales = [];

            foreach ($columns as $column) {
                if (preg_match('/^slug_([a-z]{2})$/', $column, $matches)) {
                    $indexedLocales[] = $matches[1];
                }
            }

            return $indexedLocales;
        });
    }

    public function findById(int $id): ?Song
    {
        return $this->model->where('song_id', $id)->first();
    }

    public function findByIdWithRelations(int $id): ?Song
    {
        return $this->model->with(['album.artist', 'genre', 'media', 'playlists'])->where('song_id', $id)->first();
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?Song
    {
        $indexedLocales = $this->getIndexedSlugLocales();

        return $this->model->where(function ($query) use ($slug, $locale, $indexedLocales) {
            // Check if this locale has an indexed column
            if (in_array($locale, $indexedLocales)) {
                $query->where("slug_{$locale}", $slug);
            } else {
                // JSON fallback for non-indexed locales
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug]);
            }

            // Always add Turkish fallback (if not already the requested locale)
            if ($locale !== 'tr' && in_array('tr', $indexedLocales)) {
                $query->orWhere('slug_tr', $slug);
            }
        })->active()->with(['album.artist', 'genre', 'media'])->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->with(['album.artist', 'genre'])->orderBy('song_id', 'desc')->get();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->featured()->active()->with(['album.artist', 'genre', 'media'])->limit($limit)->get();
    }

    public function getPopular(int $limit = 10): Collection
    {
        return $this->model->active()->popular($limit)->with(['album.artist', 'genre', 'media'])->get();
    }

    public function getByGenre(int $genreId, int $limit = null): Collection
    {
        $query = $this->model->where('genre_id', $genreId)->active()->with(['album.artist', 'media']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->orderBy('song_id', 'desc')->get();
    }

    public function getByAlbum(int $albumId): Collection
    {
        return $this->model->where('album_id', $albumId)->active()->orderBy('song_id')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['album.artist', 'genre']);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['genre_id'])) {
            $query->where('genre_id', $filters['genre_id']);
        }

        if (isset($filters['album_id'])) {
            $query->where('album_id', $filters['album_id']);
        }

        // Artist filter (through album relation)
        if (!empty($filters['filterArtist'])) {
            $query->whereHas('album', function ($q) use ($filters) {
                $q->where('artist_id', $filters['filterArtist']);
            });
        }

        // Genre filter
        if (!empty($filters['filterGenre'])) {
            $query->where('genre_id', $filters['filterGenre']);
        }

        // Album filter
        if (!empty($filters['filterAlbum'])) {
            $query->where('album_id', $filters['filterAlbum']);
        }

        // HLS status filter
        if (!empty($filters['filterHls'])) {
            if ($filters['filterHls'] === 'completed') {
                $query->whereNotNull('hls_path');
            } elseif ($filters['filterHls'] === 'pending') {
                $query->whereNotNull('file_path')
                    ->whereNull('hls_path');
            }
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $locales = $filters['locales'] ?? ['tr'];
            $searchLower = '%' . mb_strtolower($search) . '%';

            $query->where(function ($q) use ($searchLower, $locales) {
                // Title search (JSON field - all active languages)
                foreach ($locales as $locale) {
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?", [$searchLower]);
                }

                // Lyrics search
                foreach ($locales as $locale) {
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(lyrics, '$.{$locale}'))) LIKE ?", [$searchLower]);
                }

                // Artist search (through album relation)
                $q->orWhereHas('album.artist', function ($artistQuery) use ($searchLower, $locales) {
                    $artistQuery->where(function ($aq) use ($searchLower, $locales) {
                        foreach ($locales as $locale) {
                            $aq->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?", [$searchLower]);
                        }
                    });
                });

                // Album search
                $q->orWhereHas('album', function ($albumQuery) use ($searchLower, $locales) {
                    $albumQuery->where(function ($alq) use ($searchLower, $locales) {
                        foreach ($locales as $locale) {
                            $alq->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?", [$searchLower]);
                        }
                    });
                });

                // Genre search
                $q->orWhereHas('genre', function ($genreQuery) use ($searchLower, $locales) {
                    $genreQuery->where(function ($gq) use ($searchLower, $locales) {
                        foreach ($locales as $locale) {
                            $gq->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?", [$searchLower]);
                        }
                    });
                });
            });
        }

        $sortField = $filters['sortField'] ?? 'song_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Song
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $song = $this->findById($id);
        return $song ? $song->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $song = $this->findById($id);
        return $song ? $song->delete() : false;
    }

    public function incrementPlayCount(int $id, ?int $userId = null, ?string $ipAddress = null): void
    {
        $song = $this->findById($id);
        if ($song) {
            $song->incrementPlayCount($userId, $ipAddress);
        }
    }

    public function clearCache(): void
    {
        Cache::tags([$this->cachePrefix, 'muzibu'])->flush();
    }
}
