<?php

namespace Modules\Blog\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Jobs\GenerateDraftsJob;
use Modules\Blog\App\Services\BlogAIBatchProcessor;
use Illuminate\Support\Facades\Log;

/**
 * Blog AI Draft Component
 *
 * AI taslak üretimi ve seçim UI'ı
 * Component name: blog-ai-draft-component (auto-generated from class name)
 */
#[Layout('admin.layout')]
class BlogAiDraftComponent extends Component
{
    use WithPagination;

    public int $draftCount = 100;
    public array $selectedDrafts = [];
    public bool $isGenerating = false;
    public bool $isWriting = false;
    public ?string $currentBatchId = null;
    public array $batchProgress = [
        'total' => 0,
        'completed' => 0,
        'failed' => 0,
    ];

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $rules = [
        'draftCount' => 'required|integer|min:1|max:200',
    ];

    /**
     * Taslak üretimi başlat (queue)
     */
    public function generateDrafts()
    {
        $this->validate();

        // Credit kontrolü (UI'da gösterilmesi için)
        if (!ai_can_use_credits(1.0)) {
            $this->addError('credits', 'Yetersiz AI kredisi! Lütfen kredi satın alın.');
            return;
        }

        try {
            // Job dispatch
            GenerateDraftsJob::dispatch($this->draftCount);

            $this->isGenerating = true;

            session()->flash('success', "{$this->draftCount} taslak üretimi başlatıldı. Lütfen bekleyin...");

            Log::info('Blog AI Draft Generation Requested', [
                'count' => $this->draftCount,
                'tenant_id' => tenant('id'),
            ]);

        } catch (\Exception $e) {
            $this->addError('generation', 'Taslak üretimi başlatılamadı: ' . $e->getMessage());

            Log::error('Blog AI Draft Generation Request Failed', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
            ]);
        }
    }

    /**
     * Taslak seçimini toggle
     */
    public function toggleDraftSelection(int $draftId)
    {
        if (in_array($draftId, $this->selectedDrafts)) {
            // Kaldır
            $this->selectedDrafts = array_diff($this->selectedDrafts, [$draftId]);

            // Database'de güncelle
            BlogAIDraft::find($draftId)?->update(['is_selected' => false]);
        } else {
            // Ekle
            $this->selectedDrafts[] = $draftId;

            // Database'de güncelle
            BlogAIDraft::find($draftId)?->update(['is_selected' => true]);
        }
    }

    /**
     * Tüm taslakları seç/kaldır
     */
    public function toggleAll()
    {
        $visibleDraftIds = BlogAIDraft::query()
            ->where('is_generated', false)
            ->pluck('id')
            ->toArray();

        if (count($this->selectedDrafts) === count($visibleDraftIds)) {
            // Hepsini kaldır
            $this->selectedDrafts = [];
            BlogAIDraft::whereIn('id', $visibleDraftIds)->update(['is_selected' => false]);
        } else {
            // Hepsini seç
            $this->selectedDrafts = $visibleDraftIds;
            BlogAIDraft::whereIn('id', $visibleDraftIds)->update(['is_selected' => true]);
        }
    }

    /**
     * Seçili taslakları blog yazısına dönüştür
     */
    public function generateBlogs()
    {
        if (empty($this->selectedDrafts)) {
            $this->addError('selection', 'Lütfen en az bir taslak seçin.');
            return;
        }

        // Credit kontrolü (seçili blog sayısı × 1.0 kredi)
        $requiredCredits = count($this->selectedDrafts) * 1.0;

        if (!ai_can_use_credits($requiredCredits)) {
            $this->addError('credits', "Yetersiz kredi! Gerekli: {$requiredCredits} kredi.");
            return;
        }

        try {
            // Batch processor ile toplu işlem başlat
            $batchProcessor = app(BlogAIBatchProcessor::class);
            $batchProcessor->procesSelectedDrafts($this->selectedDrafts);

            // Batch ID'yi kaydet (progress tracking için)
            $this->currentBatchId = 'blog_ai_batch_' . time() . '_' . tenant('id');
            $this->isWriting = true;
            $this->batchProgress = [
                'total' => count($this->selectedDrafts),
                'completed' => 0,
                'failed' => 0,
            ];

            session()->flash('success', count($this->selectedDrafts) . ' blog yazımı başlatıldı. İşlem tamamlanana kadar bekleyin...');

            Log::info('Blog AI Content Generation Requested', [
                'draft_count' => count($this->selectedDrafts),
                'batch_id' => $this->currentBatchId,
                'tenant_id' => tenant('id'),
            ]);

        } catch (\Exception $e) {
            $this->addError('generation', 'Blog yazımı başlatılamadı: ' . $e->getMessage());

            Log::error('Blog AI Content Generation Request Failed', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
            ]);
        }
    }

    /**
     * Batch progress kontrol et (polling için)
     */
    public function checkBatchProgress()
    {
        if (!$this->currentBatchId) {
            return;
        }

        $batchProcessor = app(BlogAIBatchProcessor::class);
        $this->batchProgress = $batchProcessor->getBatchStatus($this->currentBatchId);

        // Batch tamamlandı mı?
        if ($batchProcessor->isBatchCompleted($this->currentBatchId)) {
            $this->isWriting = false;
            $this->selectedDrafts = [];
            $this->currentBatchId = null;

            session()->flash('success', 'Tüm bloglar başarıyla oluşturuldu!');
        }
    }

    /**
     * Taslak sil
     */
    public function deleteDraft(int $draftId)
    {
        try {
            BlogAIDraft::find($draftId)?->delete();

            // Seçili listeden kaldır
            $this->selectedDrafts = array_diff($this->selectedDrafts, [$draftId]);

            session()->flash('success', 'Taslak silindi.');

        } catch (\Exception $e) {
            $this->addError('delete', 'Taslak silinemedi: ' . $e->getMessage());
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        $drafts = BlogAIDraft::query()
            ->latest()
            ->paginate(20);

        return view('blog::admin.livewire.blog-ai-draft-component', [
            'drafts' => $drafts,
        ]);
    }
}
