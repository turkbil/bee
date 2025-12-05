@extends('themes.muzibu.layouts.app')

@section('content')
<style>
@keyframes confetti-fall {
    0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
    }
}

.confetti {
    position: fixed;
    width: 10px;
    height: 10px;
    z-index: 9999;
    animation: confetti-fall 3s linear forwards;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.bounce {
    animation: bounce 1s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
    }
    50% {
        box-shadow: 0 0 40px rgba(239, 68, 68, 0.6);
    }
}

.pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}
</style>

<div class="min-h-screen bg-gradient-to-br from-spotify-black via-[#0a0a0a] to-spotify-black py-12 px-4">
    <div class="max-w-4xl mx-auto">

        {{-- Confetti Container --}}
        <div id="confetti-container"></div>

        {{-- Success Icon --}}
        <div class="text-center mb-8">
            <div class="inline-block relative">
                <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-r from-emerald-500 to-green-600 flex items-center justify-center pulse-glow">
                    <i class="fas fa-check text-white text-6xl bounce"></i>
                </div>
            </div>
        </div>

        {{-- Main Message --}}
        <div class="text-center mb-12">
            @if($isTrial)
                <h1 class="text-5xl md:text-6xl font-black text-white mb-4">
                    <span class="bg-gradient-to-r from-emerald-400 via-green-400 to-teal-400 bg-clip-text text-transparent">
                        Tebrikler!
                    </span>
                </h1>
                <p class="text-2xl md:text-3xl text-emerald-400 font-bold mb-2">
                    <i class="fas fa-gift mr-2"></i>{{ $subscription->trial_days }} Günlük Ücretsiz Deneme Başladı
                </p>
            @else
                <h1 class="text-5xl md:text-6xl font-black text-white mb-4">
                    <span class="bg-gradient-to-r from-muzibu-coral via-pink-500 to-purple-500 bg-clip-text text-transparent">
                        Hoş Geldin!
                    </span>
                </h1>
                <p class="text-2xl md:text-3xl text-muzibu-coral font-bold mb-2">
                    <i class="fas fa-crown mr-2"></i>Premium Aboneliğin Aktif
                </p>
            @endif
            <p class="text-gray-400 text-lg mt-4">
                Artık sınırsız müzik keyfinin tadını çıkarabilirsin
            </p>
        </div>

        {{-- Subscription Details Card --}}
        <div class="bg-gradient-to-br from-spotify-gray to-[#0a0a0a] rounded-3xl p-8 mb-8 border-2 {{ $isTrial ? 'border-emerald-500 shadow-2xl shadow-emerald-500/30' : 'border-muzibu-coral shadow-2xl shadow-muzibu-coral/30' }}">
            <div class="flex items-center justify-between mb-6 pb-6 border-b border-white/10">
                <div>
                    <h3 class="text-2xl font-bold text-white mb-1">
                        {{ $subscription->plan->getTranslated('title') }}
                    </h3>
                    <p class="text-gray-400">
                        {{ $subscription->getCycleLabel() ?? 'Abonelik' }}
                    </p>
                </div>
                <div class="text-right">
                    @if($isTrial)
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/20 border border-emerald-500/40 rounded-full">
                            <i class="fas fa-gift text-emerald-400"></i>
                            <span class="text-emerald-400 font-bold">Deneme</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-muzibu-coral/20 border border-muzibu-coral/40 rounded-full">
                            <i class="fas fa-crown text-muzibu-coral"></i>
                            <span class="text-muzibu-coral font-bold">Premium</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-black/30 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-calendar-check text-emerald-400 text-xl"></i>
                        <span class="text-gray-400 text-sm">Başlangıç Tarihi</span>
                    </div>
                    <p class="text-white font-bold text-lg">
                        {{ $subscription->started_at?->format('d.m.Y') ?? 'Şimdi' }}
                    </p>
                </div>

                <div class="bg-black/30 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-hourglass-end text-muzibu-coral text-xl"></i>
                        <span class="text-gray-400 text-sm">{{ $isTrial ? 'Deneme Bitiş' : 'Bitiş' }} Tarihi</span>
                    </div>
                    <p class="text-white font-bold text-lg">
                        {{ $subscription->current_period_end?->format('d.m.Y') ?? '-' }}
                    </p>
                </div>
            </div>

            @if($isTrial && $subscription->trial_ends_at)
                <div class="mt-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-info-circle text-emerald-400 text-xl"></i>
                        <p class="text-emerald-300 text-sm">
                            Deneme süreniz <strong>{{ $subscription->trial_ends_at->format('d.m.Y') }}</strong> tarihinde sona erecek. 
                            Devam etmek için bir plan seçmeyi unutmayın!
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Features --}}
        <div class="bg-gradient-to-br from-spotify-gray to-[#0a0a0a] rounded-3xl p-8 mb-8 border border-white/10">
            <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <i class="fas fa-star text-yellow-400"></i>
                Senin İçin Neler Hazır?
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
                @php
                    $features = $subscription->plan->features ?? [
                        'Sınırsız müzik dinleme',
                        'Reklamsız deneyim',
                        'Yüksek kaliteli ses',
                        'Çevrimdışı dinleme',
                        'Sınırsız playlist oluşturma',
                        'Tüm cihazlarda erişim'
                    ];
                @endphp
                @foreach($features as $feature)
                    <div class="flex items-center gap-3 p-4 bg-black/30 rounded-xl">
                        <i class="fas fa-check-circle text-emerald-400 text-xl"></i>
                        <span class="text-gray-300">{{ $feature }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('home') }}" 
               class="flex-1 py-4 px-8 bg-gradient-to-r from-muzibu-coral via-pink-500 to-purple-500 text-white rounded-xl font-bold text-lg text-center transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-muzibu-coral/50">
                <i class="fas fa-music mr-2"></i>Müzik Dinlemeye Başla
            </a>
            <a href="{{ route('dashboard') }}" 
               class="flex-1 py-4 px-8 bg-white/10 text-white border border-white/20 rounded-xl font-bold text-lg text-center transition-all duration-300 hover:bg-white/20">
                <i class="fas fa-user mr-2"></i>Profilime Git
            </a>
        </div>

    </div>
</div>

{{-- Confetti Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('confetti-container');
    const colors = ['#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'];
    
    function createConfetti() {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDelay = Math.random() * 0.5 + 's';
        confetti.style.animationDuration = (Math.random() * 2 + 3) + 's';
        container.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 4000);
    }
    
    // Initial burst
    for (let i = 0; i < 50; i++) {
        setTimeout(() => createConfetti(), i * 30);
    }
    
    // Continue for 3 seconds
    const interval = setInterval(createConfetti, 100);
    setTimeout(() => clearInterval(interval), 3000);
});
</script>
@endsection
