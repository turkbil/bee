<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Playlist;
use Modules\MediaManagement\App\Jobs\GenerateAICover;

class AICoverComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $contentType = 'song'; // song, album, playlist
    public string $filter = 'without_cover'; // all, without_cover, with_cover
    public array $selectedItems = [];
    public bool $selectAll = false;
    public bool $processing = false;

    protected $queryString = ['contentType', 'filter'];

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedItems = $this->items->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    #[Computed]
    public function items()
    {
        $query = match($this->contentType) {
            'album' => Album::query(),
            'playlist' => Playlist::where('type', 'system'),
            default => Song::query(),
        };

        if ($this->filter === 'without_cover') {
            $query->whereDoesntHave('media', fn($q) => $q->where('collection_name', 'hero'));
        } elseif ($this->filter === 'with_cover') {
            $query->whereHas('media', fn($q) => $q->where('collection_name', 'hero'));
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    #[Computed]
    public function stats(): array
    {
        $modelClass = match($this->contentType) {
            'album' => Album::class,
            'playlist' => Playlist::class,
            default => Song::class,
        };

        $query = $modelClass::query();
        if ($this->contentType === 'playlist') {
            $query->where('type', 'system');
        }

        $total = $query->count();
        $withCover = (clone $query)->whereHas('media', fn($q) => $q->where('collection_name', 'hero'))->count();

        return [
            'total' => $total,
            'with_cover' => $withCover,
            'without_cover' => $total - $withCover,
        ];
    }

    public function generateSelected(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Lütfen en az bir öğe seçin.');
            return;
        }

        $this->processing = true;

        $modelClass = match($this->contentType) {
            'album' => Album::class,
            'playlist' => Playlist::class,
            default => Song::class,
        };

        foreach ($this->selectedItems as $id) {
            $item = $modelClass::find($id);
            if ($item) {
                $title = $item->title ?? $item->name ?? 'Untitled';
                GenerateAICover::dispatch(
                    $modelClass,
                    $item->id,
                    $title,
                    $this->contentType,
                    auth()->id()
                );
            }
        }

        $this->selectedItems = [];
        $this->selectAll = false;
        $this->processing = false;

        session()->flash('success', count($this->selectedItems) . ' öğe için AI görsel üretimi başlatıldı.');
    }

    public function generateSingle(int $id): void
    {
        $modelClass = match($this->contentType) {
            'album' => Album::class,
            'playlist' => Playlist::class,
            default => Song::class,
        };

        $item = $modelClass::find($id);
        if ($item) {
            $title = $item->title ?? $item->name ?? 'Untitled';
            GenerateAICover::dispatch(
                $modelClass,
                $item->id,
                $title,
                $this->contentType,
                auth()->id()
            );
            session()->flash('success', "'$title' için AI görsel üretimi başlatıldı.");
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.ai-cover-component');
    }
}
