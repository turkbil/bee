{{-- 🚨 AI Credit Warning Component v3.0 - Global Notification System --}}
<div>
@if($showWarning && !$isDismissed)
    {{-- Fixed Position Alert Banner (Non-intrusive for normal warnings) --}}
    @if($warningType !== 'error')
    <div class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999; width: auto; max-width: 90vw;">
        <div class="alert {{ $this->getWarningClass() }} alert-dismissible d-flex align-items-center shadow-lg" role="alert" style="border-radius: 12px; min-width: 400px;">
            <i class="ti {{ $this->getWarningIcon() }} fs-3 me-3"></i>
            <div class="flex-grow-1">
                <div class="fw-bold">💳 AI Kredi {{ $this->getCreditSummary() }}</div>
                <div class="small">{{ $warningMessage }}</div>
                @if($currentBalance !== null)
                <div class="small opacity-75 mt-1">
                    Mevcut: {{ number_format($currentBalance, 2) }} kredi
                </div>
                @endif
            </div>
            <div class="ms-3">
                <button type="button" class="btn btn-sm btn-outline-primary me-2" wire:click="buyCredits" title="Kredi Satın Al">
                    <i class="fas fa-plus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="refreshCredits" title="Yenile">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button type="button" class="btn-close ms-2" wire:click="dismissWarning" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

@endif

{{-- Success Toast removed - kullanıcı her sayfa yüklenişinde mesaj görmek istemiyor --}}

</div>

{{-- JavaScript for Enhanced Functionality --}}
@push('script')
<script>

// Basit toast notification - GLOBAL
window.showToast = function(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
    toast.style.zIndex = '99999';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Credit Warning Component v3.0 Enhanced Scripts

    // Auto-refresh every 2 minutes (reduced from 5 minutes for better UX)
    setInterval(() => {
        if (typeof @this !== 'undefined') {
            @this.call('refreshCredits');
        }
    }, 120000);
    
    // Listen for AI operations to refresh credit status
    document.addEventListener('ai-operation-completed', function(e) {
        setTimeout(() => {
            if (typeof @this !== 'undefined') {
                @this.call('refreshCredits');
            }
        }, 1000);
    });
    
    // Listen for credit usage events
    document.addEventListener('ai-credit-used', function(e) {
        setTimeout(() => {
            if (typeof @this !== 'undefined') {
                @this.call('refreshBalance');
            }
        }, 500);
    });
    
    // Broadcast credit warnings to other components
    @if($showWarning)
    window.dispatchEvent(new CustomEvent('credit-warning-active', {
        detail: {
            type: '{{ $warningType }}',
            balance: {{ $currentBalance ?? 0 }},
            message: '{{ addslashes($warningMessage) }}'
        }
    }));
    @endif
    
    // Handle keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+Shift+C to refresh credits
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            if (typeof @this !== 'undefined') {
                @this.call('refreshCredits');
            }
        }
    });
    
    // Auto-hide non-critical warnings after 10 seconds
    @if($warningType === 'warning' && $showWarning)
    setTimeout(() => {
        if (typeof @this !== 'undefined') {
            @this.call('dismissWarning');
        }
    }, 10000);
    @endif
});

// Global function to manually refresh credit status
window.refreshAICredits = function() {
    if (typeof Livewire !== 'undefined') {
        Livewire.emit('refreshCreditWarning');
    }
};
</script>
@endpush

{{-- Enhanced CSS Styles --}}
@push('head')
<style>
/* AI Credit Warning Component v3.0 Styles */

/* Enhanced alert styling */
.alert.shadow-lg {
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2) !important;
}

/* Warning banner hover effects */
.alert:hover {
    transform: translateY(-2px);
    transition: transform 0.3s ease;
}

/* Progress bar enhancements */
.progress {
    border-radius: 10px;
    background-color: rgba(0, 0, 0, 0.1);
}

/* Button enhancements */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .position-fixed.top-0.start-50 {
        width: 95vw !important;
        max-width: none !important;
    }
    
    .alert {
        min-width: auto !important;
        font-size: 0.875rem;
    }
}

/* Dark mode compatibility */
@media (prefers-color-scheme: dark) {
    .alert-warning {
        --bs-alert-bg: rgba(255, 193, 7, 0.1);
        --bs-alert-border-color: rgba(255, 193, 7, 0.2);
    }
    
    .alert-danger {
        --bs-alert-bg: rgba(220, 53, 69, 0.1);
        --bs-alert-border-color: rgba(220, 53, 69, 0.2);
    }
}

/* Loading states */
.btn[wire\:loading] {
    opacity: 0.6;
    pointer-events: none;
}

/* Enhanced focus states for accessibility */
.btn:focus,
.btn-close:focus {
    outline: 2px solid rgba(13, 110, 253, 0.5);
    outline-offset: 2px;
}
</style>
@endpush