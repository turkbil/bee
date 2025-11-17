<div
    x-data="{
        isGenerating: @entangle('isGenerating').live,
        pollCount: 0,
        startPolling() {
            if (this.isGenerating) {
                // Progressive interval: 0-30sn → 3sn, 30-60sn → 5sn, 60sn+ → 10sn
                let interval = 3000; // Default 3 saniye

                if (this.pollCount > 20) {
                    interval = 10000; // 60+ saniye sonra 10 saniye
                } else if (this.pollCount > 10) {
                    interval = 5000; // 30-60 saniye arası 5 saniye
                }

                setTimeout(() => {
                    $wire.call('checkDraftProgress');
                    this.pollCount++;
                    this.startPolling();
                }, interval);
            } else {
                // Reset counter when generation stops
                this.pollCount = 0;
            }
        }
    }"
    x-init="$watch('isGenerating', value => { if(value) { this.pollCount = 0; this.startPolling(); } })"
>
    @include('blog::admin.helper')

    {{-- Header --}}
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">AI Blog Taslakları</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex align-items-center gap-2">
                    {{-- Draft Count Selector --}}
                    <select
                        wire:model.live="draftCount"
                        class="form-select"
                        style="width: 140px;"
                        @if($isGenerating) disabled @endif
                    >
                        <option value="10">10 Taslak</option>
                        <option value="25">25 Taslak</option>
                        <option value="50">50 Taslak</option>
                        <option value="100">100 Taslak</option>
                        <option value="200">200 Taslak</option>
                    </select>

                    {{-- Taslak Üret Butonu --}}
                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="generateDrafts"
                        wire:loading.attr="disabled"
                        @if($isGenerating) disabled @endif
                    >
                        <i class="fa-solid fa-plus"></i>
                        <span wire:loading.remove wire:target="generateDrafts">Taslak Üret (1 kredi)</span>
                        <span wire:loading wire:target="generateDrafts">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Oluşturuluyor...
                        </span>
                    </button>
                </div>
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
                    <!-- Arama Kutusu -->
                    <div class="col">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live="search" class="form-control"
                                placeholder="Anahtar kelime, kategori veya keyword ara...">
                        </div>
                    </div>
                    <!-- Ortadaki Loading -->
                    <div class="col position-relative">
                        <div wire:loading
                            wire:target="render, search, perPage, gotoPage, previousPage, nextPage, toggleAll, toggleDraftSelection, deleteDraft, generateBlogs, bulkDelete"
                            class="position-absolute top-50 start-50 translate-middle text-center"
                            style="width: 100%; max-width: 250px;">
                            <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                            <div class="progress mb-1">
                                <div class="progress-bar progress-bar-indeterminate"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Sağ Taraf (Sayfa Adeti) -->
                    <div class="col">
                        <div class="d-flex align-items-center justify-content-end gap-3">
                            <!-- Sayfa Adeti Seçimi -->
                            <div style="width: 80px; min-width: 80px">
                                <select wire:model.live="perPage" class="form-control listing-filter-select" data-choices
                                    data-choices-search="false" data-choices-filter="true">
                                    <option value="10">
                                        <nobr>10</nobr>
                                    </option>
                                    <option value="50">
                                        <nobr>50</nobr>
                                    </option>
                                    <option value="100">
                                        <nobr>100</nobr>
                                    </option>
                                    <option value="500">
                                        <nobr>500</nobr>
                                    </option>
                                    <option value="1000">
                                        <nobr>1000</nobr>
                                    </option>
                                </select>
                            </div>
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
                                            wire:model.live="selectAll"
                                            class="form-check-input"
                                            id="selectAllCheckbox"
                                            x-data="{
                                                indeterminate: {{ count($selectedDrafts) > 0 && !$selectAll ? 'true' : 'false' }}
                                            }"
                                            x-init="$el.indeterminate = indeterminate"
                                            x-effect="$el.indeterminate = ({{ count($selectedDrafts) }} > 0 && !{{ $selectAll ? 'true' : 'false' }})"
                                            @checked($selectAll)>
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
                                <tr class="hover-trigger" wire:key="row-{{ $draft->id }}" @if($draft->is_generated) style="opacity: 0.6;" @endif>
                                    <td class="sort-id small">
                                        <div class="hover-toggle">
                                            <span class="hover-hide">{{ $draft->id }}</span>
                                            <input type="checkbox"
                                                wire:model.live="selectedDrafts"
                                                value="{{ $draft->id }}"
                                                class="form-check-input hover-show"
                                                id="checkbox-{{ $draft->id }}"
                                                @checked(in_array($draft->id, $selectedDrafts))
                                                @if($draft->is_generated) disabled title="Bu taslak zaten kullanılmış" @endif>
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
                                            @php
                                                $categorySuggestions = is_array($draft->category_suggestions)
                                                    ? $draft->category_suggestions
                                                    : (is_string($draft->category_suggestions) ? json_decode($draft->category_suggestions, true) ?? [] : []);
                                            @endphp
                                            @if(!empty($categorySuggestions))
                                                <div>
                                                    @foreach(array_slice($categorySuggestions, 0, 2) as $catId)
                                                        @if(isset($categories[$catId]))
                                                            <span class="badge bg-blue-lt me-1">{{ $categories[$catId]->title['tr'] ?? 'N/A' }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Keywords --}}
                                            @php
                                                $seoKeywords = is_array($draft->seo_keywords)
                                                    ? $draft->seo_keywords
                                                    : (is_string($draft->seo_keywords) ? json_decode($draft->seo_keywords, true) ?? [] : []);
                                            @endphp
                                            @if(!empty($seoKeywords))
                                                <div class="small">
                                                    {{ implode(', ', array_slice($seoKeywords, 0, 3)) }}
                                                    @if(count($seoKeywords) > 3)
                                                        <span>+{{ count($seoKeywords) - 3 }}</span>
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
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            @if ($drafts->hasPages())
                {{ $drafts->links('livewire.custom-pagination') }}
            @else
                <div class="text-center py-3">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $drafts->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedDrafts) > 0)
            <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1050;">
                <div class="card shadow-lg border-0">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted">
                                <strong>{{ count($selectedDrafts) }}</strong> taslak seçildi
                            </span>
                            <div class="vr"></div>
                            <button
                                type="button"
                                class="btn btn-success"
                                wire:click.prevent="generateBlogs"
                                @if($isWriting) disabled @endif
                                data-bs-toggle="tooltip"
                                title="Seçilenleri Blog Olarak Yaz"
                            >
                                <i class="fa-solid fa-check me-1"></i>
                                Blog Yaz ({{ count($selectedDrafts) }} kredi)
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-danger"
                                wire:click.prevent="bulkDelete"
                                onclick="return confirm('Seçili {{ count($selectedDrafts) }} taslağı silmek istediğinize emin misiniz?')"
                                data-bs-toggle="tooltip"
                                title="Seçilenleri Sil"
                            >
                                <i class="fa-solid fa-trash me-1"></i>
                                Sil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
    @endif
</div>
