<div
    x-data="{
        isGenerating: @entangle('isGenerating').live,
        startPolling() {
            if (this.isGenerating) {
                setTimeout(() => {
                    $wire.call('checkDraftProgress');
                    this.startPolling();
                }, 3000); // Her 3 saniyede bir kontrol
            }
        }
    }"
    x-init="$watch('isGenerating', value => { if(value) startPolling(); })"
>
    @include('blog::admin.helper')

    {{-- Header --}}
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col-auto ms-auto d-print-none">
                {{-- DEBUG: Test Livewire Button (Modal Dışında) --}}
                <button type="button" class="btn btn-warning me-2" wire:click="generateDrafts">
                    <i class="fas fa-bug"></i>
                    TEST (Modal Dışı)
                </button>

                {{-- Taslak Üret Butonu --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateDraftsModal" @if($isGenerating) disabled @endif>
                    <i class="fas fa-plus"></i>
                    Taslak Üret
                </button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @error('credits')
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @enderror

    {{-- Progress Bar (Taslak Üretimi) --}}
    @if($isGenerating)
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="spinner-border text-primary me-3" role="status"></div>
                    <div class="flex-fill">
                        <h4 class="mb-1">Taslaklar Oluşturuluyor...</h4>
                        <p class="text-muted mb-0">{{ $draftCount }} taslak için AI çalışıyor. Bu işlem birkaç dakika sürebilir.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Progress Bar (Blog Yazımı) --}}
    @if($isWriting)
        <div class="card mt-3" wire:poll.3s="checkBatchProgress">
            <div class="card-body">
                <h4 class="mb-3">
                    <i class="fas fa-pencil-alt"></i>
                    Bloglar Yazılıyor...
                </h4>

                <div class="progress progress-sm mb-2">
                    @php
                        $percentage = $batchProgress['total'] > 0
                            ? (($batchProgress['completed'] + $batchProgress['failed']) / $batchProgress['total']) * 100
                            : 0;
                    @endphp
                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                </div>

                <div class="row text-center">
                    <div class="col">
                        <div class="text-muted">Toplam</div>
                        <div class="fs-3">{{ $batchProgress['total'] }}</div>
                    </div>
                    <div class="col">
                        <div class="text-muted">Tamamlanan</div>
                        <div class="fs-3 text-success">{{ $batchProgress['completed'] }}</div>
                    </div>
                    <div class="col">
                        <div class="text-muted">Başarısız</div>
                        <div class="fs-3 text-danger">{{ $batchProgress['failed'] }}</div>
                    </div>
                    <div class="col">
                        <div class="text-muted">İlerleme</div>
                        <div class="fs-3 text-primary">{{ number_format($percentage, 0) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Drafts Table --}}
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">
                Taslak Listesi ({{ $drafts->total() }})
            </h3>
            <div class="card-actions">
                {{-- Toplu İşlem Butonları --}}
                @if(count($selectedDrafts) > 0)
                    <button
                        type="button"
                        class="btn btn-success me-2"
                        wire:click.prevent="generateBlogs"
                        @if($isWriting) disabled @endif
                    >
                        <i class="fas fa-check"></i>
                        Seçilenleri Yaz ({{ count($selectedDrafts) }} × 1 kredi = {{ count($selectedDrafts) }} kredi)
                    </button>
                @endif

                <button type="button" class="btn btn-outline-secondary" wire:click="toggleAll">
                    <i class="far fa-check-square"></i>
                    Tümünü Seç/Kaldır
                </button>
            </div>
        </div>

        @if($drafts->isEmpty())
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                <h3 class="mt-3 text-muted">Henüz taslak yok</h3>
                <p class="text-muted">Başlamak için yukarıdaki "Taslak Üret" butonunu kullanın.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-vcenter card-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" wire:click="toggleAll">
                            </th>
                            <th>Anahtar Kelime</th>
                            <th>Kategoriler</th>
                            <th>SEO Keywords</th>
                            <th>Durum</th>
                            <th style="width: 120px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drafts as $draft)
                            <tr @if($draft->is_generated) class="opacity-50 bg-light" style="pointer-events: none;" @endif>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        wire:click="toggleDraftSelection({{ $draft->id }})"
                                        @if(in_array($draft->id, $selectedDrafts)) checked @endif
                                        @if($draft->is_generated) disabled title="Bu taslak zaten kullanılmış" @endif
                                    >
                                </td>
                                <td>
                                    <strong>{{ $draft->topic_keyword }}</strong>
                                    <div class="text-muted small">{{ Str::limit($draft->meta_description, 80) }}</div>
                                    @if($draft->is_generated)
                                        <div class="text-danger small"><i class="fas fa-ban"></i> Zaten kullanılmış</div>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($draft->category_suggestions))
                                        @foreach(array_slice($draft->category_suggestions, 0, 2) as $catId)
                                            @php
                                                $category = \Modules\Blog\App\Models\BlogCategory::find($catId);
                                            @endphp
                                            @if($category)
                                                <span class="badge bg-blue-lt">{{ $category->title['tr'] ?? 'N/A' }}</span>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ implode(', ', array_slice($draft->seo_keywords ?? [], 0, 3)) }}
                                        @if(count($draft->seo_keywords ?? []) > 3)
                                            <span class="text-muted">+{{ count($draft->seo_keywords) - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($draft->is_generated)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Blog Yazıldı
                                        </span>
                                        @if($draft->generated_blog_id)
                                            <a href="{{ route('admin.blog.manage', $draft->generated_blog_id) }}" target="_blank" class="badge bg-primary">
                                                <i class="fas fa-external-link-alt"></i> Görüntüle
                                            </a>
                                        @endif
                                    @elseif($draft->is_selected)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Seçildi
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-file"></i> Taslak
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="deleteDraft({{ $draft->id }})"
                                        onclick="return confirm('Bu taslağı silmek istediğinize emin misiniz?')"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="card-footer">
                {{ $drafts->links() }}
            </div>
        @endif
    </div>

    {{-- Modal: Taslak Üret --}}
    <div
        class="modal fade"
        id="generateDraftsModal"
        tabindex="-1"
        wire:ignore.self
        x-data="{}"
        @close-modal.window="if ($event.detail === 'generateDraftsModal') { bootstrap.Modal.getInstance($el).hide(); }"
    >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-robot me-2"></i>
                            AI Taslak Üretimi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <p class="text-muted">
                        AI ile otomatik blog taslakları oluşturun. Her taslak bir anahtar kelime, kategori önerileri, SEO keywords ve blog yapısı içerir.
                    </p>
    
                    <div class="mb-3">
                        <label class="form-label">Taslak Sayısı</label>
                        <input
                            type="number"
                            class="form-control @error('draftCount') is-invalid @enderror"
                            wire:model="draftCount"
                            min="1"
                            max="200"
                        >
                        @error('draftCount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">
                            Toplam maliyet: <strong>1.0 kredi</strong> (taslak sayısından bağımsız)
                        </div>
                    </div>
    
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Bilgi:</strong> Taslak üretimi birkaç dakika sürebilir. İşlem arka planda çalışacaktır.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="generateDrafts"
                        @click="console.log('🔥 BUTTON CLICKED - Calling generateDrafts...')"
                        @if($isGenerating) disabled @endif
                    >
                        <i class="fas fa-magic"></i>
                        Taslak Üret (1 kredi)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
