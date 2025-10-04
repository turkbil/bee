@if ($bulkActionsEnabled)
    <div x-data="{
        confirmDelete: false,
        countdown: 5,
        countdownInterval: null,

        startDelete() {
            this.confirmDelete = true;
            this.countdown = 5;

            // Geri sayım başlat
            this.countdownInterval = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    this.cancelDelete();
                }
            }, 1000);
        },

        cancelDelete() {
            this.confirmDelete = false;
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }
            this.countdown = 5;
        },

        executeDelete() {
            // Clear timers first
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }

            // Reset state BEFORE delete
            this.confirmDelete = false;
            this.countdown = 5;

            // Direkt silme işlemi (modal yok)
            $wire.bulkDelete();
        }
    }"
    x-show="{{ count($selectedItems) > 0 }}"
    style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); margin-bottom: 1rem; z-index: 1000;"
    class="mb-4">
        <div class="card shadow-lg border-0 rounded-lg"
            style="backdrop-filter: blur(12px); background: var(--tblr-bg-surface);">
            <span class="badge bg-red badge-notification badge-blink"></span>
            <div class="card-body p-3">
                <template x-if="!confirmDelete">
                    <!-- Normal State -->
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
                        <span class="text-muted small">
                            {{ count($selectedItems) }} {{ __('portfolio::admin.items_selected') }}
                        </span>
                        <button type="button"
                                class="btn btn-sm btn-outline-success px-3 py-1 hover-btn"
                                wire:click="bulkToggleActive(true)">
                            <i class="fas fa-check me-2"></i>
                            <span>{{ __('portfolio::admin.activate') }}</span>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-outline-warning px-3 py-1 hover-btn"
                                wire:click="bulkToggleActive(false)">
                            <i class="fas fa-times me-2"></i>
                            <span>{{ __('portfolio::admin.deactivate') }}</span>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-outline-danger px-3 py-1 hover-btn"
                                @click="startDelete()">
                            <i class="fas fa-trash me-2"></i>
                            <span>{{ __('admin.delete') }}</span>
                        </button>
                    </div>
                </template>

                <template x-if="confirmDelete">
                    <!-- Confirmation State -->
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
                            <span class="text-danger fw-bold">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('admin.are_you_sure') }}
                            </span>
                            <button type="button"
                                    class="btn btn-sm btn-danger px-3 py-1"
                                    @click="executeDelete()">
                                <i class="fas fa-check me-2"></i>
                                <span>{{ __('admin.yes_delete') }}</span>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-secondary px-3 py-1"
                                    @click="cancelDelete()">
                                <i class="fas fa-times me-2"></i>
                                <span>{{ __('admin.cancel') }}</span>
                            </button>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                <span x-text="countdown"></span> {{ __('admin.seconds_auto_cancel') }}
                            </small>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Livewire Selection Change Listener --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('selection-changed', (count) => {
                Alpine.store('app').showBulkBar = count > 0;
            });
        });
    </script>
@endif
