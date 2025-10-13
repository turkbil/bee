<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\SettingManagement\App\Models\AIKnowledgeBase;

/**
 * AI Knowledge Base Manager - Livewire Component
 *
 * Tenant-bazlı soru-cevap yönetimi
 * Real-time CRUD işlemleri
 */
class KnowledgeBaseManager extends Component
{
    // Form fields
    public $question = '';
    public $answer = '';
    public $category = '';
    public $metadata = [];
    public $is_active = true;
    public $sort_order = 0;

    // Editing
    public $editingId = null;
    public $isEditing = false;

    // Modal
    public $showModal = false;

    // Filters
    public $filterCategory = '';
    public $search = '';

    protected $rules = [
        'question' => 'required|string|min:5|max:500',
        'answer' => 'required|string|min:10|max:2000',
        'category' => 'nullable|string|max:100',
        'is_active' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    protected $messages = [
        'question.required' => 'Soru alanı zorunludur.',
        'question.min' => 'Soru en az 5 karakter olmalıdır.',
        'answer.required' => 'Yanıt alanı zorunludur.',
        'answer.min' => 'Yanıt en az 10 karakter olmalıdır.',
    ];

    public function mount()
    {
        // Initialize
    }

    public function render()
    {
        $items = AIKnowledgeBase::query()
            ->when($this->filterCategory, function ($query) {
                $query->category($this->filterCategory);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('question', 'like', '%' . $this->search . '%')
                      ->orWhere('answer', 'like', '%' . $this->search . '%');
                });
            })
            ->ordered()
            ->get();

        $categories = AIKnowledgeBase::getCategories();

        return view('settingmanagement::livewire.knowledge-base-manager', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'question' => $this->question,
            'answer' => $this->answer,
            'category' => $this->category ?: null,
            'metadata' => $this->metadata,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        if ($this->isEditing && $this->editingId) {
            // Update
            $item = AIKnowledgeBase::findOrFail($this->editingId);
            $item->update($data);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Bilgi başarıyla güncellendi!'
            ]);
        } else {
            // Create
            AIKnowledgeBase::create($data);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Yeni bilgi eklendi!'
            ]);
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $item = AIKnowledgeBase::findOrFail($id);

        $this->editingId = $item->id;
        $this->question = $item->question;
        $this->answer = $item->answer;
        $this->category = $item->category;
        $this->metadata = $item->metadata ?? [];
        $this->is_active = $item->is_active;
        $this->sort_order = $item->sort_order;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        AIKnowledgeBase::findOrFail($id)->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Bilgi silindi!'
        ]);
    }

    public function toggleActive($id)
    {
        $item = AIKnowledgeBase::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => $item->is_active ? 'Aktif edildi' : 'Pasif edildi'
        ]);
    }

    public function updateSortOrder($items)
    {
        foreach ($items as $index => $itemData) {
            AIKnowledgeBase::where('id', $itemData['value'])
                ->update(['sort_order' => $index]);
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Sıralama güncellendi!'
        ]);
    }

    private function resetForm()
    {
        $this->question = '';
        $this->answer = '';
        $this->category = '';
        $this->metadata = [];
        $this->is_active = true;
        $this->sort_order = 0;
        $this->editingId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }
}
