{{-- AI Credit Warning Component Root --}}
<div>
@if($showWarning && !$isDismissed)
    <div class="alert {{ $this->getWarningClass() }} alert-dismissible" role="alert" 
         x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2">
    
    <div class="d-flex align-items-center">
        <!-- Warning Icon -->
        <div class="me-3">
            <i class="ti {{ $this->getWarningIcon() }} fs-3"></i>
        </div>
        
        <!-- Warning Content -->
        <div class="flex-grow-1">
            <div class="fw-bold mb-1">
                💳 AI Kredi Uyarısı
            </div>
            <div class="mb-2">
                {{ $warningData['message'] ?? 'Kredi durumunuzu kontrol edin.' }}
            </div>
            
            <!-- Credit Details -->
            @php $details = $this->getCreditDetails(); @endphp
            <div class="small text-muted">
                <strong>Mevcut:</strong> {{ $details['current_credits'] }} kredi
                @if($details['warning_threshold'] > 0)
                    | <strong>Eşik:</strong> {{ $details['warning_threshold'] }} kredi
                    | <strong>Durum:</strong> {{ $details['percentage'] }}%
                @endif
            </div>
            
            <!-- Recommendation -->
            <div class="small mt-1">
                <em>{{ $details['recommendation'] }}</em>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="ms-3">
            <div class="btn-list">
                @if($warningType === 'critical')
                <button type="button" class="btn btn-sm btn-outline-light" wire:click="buyCredits" title="Kredi Satın Al">
                    <i class="fa-solid fa-shopping-cart me-1"></i>
                    Acil Kredi Al
                </button>
                @elseif($warningType === 'low')
                <button type="button" class="btn btn-sm btn-outline-light" wire:click="buyCredits" title="Kredi Satın Al">
                    <i class="fa-solid fa-plus me-1"></i>
                    Kredi Al
                </button>
                @endif
                
                <button type="button" class="btn btn-sm btn-outline-light" wire:click="refreshCredits" title="Yenile">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>
        
        <!-- Dismiss Button -->
        <button type="button" class="btn-close" wire:click="dismissWarning" title="Bugün için kapat"></button>
    </div>
    
    <!-- Progress Bar for Critical/Low Warnings -->
    @if(in_array($warningType, ['critical', 'low']) && $details['warning_threshold'] > 0)
    <div class="mt-3">
        <div class="progress progress-sm">
            @php 
                $percentage = min(100, max(0, $details['percentage']));
                $progressClass = $percentage <= 20 ? 'progress-bar-danger' : ($percentage <= 50 ? 'progress-bar-warning' : 'progress-bar-success');
            @endphp
            <div class="progress-bar {{ $progressClass }}" 
                 role="progressbar" 
                 style="width: {{ $percentage }}%" 
                 aria-valuenow="{{ $percentage }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {{ $percentage }}%
            </div>
        </div>
        <div class="small text-muted mt-1">
            Kredi durumu
        </div>
    </div>
    @endif
@endif

    <!-- Success Message for Refresh -->
    <div x-data="{ showSuccess: false }" 
         @credit-status-refreshed.window="showSuccess = true; setTimeout(() => showSuccess = false, 3000)">
        <div x-show="showSuccess" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="alert alert-success alert-dismissible mb-3">
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

// Show toast for critical credit warnings
@if($warningType === 'critical' && $showWarning)
toastr.error('🚨 KRİTİK: Kredileriniz tükenmek üzere! Hemen satın alın.', 'Kredi Uyarısı', {
    timeOut: 0,
    extendedTimeOut: 0,
    closeButton: true,
    tapToDismiss: false
});
@endif
</script>
@endpush

@push('head')
<style>
.credit-warning-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

.alert-danger {
    animation: pulse 3s infinite;
}
</style>
@endpush