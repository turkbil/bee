<div class="file-manager"
     x-data="fileManager('{{ route('admin.mediamanagement.library.upload', [], false) }}', '{{ csrf_token() }}', '{{ $this->getId() }}')"
     @keydown.window="handleKeydown($event)"
     @contextmenu.prevent="handleGlobalContext($event)">

    @php
        $previewableMedia = $mediaItems->filter(fn($m) => $this->isPreviewable($m))->map(fn($m) => [
            'url' => $this->isVideo($m) ? $m->getUrl() : thumb($m, 1920, 1920, ['quality' => 90]),
            'thumb' => $this->isVideo($m) ? $m->getUrl() : thumb($m, 400, 400, ['quality' => 80]),
            'name' => $m->name,
            'id' => $m->id,
            'isVideo' => $this->isVideo($m),
            'mimeType' => $m->mime_type
        ])->values()->toArray();

        $allMedia = $mediaItems->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->name ?? $m->file_name,
            'file_name' => $m->file_name,
            'size' => $m->size,
            'url' => $m->computed_url ?? $m->getUrl()
        ])->values()->toArray();
    @endphp

    <style>
        /* ========== LIGHT MODE (DEFAULT) ========== */
        .file-manager {
            height: calc(100vh - 180px);
            display: flex;
            flex-direction: column;
        }
        .fm-toolbar {
            background: #ffffff;
            border-bottom: 1px solid #e6e7e9;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .fm-toolbar .btn { padding: 6px 10px; font-size: 13px; }
        .fm-toolbar .btn-icon { padding: 6px 8px; }
        .fm-toolbar .divider {
            width: 1px;
            height: 24px;
            background: #e6e7e9;
            margin: 0 4px;
        }
        .fm-body {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .fm-sidebar {
            width: 220px;
            background: #f6f8fb;
            border-right: 1px solid #e6e7e9;
            overflow-y: auto;
            flex-shrink: 0;
        }
        .fm-sidebar-section {
            padding: 12px;
            border-bottom: 1px solid #e6e7e9;
        }
        .fm-sidebar-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #626976;
            margin-bottom: 8px;
        }
        .fm-sidebar-item {
            display: flex;
            align-items: center;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            color: #1d273b;
            gap: 8px;
        }
        .fm-sidebar-item:hover { background: #e6e7e9; }
        .fm-sidebar-item.active { background: #e8f1fd; color: #206bc4; }
        .fm-sidebar-item i { width: 16px; font-size: 12px; color: #626976; }
        .fm-sidebar-item.active i { color: #206bc4; }
        .fm-sidebar-item .count {
            margin-left: auto;
            font-size: 11px;
            color: #626976;
        }
        .fm-stats-text {
            color: #6b7280;
        }
        .fm-stats-text strong {
            color: #1f2937;
        }
        .fm-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #ffffff;
        }
        .fm-breadcrumb {
            padding: 8px 12px;
            border-bottom: 1px solid rgba(98,105,118,.16);
            font-size: 12px;
            color: #626976;
            flex-shrink: 0;
        }
        .fm-breadcrumb a { color: #206bc4; text-decoration: none; }
        .fm-content {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
            position: relative;
        }
        .fm-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.95);
            align-items: center;
            justify-content: center;
            z-index: 100;
            display: none;
        }
        .fm-loading-overlay.active {
            display: flex;
        }
        [data-bs-theme="dark"] .fm-loading-overlay {
            background: rgba(24,36,51,0.9) !important;
        }
        .fm-loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e5e7eb;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .fm-grid {
            display: grid;
            gap: 4px;
        }
        .fm-grid.size-sm { grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); }
        .fm-grid.size-md { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); }
        .fm-grid.size-lg { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); }
        .fm-grid.size-xl { grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); }

        .fm-grid.size-sm .fm-item-thumb { width: 48px; height: 48px; }
        .fm-grid.size-md .fm-item-thumb { width: 80px; height: 80px; }
        .fm-grid.size-lg .fm-item-thumb { width: 100%; height: 120px; }
        .fm-grid.size-xl .fm-item-thumb { width: 100%; height: 180px; }

        .fm-grid.size-lg .fm-item-thumb img,
        .fm-grid.size-xl .fm-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fm-grid.size-lg .fm-item { padding: 6px; }
        .fm-grid.size-xl .fm-item { padding: 8px; }
        .fm-grid.size-lg .fm-item-name { font-size: 12px; }
        .fm-grid.size-xl .fm-item-name { font-size: 13px; }
        .fm-grid.size-lg .fm-item-size { font-size: 11px; }
        .fm-grid.size-xl .fm-item-size { font-size: 12px; }
        .fm-item {
            padding: 8px 4px;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.1s;
        }
        .fm-item:hover { background: #f6f8fb; }
        .fm-item.selected { background: #e8f1fd; border-color: #206bc4; }
        .fm-item-thumb {
            width: 64px;
            height: 64px;
            margin: 0 auto 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e6e7e9;
            border-radius: 4px;
            overflow: hidden;
        }
        .fm-item-thumb img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .fm-item-name {
            font-size: 11px;
            color: #1d273b;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 2px;
        }
        .fm-item-size {
            font-size: 10px;
            color: #626976;
        }
        .fm-list {
            font-size: 13px;
        }
        .fm-list-header {
            display: grid;
            grid-template-columns: 32px 40px 1fr 80px 80px 100px;
            padding: 8px 12px;
            background: #f6f8fb;
            border-bottom: 1px solid #e6e7e9;
            font-weight: 600;
            font-size: 11px;
            color: #626976;
            text-transform: uppercase;
            position: sticky;
            top: 0;
        }
        .fm-list-row {
            display: grid;
            grid-template-columns: 32px 40px 1fr 80px 80px 100px;
            padding: 6px 12px;
            border-bottom: 1px solid rgba(98,105,118,.16);
            align-items: center;
            cursor: pointer;
            color: #1d273b;
        }
        .fm-list-row:hover { background: #f6f8fb; }
        .fm-list-row.selected { background: #e8f1fd; }
        .fm-list-thumb {
            width: 32px;
            height: 32px;
            border-radius: 2px;
            object-fit: contain;
            background: #e6e7e9;
        }
        .fm-statusbar {
            padding: 6px 12px;
            border-top: 1px solid #e6e7e9;
            font-size: 11px;
            color: #626976;
            background: #f6f8fb;
            display: flex;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .fm-context {
            position: fixed;
            background: #ffffff;
            border: 1px solid #e6e7e9;
            border-radius: 6px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            min-width: 160px;
            padding: 4px 0;
            z-index: 9999;
        }
        .fm-context-item {
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1d273b;
        }
        .fm-context-item:hover { background: #f6f8fb; }
        .fm-context-item.danger { color: #d63939; }
        .fm-context-divider { height: 1px; background: #e6e7e9; margin: 4px 0; }
        .fm-upload-zone {
            border: 2px dashed #e6e7e9;
            border-radius: 6px;
            padding: 24px 16px;
            text-align: center;
            margin-bottom: 8px;
            transition: all 0.2s;
        }
        .fm-upload-zone.dropping {
            border-color: #206bc4;
            background: #e8f1fd;
        }
        .fm-lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.95);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .fm-lightbox img, .fm-lightbox video {
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
        }
        .fm-lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            color: #000;
        }
        .fm-lightbox-nav.prev { left: 20px; }
        .fm-lightbox-nav.next { right: 20px; }
        .fm-lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.9);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            color: #000;
        }

        /* ========== DARK MODE (data-bs-theme="dark") ========== */
        [data-bs-theme="dark"] .fm-toolbar {
            background: #182433 !important;
            border-color: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-toolbar .divider {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-sidebar {
            background: #101827 !important;
            border-color: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-section {
            border-color: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-title {
            color: #9ca3af !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-item {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-item:hover {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-item.active {
            background: #2563eb !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-item i {
            color: #9ca3af !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-item.active i {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-sidebar-item .count {
            color: #6b7280 !important;
        }
        [data-bs-theme="dark"] .fm-stats-text {
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .fm-stats-text strong {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-main {
            background: #182433 !important;
            border-color: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-breadcrumb {
            color: #d1d5db !important;
            border-color: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-breadcrumb a {
            color: #60a5fa !important;
        }
        [data-bs-theme="dark"] .fm-item {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-item:hover {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-item.selected {
            background: #1e3a5f !important;
            border-color: #3b82f6 !important;
        }
        [data-bs-theme="dark"] .fm-item-thumb {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-item-name {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-item-size {
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .fm-list-header {
            background: #101827 !important;
            border-color: #2d3a4d !important;
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .fm-list-row {
            border-color: #2d3a4d !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-list-row:hover {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-list-row.selected {
            background: #1e3a5f !important;
        }
        [data-bs-theme="dark"] .fm-list-row .text-muted {
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .fm-list-thumb {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-statusbar {
            background: #101827 !important;
            border-color: #2d3a4d !important;
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .fm-statusbar kbd {
            background: #2d3a4d !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-context {
            background: #182433 !important;
            border-color: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-context-item {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-context-item:hover {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-context-item.danger {
            color: #f87171 !important;
        }
        [data-bs-theme="dark"] .fm-context-divider {
            background: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .fm-upload-zone {
            border-color: #2d3a4d !important;
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .fm-upload-zone.dropping {
            background: #1e3a5f !important;
            border-color: #3b82f6 !important;
        }
        [data-bs-theme="dark"] .fm-empty-state {
            color: #9ca3af !important;
        }
        /* Form elements */
        [data-bs-theme="dark"] .fm-toolbar .form-control,
        [data-bs-theme="dark"] .fm-toolbar .form-select {
            background: #182433 !important;
            border-color: #2d3a4d !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .fm-toolbar .form-control::placeholder {
            color: #9ca3af !important;
        }
        [data-bs-theme="dark"] .fm-toolbar .input-icon-addon {
            background: #182433 !important;
            border-color: #2d3a4d !important;
            color: #9ca3af !important;
        }
        [data-bs-theme="dark"] .file-manager .badge {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .file-manager .badge.bg-secondary-lt {
            background: #374151 !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .file-manager .text-muted {
            color: #d1d5db !important;
        }
        /* Modal */
        [data-bs-theme="dark"] .file-manager .modal-content {
            background: #182433 !important;
            border-color: #2d3a4d !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .file-manager .modal-header {
            border-color: #2d3a4d !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .file-manager .modal-footer {
            border-color: #2d3a4d !important;
        }
        [data-bs-theme="dark"] .file-manager .modal-title {
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .file-manager .form-label {
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .file-manager .btn-close {
            filter: invert(1) !important;
        }
        [data-bs-theme="dark"] .file-manager .input-group-text {
            background: #101827 !important;
            border-color: #2d3a4d !important;
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .file-manager .form-control,
        [data-bs-theme="dark"] .file-manager .form-select {
            background: #182433 !important;
            border-color: #2d3a4d !important;
            color: #ffffff !important;
        }
        /* Pagination */
        [data-bs-theme="dark"] .file-manager .pagination .page-link,
        [data-bs-theme="dark"] .file-manager .page-link {
            background: #182433 !important;
            border-color: #2d3a4d !important;
            color: #d1d5db !important;
        }
        [data-bs-theme="dark"] .file-manager .pagination .page-item.active .page-link,
        [data-bs-theme="dark"] .file-manager .page-item.active .page-link {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .file-manager .pagination .page-item.disabled .page-link,
        [data-bs-theme="dark"] .file-manager .page-item.disabled .page-link {
            background: #101827 !important;
            border-color: #2d3a4d !important;
            color: #4b5563 !important;
        }
        [data-bs-theme="dark"] .file-manager .pagination .page-link:hover,
        [data-bs-theme="dark"] .file-manager .page-link:hover {
            background: #2d3a4d !important;
            border-color: #374151 !important;
            color: #ffffff !important;
        }
    </style>

    <!-- TOOLBAR -->
    <div class="fm-toolbar">
        <button class="btn btn-primary btn-sm" @click="$refs.uploader.click()" :disabled="isUploading" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Bilgisayardan dosya seç">
            <i class="fas fa-upload me-1"></i> Yükle
        </button>
        <input type="file" multiple x-ref="uploader" class="d-none" @change="handleUpload($event)" accept="image/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">

        <div class="divider"></div>

        <button class="btn btn-sm" :class="viewMode === 'grid' ? 'btn-secondary' : 'btn-ghost-secondary'" @click="viewMode = 'grid'; localStorage.setItem('fmViewMode', 'grid')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Izgara görünümü">
            <i class="fas fa-th-large"></i>
        </button>
        <button class="btn btn-sm" :class="viewMode === 'list' ? 'btn-secondary' : 'btn-ghost-secondary'" @click="viewMode = 'list'; localStorage.setItem('fmViewMode', 'list')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Liste görünümü">
            <i class="fas fa-list"></i>
        </button>

        <div class="divider"></div>

        <button class="btn btn-ghost-secondary btn-sm" @click="selectAll()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tümünü seç (Ctrl+A)">
            <i class="fas fa-check-double me-1"></i> Tümü
        </button>
        <button class="btn btn-ghost-danger btn-sm" @click="deleteSelected()" :disabled="selected.length === 0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Seçili dosyaları sil (Delete)">
            <i class="fas fa-trash me-1"></i> Sil
        </button>

        <div class="divider"></div>

        <!-- Thumbnail Size -->
        <div class="btn-group btn-group-sm" role="group">
            <button class="btn" :class="thumbSize === 'sm' ? 'btn-secondary' : 'btn-ghost-secondary'" @click="setThumbSize('sm')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Küçük önizleme">
                S
            </button>
            <button class="btn" :class="thumbSize === 'md' ? 'btn-secondary' : 'btn-ghost-secondary'" @click="setThumbSize('md')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Normal önizleme">
                M
            </button>
            <button class="btn" :class="thumbSize === 'lg' ? 'btn-secondary' : 'btn-ghost-secondary'" @click="setThumbSize('lg')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Büyük önizleme">
                L
            </button>
            <button class="btn" :class="thumbSize === 'xl' ? 'btn-secondary' : 'btn-ghost-secondary'" @click="setThumbSize('xl')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Çok büyük önizleme">
                XL
            </button>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            <div class="input-icon" style="width: 200px;">
                <span class="input-icon-addon"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control form-control-sm" wire:model.live.debounce.500ms="search" placeholder="Ara...">
            </div>
            <select class="form-select form-select-sm" wire:model.live="perPage" style="width: 80px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Sayfa başına dosya sayısı">
                <option value="48">48</option>
                <option value="96">96</option>
                <option value="150">150</option>
                <option value="200">200</option>
            </select>
        </div>
    </div>

    <!-- UPLOAD ZONE (Always visible) -->
    <div class="fm-upload-zone mx-2 mt-2"
         :class="{ 'dropping': isDragging }"
         @click="$refs.uploader.click()"
         @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false"
         @drop.prevent="isDragging = false; handleDrop($event)"
         style="cursor: pointer;">
        <i class="fas fa-cloud-upload-alt fa-lg text-primary me-2"></i>
        <span class="small">Dosyaları buraya sürükleyin veya tıklayarak seçin</span>
        <span class="text-muted ms-2 small">(Çoklu seçim desteklenir)</span>
    </div>

    <!-- BODY -->
    <div class="fm-body"
         @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false"
         @drop.prevent="isDragging = false; handleDrop($event)">

        <!-- SIDEBAR -->
        <div class="fm-sidebar">
            <!-- Collections -->
            <div class="fm-sidebar-section">
                <div class="fm-sidebar-title">Koleksiyonlar</div>
                <div class="fm-sidebar-item {{ !$collectionFilter ? 'active' : '' }}"
                     wire:click="$set('collectionFilter', null)">
                    <i class="fas fa-folder"></i>
                    <span>Tümü</span>
                    <span class="count">{{ $stats['total'] ?? 0 }}</span>
                </div>
                @foreach($availableCollections as $collection)
                    <div class="fm-sidebar-item {{ $collectionFilter === $collection ? 'active' : '' }}"
                         wire:click="$set('collectionFilter', '{{ $collection }}')">
                        <i class="fas fa-folder"></i>
                        <span>{{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::headline(str_replace('_', ' ', $collection)), 15) }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Types -->
            <div class="fm-sidebar-section">
                <div class="fm-sidebar-title">Dosya Türü</div>
                <div class="fm-sidebar-item {{ !$typeFilter ? 'active' : '' }}"
                     wire:click="$set('typeFilter', null)">
                    <i class="fas fa-file"></i>
                    <span>Tüm Türler</span>
                </div>
                @foreach($mediaTypes as $typeKey => $typeConfig)
                    <div class="fm-sidebar-item {{ $typeFilter === $typeKey ? 'active' : '' }}"
                         wire:click="$set('typeFilter', '{{ $typeKey }}')">
                        <i class="fas fa-{{ $typeKey === 'image' ? 'image' : ($typeKey === 'video' ? 'video' : ($typeKey === 'document' ? 'file-alt' : 'file')) }}"></i>
                        <span>{{ $typeConfig['label'] ?? \Illuminate\Support\Str::headline($typeKey) }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Date Filter -->
            <div class="fm-sidebar-section">
                <div class="fm-sidebar-title">Tarih</div>
                @foreach(['all' => 'Tümü', '24h' => 'Son 24 Saat', '7d' => 'Son 7 Gün', '30d' => 'Son 30 Gün'] as $key => $label)
                    <div class="fm-sidebar-item {{ $dateFilter === $key ? 'active' : '' }}"
                         wire:click="$set('dateFilter', '{{ $key }}')">
                        <i class="fas fa-clock"></i>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Stats -->
            <div class="fm-sidebar-section">
                <div class="fm-sidebar-title">İstatistikler</div>
                <div class="fm-stats-text" style="font-size: 11px;">
                    <div class="mb-1"><strong>{{ number_format($stats['total'] ?? 0) }}</strong> dosya</div>
                    <div class="mb-1"><strong>{{ $this->formatBytes($stats['total_size'] ?? 0, 1) }}</strong> toplam</div>
                    <div><strong>{{ number_format($stats['last_30_days'] ?? 0) }}</strong> son 30 gün</div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="fm-main">
            <!-- Breadcrumb -->
            <div class="fm-breadcrumb">
                <a href="#" wire:click.prevent="resetFilters">Medya Kütüphanesi</a>
                @if($collectionFilter)
                    <span class="mx-1">/</span>
                    <span>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $collectionFilter)) }}</span>
                @endif
                @if($typeFilter)
                    <span class="mx-1">/</span>
                    <span>{{ $mediaTypes[$typeFilter]['label'] ?? \Illuminate\Support\Str::headline($typeFilter) }}</span>
                @endif
                @if($search)
                    <span class="mx-1">/</span>
                    <span>"{{ $search }}"</span>
                @endif
            </div>

            <!-- Content -->
            <div class="fm-content" @click="clearSelection($event)" id="fm-content-area"
                 x-init="
                    mediaList = {{ Js::from($previewableMedia) }};
                    allMediaIds = {{ Js::from($mediaItems->pluck('id')->toArray()) }};
                    allMediaData = {{ Js::from($allMedia) }};
                 ">

                <!-- Loading Overlay -->
                <div wire:loading.class="active" wire:target="gotoPage, previousPage, nextPage, setPage, perPage, search, typeFilter, collectionFilter, dateFilter, moduleFilter, diskFilter, sortField, sortDirection" class="fm-loading-overlay">
                    <div class="fm-loading-spinner"></div>
                </div>

                <!-- Content hidden during loading -->
                <div wire:loading.remove wire:target="gotoPage, previousPage, nextPage, setPage, perPage, search, typeFilter, collectionFilter, dateFilter, moduleFilter, diskFilter, sortField, sortDirection">
                @if($mediaItems->count())
                    <!-- GRID VIEW -->
                    <div class="fm-grid" :class="'size-' + thumbSize" x-show="viewMode === 'grid'">
                        @foreach($mediaItems as $index => $media)
                            <div class="fm-item"
                                 :class="{ 'selected': selected.includes({{ $media->id }}) }"
                                 @click.stop="handleItemClick($event, {{ $media->id }}, {{ $index }})"
                                 @dblclick="openPreview({{ $media->id }})"
                                 @contextmenu.prevent.stop="openContext($event, {{ $media->id }})">
                                <div class="fm-item-thumb">
                                    @if($this->isPreviewable($media))
                                        <img src="{{ thumb($media, 300, 300, ['quality' => 80]) }}" alt="{{ $media->name }}" loading="lazy">
                                    @else
                                        <i class="fas fa-file fa-2x text-secondary"></i>
                                    @endif
                                </div>
                                <div class="fm-item-name" title="{{ $media->name ?? $media->file_name }}">
                                    {{ \Illuminate\Support\Str::limit($media->name ?? pathinfo($media->file_name, PATHINFO_FILENAME), 12) }}
                                </div>
                                <div class="fm-item-size">{{ $this->formatBytes($media->size) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- LIST VIEW -->
                    <div class="fm-list" x-show="viewMode === 'list'">
                        <div class="fm-list-header">
                            <div><input type="checkbox" @change="toggleAll($event)" :checked="selected.length === allMediaIds.length && allMediaIds.length > 0"></div>
                            <div></div>
                            <div>Ad</div>
                            <div>Boyut</div>
                            <div>Tür</div>
                            <div>Tarih</div>
                        </div>
                        @foreach($mediaItems as $index => $media)
                            <div class="fm-list-row"
                                 :class="{ 'selected': selected.includes({{ $media->id }}) }"
                                 @click.stop="handleItemClick($event, {{ $media->id }}, {{ $index }})"
                                 @dblclick="openPreview({{ $media->id }})"
                                 @contextmenu.prevent.stop="openContext($event, {{ $media->id }})">
                                <div><input type="checkbox" :checked="selected.includes({{ $media->id }})" @click.stop="toggleSelect({{ $media->id }})"></div>
                                <div>
                                    @if($this->isPreviewable($media))
                                        <img src="{{ thumb($media, 64, 64, ['quality' => 60]) }}" class="fm-list-thumb" loading="lazy">
                                    @else
                                        <div class="fm-list-thumb d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file text-secondary"></i>
                                        </div>
                                    @endif
                                </div>
                                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $media->name ?? $media->file_name }}
                                </div>
                                <div class="text-muted">{{ $this->formatBytes($media->size) }}</div>
                                <div><span class="badge bg-secondary-lt">{{ strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) }}</span></div>
                                <div class="text-muted">{{ optional($media->created_at)->format('d.m.y H:i') }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 fm-empty-state">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <div>Dosya bulunamadı</div>
                    </div>
                @endif
                </div>

            <!-- Pagination -->
            @if($mediaItems->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $mediaItems->links() }}
                </div>
            @endif
            </div>
        </div>
    </div>

    <!-- STATUS BAR -->
    <div class="fm-statusbar">
        <div>
            <span x-show="selected.length > 0">
                <strong x-text="selected.length"></strong> seçili |
            </span>
            {{ $mediaItems->total() }} dosya
        </div>
        <div class="d-flex align-items-center gap-2">
            <span x-show="selected.length === 1" class="text-muted small">
                <a href="#" @click.prevent="$wire.openEditModal(selected[0])" class="text-decoration-none">
                    <i class="fas fa-edit me-1"></i>Düzenle
                </a>
                <span class="mx-2">|</span>
                <a href="#" @click.prevent="openPreview(selected[0])" class="text-decoration-none">
                    <i class="fas fa-eye me-1"></i>Önizle
                </a>
            </span>
            <span class="text-muted small ms-2">
                Ctrl/⌘+Tık: Çoklu seç |
                Shift+Tık: Aralık seç |
                Çift tık: Önizle |
                Sağ tık: Menü
            </span>
        </div>
    </div>

    <!-- CONTEXT MENU -->
    <div class="fm-context" x-show="context.show" x-cloak
         :style="`left: ${context.x}px; top: ${context.y}px;`"
         @click.away="context.show = false">
        <div class="fm-context-item" @click="openPreview(context.id)">
            <i class="fas fa-eye"></i> Önizle
        </div>
        <div class="fm-context-item" @click="copyUrl(context.id)">
            <i class="fas fa-copy"></i> URL Kopyala
        </div>
        <div class="fm-context-item" @click="download(context.id)">
            <i class="fas fa-download"></i> İndir
        </div>
        <div class="fm-context-divider"></div>
        <div class="fm-context-item" @click="$wire.openEditModal(context.id); context.show = false;">
            <i class="fas fa-edit"></i> Düzenle
        </div>
        <div class="fm-context-divider"></div>
        <div class="fm-context-item danger" @click="deleteItem(context.id)">
            <i class="fas fa-trash"></i> Sil
        </div>
    </div>

    <!-- LIGHTBOX -->
    <template x-if="lightbox.show">
        <div class="fm-lightbox" @click="lightbox.show = false" @keydown.escape.window="lightbox.show = false"
             @keydown.arrow-left.window="prevMedia()" @keydown.arrow-right.window="nextMedia()">
            <button class="fm-lightbox-close" @click.stop="lightbox.show = false">
                <i class="fas fa-times"></i>
            </button>
            <button class="fm-lightbox-nav prev" @click.stop="prevMedia()" x-show="lightbox.index > 0">
                <i class="fas fa-chevron-left"></i>
            </button>
            <template x-if="!currentMedia.isVideo">
                <img :src="currentMedia.url" :alt="currentMedia.name" @click.stop>
            </template>
            <template x-if="currentMedia.isVideo">
                <video controls @click.stop>
                    <source :src="currentMedia.url" :type="currentMedia.mimeType">
                </video>
            </template>
            <button class="fm-lightbox-nav next" @click.stop="nextMedia()" x-show="lightbox.index < mediaList.length - 1">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </template>

    <!-- EDIT MODAL -->
    @if($editingMediaId)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form wire:submit.prevent="saveMedia">
                        <div class="modal-header">
                            <h5 class="modal-title">Medya Düzenle</h5>
                            <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Görünen Ad</label>
                                    <input type="text" class="form-control" wire:model.defer="editForm.name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dosya Adı</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" wire:model.defer="editForm.file_name">
                                        <span class="input-group-text">.{{ $editForm['extension'] ?? '' }}</span>
                                    </div>
                                </div>
                                @foreach($locales as $locale)
                                    <div class="col-md-6">
                                        <label class="form-label">Alt Text ({{ strtoupper($locale) }})</label>
                                        <input type="text" class="form-control" wire:model.defer="editForm.alt_text.{{ $locale }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" wire:click="closeEditModal">İptal</button>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
window.fileManager = function(uploadUrl, csrfToken, componentId) {
    return {
        // State
        viewMode: localStorage.getItem('fmViewMode') || 'grid',
        thumbSize: localStorage.getItem('fmThumbSize') || 'md',
        selected: [],
        lastIndex: null,
        isUploading: false,
        isDragging: false,
        showUpload: false,

        // Data
        allMediaIds: [],
        allMediaData: [],
        mediaList: [],

        // Context menu
        context: { show: false, x: 0, y: 0, id: null },

        // Lightbox
        lightbox: { show: false, index: 0 },

        get currentMedia() {
            return this.mediaList[this.lightbox.index] || {};
        },

        // Selection - FIXED shift+click
        handleItemClick(event, id, index) {
            if (event.shiftKey && this.lastIndex !== null) {
                // Shift+click: select range
                const start = Math.min(this.lastIndex, index);
                const end = Math.max(this.lastIndex, index);
                const rangeIds = this.allMediaIds.slice(start, end + 1);
                this.selected = [...new Set([...this.selected, ...rangeIds])];
            } else if (event.ctrlKey || event.metaKey) {
                // Ctrl+click: toggle single
                this.toggleSelect(id);
            } else {
                // Normal click: select only this
                this.selected = [id];
            }
            this.lastIndex = index;
            this.syncSelection();
        },

        toggleSelect(id) {
            const idx = this.selected.indexOf(id);
            if (idx === -1) {
                this.selected.push(id);
            } else {
                this.selected.splice(idx, 1);
            }
            this.syncSelection();
        },

        selectAll() {
            // Toggle: if all selected, deselect all; otherwise select all
            if (this.selected.length === this.allMediaIds.length) {
                this.selected = [];
            } else {
                this.selected = [...this.allMediaIds];
            }
            this.syncSelection();
        },

        clearSelection(event) {
            if (event.target.classList.contains('fm-content')) {
                this.selected = [];
                this.syncSelection();
            }
        },

        toggleAll(event) {
            if (event.target.checked) {
                this.selected = [...this.allMediaIds];
            } else {
                this.selected = [];
            }
            this.syncSelection();
        },

        syncSelection() {
            this.$wire.set('selectedItems', this.selected);
        },

        setThumbSize(size) {
            this.thumbSize = size;
            localStorage.setItem('fmThumbSize', size);
        },

        // Context menu
        openContext(event, id) {
            this.context = {
                show: true,
                x: Math.min(event.clientX, window.innerWidth - 180),
                y: Math.min(event.clientY, window.innerHeight - 250),
                id: id
            };
            if (!this.selected.includes(id)) {
                this.selected = [id];
                this.syncSelection();
            }
        },

        handleGlobalContext(event) {
            this.context.show = false;
        },

        // Actions
        copyUrl(id) {
            const media = this.allMediaData.find(m => m.id === id);
            if (media) {
                navigator.clipboard.writeText(media.url);
                this.$dispatch('toast', { title: 'Başarılı', message: 'URL kopyalandı', type: 'success' });
            }
            this.context.show = false;
        },

        download(id) {
            const media = this.allMediaData.find(m => m.id === id);
            if (media) {
                const link = document.createElement('a');
                link.href = media.url;
                link.download = media.file_name;
                link.click();
            }
            this.context.show = false;
        },

        deleteItem(id) {
            if (confirm('Bu dosyayı silmek istediğinize emin misiniz?')) {
                this.$wire.deleteMedia(id);
            }
            this.context.show = false;
        },

        deleteSelected() {
            if (this.selected.length === 0) return;
            if (confirm(`${this.selected.length} dosyayı silmek istediğinize emin misiniz?`)) {
                this.$wire.bulkDelete();
                this.selected = [];
            }
        },

        // Lightbox
        openPreview(id) {
            const index = this.mediaList.findIndex(m => m.id === id);
            if (index !== -1) {
                this.lightbox = { show: true, index: index };
                document.body.style.overflow = 'hidden';
            }
        },

        prevMedia() {
            if (this.lightbox.index > 0) this.lightbox.index--;
        },

        nextMedia() {
            if (this.lightbox.index < this.mediaList.length - 1) this.lightbox.index++;
        },

        // Keyboard
        handleKeydown(event) {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(event.target.tagName)) return;

            if (event.key === 'Escape') {
                if (this.lightbox.show) {
                    this.lightbox.show = false;
                    document.body.style.overflow = '';
                } else {
                    this.selected = [];
                    this.syncSelection();
                }
            }
            // Ctrl+A (Windows) or Cmd+A (Mac)
            if ((event.ctrlKey || event.metaKey) && event.key === 'a') {
                event.preventDefault();
                this.selectAll();
            }
            // Delete or Backspace (Mac)
            if ((event.key === 'Delete' || event.key === 'Backspace') && this.selected.length > 0) {
                event.preventDefault();
                this.deleteSelected();
            }
            if (event.key === 'F2' && this.selected.length === 1) {
                event.preventDefault();
                this.$wire.openEditModal(this.selected[0]);
            }
            if (event.key === 'Enter' && this.selected.length === 1) {
                event.preventDefault();
                this.openPreview(this.selected[0]);
            }
        },

        // Upload
        handleUpload(event) {
            const files = Array.from(event.target.files || []);
            if (files.length) this.upload(files);
            event.target.value = '';
        },

        handleDrop(event) {
            const files = Array.from(event.dataTransfer?.files || []);
            if (files.length) this.upload(files);
        },

        upload(files) {
            this.isUploading = true;
            const formData = new FormData();
            files.forEach(f => formData.append('files[]', f));
            formData.append('_token', csrfToken);

            fetch(uploadUrl.startsWith('http') ? uploadUrl : `${location.origin}${uploadUrl}`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                window.Livewire?.find(componentId)?.call('handleUploadCompleted', data.uploaded_count || 0, data.errors || []);
            })
            .catch(() => {
                this.$dispatch('toast', { title: 'Hata', message: 'Yükleme başarısız', type: 'error' });
            })
            .finally(() => {
                this.isUploading = false;
            });
        }
    };
};

// Initialize Bootstrap tooltips (with check)
function initTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            // Dispose existing tooltip if any
            const existing = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

document.addEventListener('DOMContentLoaded', initTooltips);

// Re-initialize tooltips after Livewire navigation
document.addEventListener('livewire:navigated', initTooltips);

// Scroll to top when page changes
document.addEventListener('livewire:init', function() {
    Livewire.hook('morph.updated', ({ el, component }) => {
        // Scroll content area to top after update
        const contentArea = document.getElementById('fm-content-area');
        if (contentArea) {
            contentArea.scrollTop = 0;
        }
    });
});
</script>
@endpush
