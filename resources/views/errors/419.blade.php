<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oturum Süresi Doldu - 419</title>
    <link rel="stylesheet" href="/admin-assets/libs/fontawesome-pro@7.1.0/css/all.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #e2e8f0;
            padding: 1rem;
        }
        .container {
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .close-btn {
            position: fixed;
            top: 1rem;
            right: 1rem;
            width: 40px;
            height: 40px;
            background: #334155;
            border: none;
            border-radius: 50%;
            color: #e2e8f0;
            font-size: 1.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .close-btn:hover { background: #475569; }
        .icon-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        .icon-wrapper i { font-size: 2.5rem; color: white; }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #f1f5f9;
        }
        .description {
            color: #94a3b8;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        .info-box {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .info-box h3 {
            color: #93c5fd;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-box ul {
            list-style: none;
            font-size: 0.85rem;
            color: #bfdbfe;
        }
        .info-box li {
            padding: 0.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-box li i { color: #60a5fa; font-size: 0.75rem; }
        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }
        .btn {
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        .btn-primary:hover { filter: brightness(1.1); }
        .btn-secondary {
            background: #334155;
            color: #e2e8f0;
        }
        .btn-secondary:hover { background: #475569; }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        .btn-danger:hover { filter: brightness(1.1); }
    </style>
</head>
<body>
    <button class="close-btn" onclick="closeModal()" title="Kapat">
        <i class="fas fa-times"></i>
    </button>

    <div class="container">
        <div class="icon-wrapper">
            <i class="fas fa-clock-rotate-left"></i>
        </div>

        <h1>Oturum Süresi Doldu</h1>

        <p class="description">
            Formda çok uzun süre beklediniz. Güvenlik nedeniyle sayfayı yenilemeniz gerekiyor.
        </p>

        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Ne Yapmalıyım?</h3>
            <ul>
                <li><i class="fas fa-check"></i> Sayfayı yenileyin (F5)</li>
                <li><i class="fas fa-check"></i> Formu tekrar doldurun</li>
                <li><i class="fas fa-check"></i> İşleminize devam edin</li>
            </ul>
        </div>

        <div class="buttons">
            <button class="btn btn-primary" onclick="window.parent.location.reload()">
                <i class="fas fa-rotate-right"></i> Sayfayı Yenile
            </button>
            <button class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i> Kapat
            </button>
            <a href="/admin/login" class="btn btn-danger" target="_top">
                <i class="fas fa-sign-out-alt"></i> Yeniden Giriş Yap
            </a>
        </div>
    </div>

    <script>
        function closeModal() {
            // Livewire modal'ını kapat
            try {
                if (window.parent && window.parent.document) {
                    var modal = window.parent.document.getElementById('livewire-error');
                    if (modal) {
                        modal.close();
                        modal.remove();
                        window.parent.document.body.style.overflow = 'visible';
                    }
                }
            } catch(e) {
                // iframe cross-origin durumunda parent'a erişemeyebiliriz
                window.parent.location.reload();
            }
        }

        // ESC tuşu ile kapatma
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
