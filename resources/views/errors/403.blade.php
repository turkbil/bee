<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erişim Reddedildi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <style>
        .empty-header {
            font-size: 8rem;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 2rem;
            color: var(--tblr-danger);
        }
        
        .border-danger {
            border-top: 3px solid var(--tblr-danger) !important;
        }
        
        .icon-warning {
            color: var(--tblr-danger);
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .permission-card {
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .permission-card:hover {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="border-top-wide border-danger d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">403</div>
                <p class="empty-title">Erişim Reddedildi</p>
                <p class="empty-subtitle text-muted">
                    Bu sayfaya erişim yetkiniz bulunmamaktadır.
                </p>
                <div class="card permission-card mt-4 mb-4">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 9v4"/>
                            <path d="M10.363 3.591l-8.106 13.295a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.296a1.914 1.914 0 0 0 -3.274 0z"/>
                            <path d="M12 16h.01"/>
                        </svg>
                        <h3 class="mt-2">Yetkisiz Erişim</h3>
                        <p class="text-muted">Bu alana erişmek için gerekli izinlere sahip değilsiniz. Erişim izni gerekiyorsa lütfen sistem yöneticinizle iletişime geçin.</p>
                    </div>
                </div>
                <div class="empty-action">
                    <a href="{{ route('dashboard') }}" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l14 0"></path>
                            <path d="M5 12l6 6"></path>
                            <path d="M5 12l6 -6"></path>
                        </svg>
                        Kontrol Paneline Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>