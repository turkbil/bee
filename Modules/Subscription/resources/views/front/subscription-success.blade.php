<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonelik Başarılı!</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

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
                box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
            }
            50% {
                box-shadow: 0 0 40px rgba(102, 126, 234, 0.8);
            }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .success-icon i {
            color: white;
            font-size: 60px;
            animation: bounce 1s ease-in-out infinite;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 1.25rem;
            margin-bottom: 3rem;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-box {
            background: #f3f4f6;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }

        .detail-box i {
            font-size: 1.5rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .detail-label {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: #111827;
            font-weight: bold;
            font-size: 1.125rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .badge-trial {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 2px solid #10b981;
        }

        .badge-premium {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 2px solid #667eea;
        }

        .features {
            background: #f9fafb;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .features h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            color: #111827;
        }

        .features ul {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            list-style: none;
        }

        .features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #4b5563;
        }

        .features li i {
            color: #10b981;
            font-size: 1.25rem;
        }

        .cta-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1.125rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
        }

        .info-box {
            background: rgba(16, 185, 129, 0.1);
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .info-box i {
            color: #10b981;
            font-size: 1.5rem;
        }

        .info-box p {
            color: #065f46;
            margin: 0;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            .subtitle {
                font-size: 1rem;
            }
            .card {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div id="confetti-container"></div>

    <div class="container">
        <div class="card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>

            @if($isTrial)
                <h1>Tebrikler!</h1>
                <p class="subtitle">
                    <i class="fas fa-gift"></i> {{ $subscription->trial_days }} Günlük Ücretsiz Deneme Başladı
                </p>
            @else
                <h1>Hoş Geldin!</h1>
                <p class="subtitle">
                    <i class="fas fa-crown"></i> Premium Aboneliğin Aktif
                </p>
            @endif

            <div style="text-align: center;">
                @if($isTrial)
                    <span class="badge badge-trial">
                        <i class="fas fa-gift"></i>
                        Deneme Sürümü
                    </span>
                @else
                    <span class="badge badge-premium">
                        <i class="fas fa-crown"></i>
                        Premium Üye
                    </span>
                @endif
            </div>

            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 style="color: #111827; margin-bottom: 0.5rem;">{{ $subscription->plan->getTranslated('title') }}</h2>
                <p style="color: #6b7280;">{{ $subscription->getCycleLabel() ?? 'Abonelik' }}</p>
            </div>

            <div class="details-grid">
                <div class="detail-box">
                    <i class="fas fa-calendar-check"></i>
                    <div class="detail-label">Başlangıç Tarihi</div>
                    <div class="detail-value">{{ $subscription->started_at?->format('d.m.Y') ?? 'Şimdi' }}</div>
                </div>

                <div class="detail-box">
                    <i class="fas fa-hourglass-end"></i>
                    <div class="detail-label">{{ $isTrial ? 'Deneme Bitiş' : 'Bitiş' }} Tarihi</div>
                    <div class="detail-value">{{ $subscription->current_period_end?->format('d.m.Y') ?? '-' }}</div>
                </div>
            </div>

            @if($isTrial && $subscription->trial_ends_at)
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>
                        Deneme süreniz <strong>{{ $subscription->trial_ends_at->format('d.m.Y') }}</strong> tarihinde sona erecek.
                        Devam etmek için bir plan seçmeyi unutmayın!
                    </p>
                </div>
            @endif
        </div>

        <div class="card features">
            <h3>
                <i class="fas fa-star" style="color: #f59e0b;"></i>
                Senin İçin Neler Hazır?
            </h3>
            <ul>
                @php
                    $features = $subscription->plan->features ?? [
                        'Tüm premium özellikler',
                        'Öncelikli destek',
                        'Gelişmiş raporlama',
                        'Özel entegrasyonlar',
                        'Sınırsız kullanım',
                        'Tüm cihazlarda erişim'
                    ];
                @endphp
                @foreach($features as $feature)
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>{{ $feature }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="cta-buttons">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Ana Sayfaya Git
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-user"></i>
                Profilime Git
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('confetti-container');
        const colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#3b82f6', '#ec4899'];

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
</body>
</html>
