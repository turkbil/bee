<?php

namespace Modules\AI\App\Livewire\KnowledgeBase;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Modules\AI\App\Models\KnowledgeBase;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class ManageComponent extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $filterCategory = '';
    public $filterActive = 'all';

    // Form fields
    public $itemId = null;
    public $category = '';
    public $question = '';
    public $answer = '';
    public $is_active = true;
    public $sort_order = 0;

    // UI state
    public $isEditing = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterActive' => ['except' => 'all'],
    ];

    protected $rules = [
        'category' => 'nullable|string|max:255',
        'question' => 'required|string|max:1000',
        'answer' => 'nullable|string|max:5000',
        'is_active' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    public function mount()
    {
        $this->is_active = true;
        $this->sort_order = 0;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingFilterActive()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $item = KnowledgeBase::findOrFail($id);

        $this->itemId = $item->id;
        $this->category = $item->category;
        $this->question = $item->question;
        $this->answer = $item->answer;
        $this->is_active = $item->is_active;
        $this->sort_order = $item->sort_order;

        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->itemId) {
                // Update
                $item = KnowledgeBase::findOrFail($this->itemId);
                $item->update([
                    'category' => $this->category,
                    'question' => $this->question,
                    'answer' => $this->answer,
                    'is_active' => $this->is_active,
                    'sort_order' => $this->sort_order,
                ]);

                $message = 'Bilgi başarıyla güncellendi.';
            } else {
                // Create
                KnowledgeBase::create([
                    'category' => $this->category,
                    'question' => $this->question,
                    'answer' => $this->answer,
                    'is_active' => $this->is_active,
                    'sort_order' => $this->sort_order,
                ]);

                $message = 'Bilgi başarıyla eklendi.';
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ]);

            $this->resetForm();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            if ($this->deleteId) {
                KnowledgeBase::findOrFail($this->deleteId)->delete();

                $this->dispatch('toast', [
                    'title' => 'Başarılı',
                    'message' => 'Bilgi başarıyla silindi.',
                    'type' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Silme işlemi sırasında bir hata oluştu.',
                'type' => 'error'
            ]);
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function toggleActive($id)
    {
        try {
            $item = KnowledgeBase::findOrFail($id);
            $item->update(['is_active' => !$item->is_active]);

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Durum güncellendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'İşlem sırasında bir hata oluştu.',
                'type' => 'error'
            ]);
        }
    }

    public function updateOrder($orderedIds)
    {
        try {
            foreach ($orderedIds as $index => $id) {
                KnowledgeBase::where('id', $id)->update(['sort_order' => $index + 1]);
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Sıralama güncellendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Sıralama güncellenirken bir hata oluştu.',
                'type' => 'error'
            ]);
        }
    }

    public function resetForm()
    {
        $this->reset(['itemId', 'category', 'question', 'answer', 'sort_order']);
        $this->is_active = true;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $query = KnowledgeBase::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('question', 'like', '%' . $this->search . '%')
                  ->orWhere('answer', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by category
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        // Filter by active status
        if ($this->filterActive !== 'all') {
            $query->where('is_active', $this->filterActive === 'active');
        }

        $items = $query->ordered()->paginate(20);
        $categories = KnowledgeBase::getCategories();

        return view('ai::livewire.knowledge-base.manage-component', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }
}
