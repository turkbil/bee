<div>
    {{-- Header --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-1">
                        <i class="fas fa-compact-disc me-2"></i>
                        {{ $this->album?->getTranslated('title', app()->getLocale()) ?? __('muzibu::admin.album') }}
                    </h3>
                    <p class="mb-0">
                        {{ __('muzibu::admin.bulk_upload.subtitle') }}
                    </p>
                </div>
                <a href="{{ route('admin.muzibu.album.manage', ['id' => $albumId]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('admin.back') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="card">
        <div class="card-body">
            {{-- Step 1: File Selection --}}
            @if(empty($uploadedFiles))
                <div class="text-center py-5">
                    <div class="upload-zone mb-4"
                         x-data="{ isDragging: false }"
                         x-on:dragover.prevent="isDragging = true"
                         x-on:dragleave.prevent="isDragging = false"
                         x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                         :class="{ 'border-primary bg-primary-lt': isDragging }"
                         style="border: 3px dashed #dee2e6; border-radius: 12px; padding: 60px 40px; cursor: pointer; transition: all 0.3s ease;"
                         onclick="document.getElementById('audioFilesInput').click()">

                        <i class="fas fa-cloud-upload-alt fa-4x mb-3"></i>
                        <h4 class="mb-2">{{ __('muzibu::admin.bulk_upload.drag_drop') }}</h4>
                        <p class="mb-3">{{ __('muzibu::admin.bulk_upload.or_click') }}</p>

                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <span class="badge bg-blue-lt">MP3</span>
                            <span class="badge bg-blue-lt">WAV</span>
                            <span class="badge bg-blue-lt">FLAC</span>
                            <span class="badge bg-blue-lt">M4A</span>
                            <span class="badge bg-blue-lt">OGG</span>
                        </div>
                        <p class="mt-2 mb-0" style="font-size: 0.85rem;">
                            {{ __('muzibu::admin.bulk_upload.max_size') }}: 100MB
                        </p>
                    </div>

                    <input type="file"
                           id="audioFilesInput"
                           x-ref="fileInput"
                           wire:model="audioFiles"
                           multiple
                           accept=".mp3,.wav,.flac,.m4a,.ogg"
                           class="d-none">
                </div>
            @else
                {{-- Step 2: Review & Edit --}}

                {{-- Bulk Genre Selection (PRIMARY) --}}
                <div class="mb-4 p-3 bg-light rounded">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-2">
                                <i class="fas fa-music me-1"></i>
                                {{ __('muzibu::admin.bulk_upload.bulk_genre') }}
                            </label>
                            <select wire:model.live="bulkGenreId" class="form-select">
                                <option value="">{{ __('muzibu::admin.song.select_genre') }}</option>
                                @foreach($this->activeGenres as $genre)
                                    <option value="{{ $genre->genre_id }}">
                                        {{ $genre->getTranslated('title', app()->getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input type="checkbox"
                                       wire:model.live="enableIndividualGenre"
                                       class="form-check-input"
                                       id="enableIndividualGenre">
                                <label class="form-check-label" for="enableIndividualGenre">
                                    {{ __('muzibu::admin.bulk_upload.enable_individual') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- File List Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="badge bg-blue">{{ count($uploadedFiles) }}</span>
                        {{ __('muzibu::admin.bulk_upload.files_ready') }}
                        <span class="ms-2">
                            ({{ __('muzibu::admin.bulk_upload.total_duration') }}: {{ $this->formatDuration($this->totalDuration) }})
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <label class="btn btn-outline-secondary btn-sm" style="cursor: pointer;">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('muzibu::admin.bulk_upload.add_more') }}
                            <input type="file"
                                   wire:model="audioFiles"
                                   multiple
                                   accept=".mp3,.wav,.flac,.m4a,.ogg"
                                   class="d-none">
                        </label>
                        <button wire:click="clearAll" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-1"></i>
                            {{ __('muzibu::admin.bulk_upload.clear_all') }}
                        </button>
                    </div>
                </div>

                {{-- File List --}}
                <div class="table-responsive mb-4">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>{{ __('muzibu::admin.song.title_field') }}</th>
                                <th style="width: 160px;">{{ __('muzibu::admin.song.genre') }}</th>
                                <th style="width: 80px;">{{ __('muzibu::admin.song.duration') }}</th>
                                <th style="width: 90px;">{{ __('admin.status') }}</th>
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uploadedFiles as $index => $file)
                                <tr wire:key="file-{{ $file['id'] }}">
                                    <td class="">{{ $index + 1 }}</td>
                                    <td>
                                        <input type="text"
                                               wire:blur="updateTitle('{{ $file['id'] }}', $event.target.value)"
                                               value="{{ $file['title'] }}"
                                               class="form-control form-control-sm"
                                               placeholder="{{ __('muzibu::admin.song.title_field') }}"
                                               @if($file['status'] === 'completed' || $file['status'] === 'processing') disabled @endif>
                                        @if($file['error'])
                                            <small class="text-danger">{{ $file['error'] }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($enableIndividualGenre)
                                            <select wire:change="updateFileGenre('{{ $file['id'] }}', $event.target.value)"
                                                    class="form-select form-select-sm"
                                                    @if($file['status'] === 'completed' || $file['status'] === 'processing') disabled @endif>
                                                <option value="">{{ __('muzibu::admin.bulk_upload.use_bulk') }}</option>
                                                @foreach($this->activeGenres as $genre)
                                                    <option value="{{ $genre->genre_id }}" @if($file['genre_id'] == $genre->genre_id) selected @endif>
                                                        {{ $genre->getTranslated('title', app()->getLocale()) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            @if($bulkGenreId)
                                                @php
                                                    $selectedGenre = $this->activeGenres->firstWhere('genre_id', $bulkGenreId);
                                                @endphp
                                                <span class="badge bg-green-lt">
                                                    {{ $selectedGenre?->getTranslated('title', app()->getLocale()) ?? '-' }}
                                                    <i class="fas fa-check ms-1"></i>
                                                </span>
                                            @else
                                                <span class="">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="">{{ $this->formatDuration($file['duration']) }}</span>
                                    </td>
                                    <td>
                                        @switch($file['status'])
                                            @case('pending')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ __('muzibu::admin.dashboard.pending') }}
                                                </span>
                                                @break
                                            @case('processing')
                                                <span class="badge bg-blue">
                                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                                    {{ __('admin.processing') }}
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-green">
                                                    <i class="fas fa-check me-1"></i>
                                                    {{ __('admin.success') }}
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-red">
                                                    <i class="fas fa-times me-1"></i>
                                                    {{ __('admin.error') }}
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($file['status'] === 'pending')
                                            <button wire:click="removeFile('{{ $file['id'] }}')"
                                                    class="btn btn-ghost-danger btn-sm"
                                                    title="{{ __('admin.delete') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($file['status'] === 'completed' && isset($file['song_id']))
                                            <a href="{{ route('admin.muzibu.song.manage', ['id' => $file['song_id']]) }}"
                                               class="btn btn-ghost-primary btn-sm"
                                               title="{{ __('admin.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.muzibu.album.manage', ['id' => $albumId]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        {{ __('admin.cancel') }}
                    </a>
                    @php
                        $hasPendingFiles = false;
                        foreach ($uploadedFiles as $f) {
                            if (($f['status'] ?? '') === 'pending') {
                                $hasPendingFiles = true;
                                break;
                            }
                        }
                    @endphp
                    <button wire:click="startUpload"
                            wire:loading.attr="disabled"
                            class="btn btn-primary"
                            @if(!$hasPendingFiles) disabled @endif>
                        <span wire:loading.remove wire:target="startUpload">
                            <i class="fas fa-upload me-1"></i>
                            {{ __('muzibu::admin.bulk_upload.start_upload') }}
                        </span>
                        <span wire:loading wire:target="startUpload">
                            <i class="fas fa-spinner fa-spin me-1"></i>
                            {{ __('admin.processing') }}...
                        </span>
                    </button>
                </div>
            @endif

            {{-- Loading Overlay --}}
            <div wire:loading.flex wire:target="audioFiles" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 justify-content-center align-items-center" style="z-index: 100;">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <p class="mb-0">{{ __('muzibu::admin.bulk_upload.analyzing') }}...</p>
                </div>
            </div>
        </div>
    </div>
</div>
