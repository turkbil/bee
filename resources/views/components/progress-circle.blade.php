<div class="progress-circle-container">
    <div class="progress-circle progress-circle-{{ $size }}">
        <svg class="progress-svg" viewBox="0 0 {{ ($radius * 2) + ($strokeWidth * 2) }} {{ ($radius * 2) + ($strokeWidth * 2) }}">
            <!-- Background circle -->
            <circle 
                cx="{{ $radius + $strokeWidth }}" 
                cy="{{ $radius + $strokeWidth }}" 
                r="{{ $radius }}" 
                fill="none" 
                stroke="rgba(var(--tblr-muted-rgb, 255,255,255),0.1)" 
                stroke-width="{{ $strokeWidth }}">
            </circle>
            
            <!-- Progress circle -->
            <circle 
                cx="{{ $radius + $strokeWidth }}" 
                cy="{{ $radius + $strokeWidth }}" 
                r="{{ $radius }}" 
                fill="none" 
                stroke="url(#stepGradient-{{ $size }})" 
                stroke-width="{{ $strokeWidth }}" 
                stroke-dasharray="{{ $circumference }}" 
                stroke-dashoffset="{{ $strokeDashoffset }}" 
                transform="rotate(-90 {{ $radius + $strokeWidth }} {{ $radius + $strokeWidth }})" 
                stroke-linecap="round">
            </circle>
            
            <!-- Gradient definition -->
            <defs>
                <linearGradient id="stepGradient-{{ $size }}" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#00d4ff"></stop>
                    <stop offset="50%" style="stop-color:#9333ea"></stop>
                    <stop offset="100%" style="stop-color:#f59e0b"></stop>
                </linearGradient>
            </defs>
        </svg>
        
        <!-- Progress text -->
        <div class="progress-text">
            <span class="progress-percentage">{{ $percentage }}%</span>
            <small class="progress-label">{{ $percentage >= 100 ? 'Tamamlandı' : 'Devam ediyor' }}</small>
        </div>
    </div>
</div>

@push('styles')
<style>
.progress-circle-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.progress-circle {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.progress-circle-large {
    width: 120px;
    height: 120px;
}

.progress-circle-medium {
    width: 90px;
    height: 90px;
}

.progress-circle-small {
    width: 60px;
    height: 60px;
}

.progress-svg {
    width: 100%;
    height: 100%;
    transform: rotate(0deg);
}

.progress-text {
    position: absolute;
    text-align: center;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.progress-percentage {
    display: block;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--tblr-primary);
}

.progress-circle-medium .progress-percentage {
    font-size: 1rem;
}

.progress-circle-small .progress-percentage {
    font-size: 0.875rem;
}

.progress-label {
    display: block;
    font-size: 0.75rem;
    color: var(--tblr-muted);
    margin-top: 0.25rem;
}

.progress-circle-medium .progress-label {
    font-size: 0.6875rem;
}

.progress-circle-small .progress-label {
    font-size: 0.625rem;
}
</style>
@endpush