<?php
namespace Modules\Announcement\App\Http\Livewire\Admin;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Announcement\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Announcement\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Announcement\App\Models\Announcement;

#[Layout('admin.layout')]
class AnnouncementComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle;

    #[Url]
    public $search = '';

    #[Url]
    public $perAnnouncement = 10;

    #[Url]
    public $sortField = 'announcement_id';

    #[Url]
    public $sortDirection = 'desc';

    protected function getModelClass()
    {
        return Announcement::class;
    }

    public function updatedPerAnnouncement()
    {
        $this->resetAnnouncement();
    }

    public function updatedSearch()
    {
        $this->resetAnnouncement();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $announcement = Announcement::where('announcement_id', $id)->first();
    
        if ($announcement) {
            $announcement->update(['is_active' => !$announcement->is_active]);
            
            log_activity(
                $announcement,
                $announcement->is_active ? 'aktif edildi' : 'pasif edildi'
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$announcement->title}\" " . ($announcement->is_active ? 'aktif' : 'pasif') . " edildi.",
                'type' => $announcement->is_active ? 'success' : 'warning',
            ]);
        }
    }

    public function render()
    {
        $query = Announcement::where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
    
        $announcements = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perAnnouncement);
    
        return view('announcement::admin.livewire.announcement-component', [
            'announcements' => $announcements,
        ]);
    }
}