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
     * Component mount - seçili taslakları yükle
     */
    public function mount()
    {
        // Database'deki seçili taslakları component'e yükle
        // SADECE henüz üretilmemiş taslakları yükle!
        $this->selectedDrafts = BlogAIDraft::where('is_selected', true)
            ->where('is_generated', false) // Daha önce üretilmiş olanları HARIÇ TUT!
            ->pluck('id')
            ->toArray();

        Log::info('📋 Component mounted', [
            'selected_count' => count($this->selectedDrafts),
            'selected_ids' => $this->selectedDrafts,
        ]);
    }

    /**
     * Taslak üretimi başlat (queue)
     */
    public function generateDrafts()
    {
        Log::info('🔥 GENERATE DRAFTS CLICKED!', [
            'draftCount' => $this->draftCount,
            'tenant_id' => tenant('id'),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $this->validate();

        Log::info('✅ Validation passed', ['draftCount' => $this->draftCount]);

        // Credit kontrolü (UI'da gösterilmesi için)
        if (!ai_can_use_credits(1.0)) {
            Log::warning('❌ Insufficient credits');
            $this->addError('credits', 'Yetersiz AI kredisi! Lütfen kredi satın alın.');
            return;
        }

        Log::info('✅ Credit check passed');

        try {
            // Job dispatch
            GenerateDraftsJob::dispatch($this->draftCount);

            $this->isGenerating = true;

            session()->flash('success', "{$this->draftCount} taslak üretimi başlatıldı. Lütfen bekleyin...");

            // Modal'ı kapat (başarılı olduğu için)
            $this->dispatch('close-modal', 'generateDraftsModal');

            Log::info('Blog AI Draft Generation Requested', [
                'count' => $this->draftCount,
                'tenant_id' => tenant('id'),
            ]);

        } catch (\Exception $e) {
            Log::error('❌ EXCEPTION IN GENERATE DRAFTS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tenant_id' => tenant('id'),
            ]);

            $this->addError('generation', 'Taslak üretimi başlatılamadı: ' . $e->getMessage());

            Log::error('Blog AI Draft Generation Request Failed', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
            ]);
        }

        Log::info('🏁 GENERATE DRAFTS METHOD FINISHED');
    }

    /**
     * Taslak seçimini toggle
     */
    public function toggleDraftSelection(int $draftId)
    {
        $draft = BlogAIDraft::find($draftId);

        // Daha önce üretilmiş taslak seçilemesin!
        if ($draft && $draft->is_generated) {
            $this->addError('selection', 'Bu taslak zaten kullanılmış! Yeni bir taslak seçin.');
            return;
        }

        if (in_array($draftId, $this->selectedDrafts)) {
            // Kaldır
            $this->selectedDrafts = array_diff($this->selectedDrafts, [$draftId]);

            // Database'de güncelle
            $draft?->update(['is_selected' => false]);
        } else {
            // Ekle
            $this->selectedDrafts[] = $draftId;

            // Database'de güncelle
            $draft?->update(['is_selected' => true]);
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
        Log::info('🚀 GENERATE BLOGS CLICKED!', [
            'selected_count' => count($this->selectedDrafts),
            'selected_ids' => $this->selectedDrafts,
            'tenant_id' => tenant('id'),
        ]);

        if (empty($this->selectedDrafts)) {
            Log::warning('❌ No drafts selected');
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
     * Draft generation progress kontrol et (polling için)
     */
    public function checkDraftProgress()
    {
        if (!$this->isGenerating) {
            return;
        }

        // Yeni taslak var mı kontrol et
        $recentDrafts = BlogAIDraft::where('created_at', '>=', now()->subMinutes(5))->count();

        if ($recentDrafts > 0) {
            // Taslaklar oluşmuş, flag'i kapat
            $this->isGenerating = false;
            session()->flash('success', "{$recentDrafts} taslak başarıyla oluşturuldu!");
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
        // Otomatik progress kontrolü
        if ($this->isGenerating) {
            $this->checkDraftProgress();
        }

        $drafts = BlogAIDraft::query()
            ->latest()
            ->paginate(20);

        return view('blog::admin.livewire.blog-ai-draft-component', [
            'drafts' => $drafts,
        ]);
    }
}
