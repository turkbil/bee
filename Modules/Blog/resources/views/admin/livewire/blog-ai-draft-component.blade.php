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
                {{-- Taslak Üret Butonu (Modal Yok - Direkt Livewire) --}}
                <button
                    type="button"
                    class="btn btn-primary"
                    wire:click="generateDrafts"
                    wire:loading.attr="disabled"
                    @if($isGenerating) disabled @endif
                >
                    <i class="fa-solid fa-plus"></i>
                    <span wire:loading.remove wire:target="generateDrafts">Taslak Üret (1 kredi - {{ $draftCount }} adet)</span>
                    <span wire:loading wire:target="generateDrafts">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Oluşturuluyor...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fa-solid fa-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @error('credits')
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>
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
                    <i class="fa-solid fa-pencil"></i>
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
        @if($drafts->isEmpty())
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                <h3 class="mt-3" style="opacity: 0.7;">Henüz taslak yok</h3>
                <p style="opacity: 0.6;">Başlamak için yukarıdaki "Taslak Üret" butonunu kullanın.</p>
            </div>
        @else
            <div class="card-body p-0">
                <!-- Header Bölümü -->
                <div class="row mx-2 my-3">
                    <!-- Sol Taraf - Toplam Sayı -->
                    <div class="col-auto">
                        <h3 class="card-title mb-0">
                            Taslak Listesi ({{ $drafts->total() }})
                        </h3>
                    </div>
                    <!-- Ortadaki Loading -->
                    <div class="col position-relative">
                        <div wire:loading
                            wire:target="toggleAll, toggleDraftSelection, deleteDraft, generateBlogs"
                            class="position-absolute top-50 start-50 translate-middle text-center"
                            style="width: 100%; max-width: 250px;">
                            <div class="small mb-2" style="opacity: 0.7;">{{ __('admin.updating') }}</div>
                            <div class="progress mb-1">
                                <div class="progress-bar progress-bar-indeterminate"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Sağ Taraf - Aksiyon Butonları -->
                    <div class="col-auto">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            @if(count($selectedDrafts) > 0)
                                <button
                                    type="button"
                                    class="btn btn-success"
                                    wire:click.prevent="generateBlogs"
                                    @if($isWriting) disabled @endif
                                    data-bs-toggle="tooltip"
                                    title="Seçilenleri Blog Olarak Yaz"
                                >
                                    <i class="fa-solid fa-check"></i>
                                    ({{ count($selectedDrafts) }} kredi)
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-outline-danger"
                                    wire:click.prevent="bulkDelete"
                                    onclick="return confirm('Seçili {{ count($selectedDrafts) }} taslağı silmek istediğinize emin misiniz?')"
                                    data-bs-toggle="tooltip"
                                    title="Seçilenleri Sil"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif

                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                wire:click="toggleAll"
                                data-bs-toggle="tooltip"
                                title="Tümünü Seç/Kaldır"
                            >
                                <i class="fa-regular fa-square-check"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tablo Bölümü -->
                <div id="table-default" class="table-responsive">
                    <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                        <thead>
                            <tr>
                                <th style="width: 50px">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="checkbox"
                                            class="form-check-input"
                                            wire:click="toggleAll"
                                            id="selectAllCheckbox"
                                            x-data="{
                                                totalDrafts: {{ $drafts->where('is_generated', false)->count() }},
                                                selectedCount: {{ count($selectedDrafts) }}
                                            }"
                                            x-init="
                                                $el.indeterminate = (selectedCount > 0 && selectedCount < totalDrafts);
                                                $el.checked = (selectedCount === totalDrafts && totalDrafts > 0);
                                            "
                                            x-effect="
                                                $el.indeterminate = ({{ count($selectedDrafts) }} > 0 && {{ count($selectedDrafts) }} < totalDrafts);
                                                $el.checked = ({{ count($selectedDrafts) }} === totalDrafts && totalDrafts > 0);
                                            ">
                                    </div>
                                </th>
                                <th>Anahtar Kelime</th>
                                <th>Kategoriler & Keywords</th>
                                <th class="text-center" style="width: 140px">Durum</th>
                                <th class="text-center" style="width: 100px">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody">
                            @foreach($drafts as $draft)
                                <tr class="hover-trigger" @if($draft->is_generated) style="opacity: 0.6;" @endif>
                                    <td class="sort-id small">
                                        <div class="hover-toggle">
                                            <span class="hover-hide">{{ $draft->id }}</span>
                                            <input
                                                type="checkbox"
                                                class="form-check-input hover-show"
                                                wire:click="toggleDraftSelection({{ $draft->id }})"
                                                @if(in_array($draft->id, $selectedDrafts)) checked @endif
                                                @if($draft->is_generated) disabled title="Bu taslak zaten kullanılmış" @endif
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $draft->topic_keyword }}</strong>
                                            <div class="small">{{ Str::limit($draft->meta_description, 80) }}</div>
                                            @if($draft->is_generated)
                                                <div class="text-danger small mt-1">
                                                    <i class="fa-solid fa-ban"></i> Zaten kullanılmış
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            {{-- Kategoriler --}}
                                            @if(!empty($draft->category_suggestions))
                                                <div>
                                                    @foreach(array_slice($draft->category_suggestions, 0, 2) as $catId)
                                                        @if(isset($categories[$catId]))
                                                            <span class="badge bg-blue-lt me-1">{{ $categories[$catId]->title['tr'] ?? 'N/A' }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Keywords --}}
                                            @if(!empty($draft->seo_keywords))
                                                <div class="small">
                                                    {{ implode(', ', array_slice($draft->seo_keywords ?? [], 0, 3)) }}
                                                    @if(count($draft->seo_keywords ?? []) > 3)
                                                        <span>+{{ count($draft->seo_keywords) - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($draft->is_generated)
                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                <span class="badge bg-success">
                                                    <i class="fa-solid fa-check"></i> Blog Yazıldı
                                                </span>
                                                @if($draft->generated_blog_id)
                                                    <a href="{{ route('admin.blog.manage', $draft->generated_blog_id) }}" target="_blank" class="badge bg-primary">
                                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Görüntüle
                                                    </a>
                                                @endif
                                            </div>
                                        @elseif($draft->is_selected)
                                            <span class="badge bg-warning">
                                                <i class="fa-solid fa-clock"></i> Seçildi
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fa-solid fa-file"></i> Taslak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex align-items-center gap-3 justify-content-center">
                                            <a href="javascript:void(0);"
                                                wire:click="deleteDraft({{ $draft->id }})"
                                                onclick="return confirm('Bu taslağı silmek istediğinize emin misiniz?')"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Sil"
                                                style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="card-footer">
                @if ($drafts->hasPages())
                    {{ $drafts->links() }}
                @else
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <p class="small mb-0" style="opacity: 0.7;">
                            Toplam <span class="fw-semibold">{{ $drafts->total() }}</span> sonuç
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Modal Removed: Direkt Livewire button kullanılıyor --}}
</div>
