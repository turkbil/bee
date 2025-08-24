{{-- AI Credit Warning Component Root --}}
<div>
@if($showWarning && !$isDismissed)
    <!-- Modal -->
    <div class="modal fade" id="creditWarningModal" tabindex="-1" aria-labelledby="creditWarningModalLabel" aria-hidden="true"
         x-data="{ show: @js($showWarning && !$isDismissed) }" 
         x-show="show"
         x-init="if(show) { 
            setTimeout(() => { 
                new bootstrap.Modal(document.getElementById('creditWarningModal')).show(); 
            }, 500); 
         }">
        <div class="modal-dialog modal-dialog-centered {{ $warningType === 'critical' ? 'modal-lg' : 'modal-md' }}">
            <div class="modal-content border-{{ $warningType === 'critical' ? 'danger' : 'warning' }}">
                
                <!-- Modal Header -->
                <div class="modal-header bg-{{ $warningType === 'critical' ? 'danger' : 'warning' }} text-white">
                    <div class="d-flex align-items-center">
                        <i class="ti {{ $this->getWarningIcon() }} fs-2 me-3"></i>
                        <div>
                            <h4 class="modal-title mb-0" id="creditWarningModalLabel">
                                💳 AI Kredi Uyarısı
                            </h4>
                            <small class="opacity-75">
                                {{ $warningData['message'] ?? 'Kredi durumunuzu kontrol edin.' }}
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" wire:click="dismissWarning"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    @php $details = $this->getCreditDetails(); @endphp
                    
                    <!-- Critical Warning Message -->
                    @if($warningType === 'critical')
                    <div class="alert alert-danger mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-triangle-exclamation fs-3 me-3"></i>
                            <div>
                                <h5 class="mb-1">🚨 KRİTİK: Sadece {{ $details['current_credits'] }} kredi kaldı!</h5>
                                <p class="mb-0">Hemen kredi satın alın, AI özellikler çalışmayabilir.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Credit Status Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">📊 Kredi Durumu</h5>
                            
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="fs-2 fw-bold text-primary">{{ $details['current_credits'] }}</div>
                                        <small class="text-muted">Mevcut Kredi</small>
                                    </div>
                                </div>
                                
                                @if($details['warning_threshold'] > 0)
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="fs-2 fw-bold text-warning">{{ $details['warning_threshold'] }}</div>
                                        <small class="text-muted">Eşik Değeri</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="fs-2 fw-bold text-{{ $details['percentage'] <= 20 ? 'danger' : ($details['percentage'] <= 50 ? 'warning' : 'success') }}">
                                            {{ $details['percentage'] }}%
                                        </div>
                                        <small class="text-muted">Durum</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Progress Bar -->
                            @if($details['warning_threshold'] > 0)
                            <div class="mt-3">
                                @php 
                                    $percentage = min(100, max(0, $details['percentage']));
                                    $progressClass = $percentage <= 20 ? 'bg-danger' : ($percentage <= 50 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $progressClass }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="small text-muted mt-1">
                                    Kredi kullanım oranı
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recommendation -->
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-lightbulb me-2"></i>
                            <div>
                                <strong>Öneri:</strong> {{ $details['recommendation'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-outline-secondary" wire:click="refreshCredits" title="Kredi durumunu yenile">
                                <i class="fa-solid fa-rotate-right me-1"></i>
                                Yenile
                            </button>
                        </div>
                        
                        <div>
                            @if($warningType === 'critical')
                            <button type="button" class="btn btn-danger me-2" wire:click="buyCredits">
                                <i class="fa-solid fa-shopping-cart me-1"></i>
                                Acil Kredi Al
                            </button>
                            @elseif($warningType === 'low')
                            <button type="button" class="btn btn-warning me-2" wire:click="buyCredits">
                                <i class="fa-solid fa-plus me-1"></i>
                                Kredi Al
                            </button>
                            @endif
                            
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="dismissWarning">
                                Bugün için kapat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

    <!-- Success Message for Refresh -->
    <div x-data="{ showSuccess: false }" 
         @credit-status-refreshed.window="showSuccess = true; setTimeout(() => showSuccess = false, 3000);">
        <div x-show="showSuccess" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="alert alert-success alert-dismissible mb-3 position-fixed"
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ✅ Kredi durumu güncellendi!
        </div>
    </div>

</div> {{-- Root div close --}}


@push('script')
<script>
// Auto-refresh credit status every 5 minutes
setInterval(() => {
    @this.call('refreshCredits');
}, 300000);

// Listen for credit usage events to refresh status
document.addEventListener('ai-credit-used', function() {
    setTimeout(() => {
        @this.call('refreshCredits');
    }, 1000);
});

// Show modal for critical credit warnings (backup in case Alpine.js fails)
@if($warningType === 'critical' && $showWarning)
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('creditWarningModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }, 1000);
});
@endif
</script>
@endpush

@push('head')
<style>
.modal-content.border-danger {
    border-color: #dc3545 !important;
    border-width: 3px;
    box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.3);
}

.modal-content.border-warning {
    border-color: #ffc107 !important;
    border-width: 3px;
    box-shadow: 0 0.5rem 1rem rgba(255, 193, 7, 0.3);
}

.modal-header.bg-danger,
.modal-header.bg-warning {
    border-bottom: none;
}

.modal-body .progress {
    border-radius: 10px;
    overflow: hidden;
}

.modal-body .alert {
    border-radius: 10px;
}

/* Pulse animation for critical warnings */
@if($warningType === 'critical')
.modal-content.border-danger {
    animation: dangerPulse 2s infinite;
}

@keyframes dangerPulse {
    0% {
        box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.3);
    }
    50% {
        box-shadow: 0 0.5rem 2rem rgba(220, 53, 69, 0.6);
    }
    100% {
        box-shadow: 0 0.5rem 1rem rgba(220, 53, 69, 0.3);
    }
}
@endif
</style>
@endpush