<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Bakımda</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <style>
        .empty-header {
            font-size: 8rem;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 2rem;
            color: var(--tblr-primary);
        }

        .border-primary {
            border-top: 3px solid var(--tblr-primary) !important;
        }

        .icon-maintenance {
            color: var(--tblr-primary);
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-img">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-maintenance" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-10"/>
                        <path d="M3 7l9 -4l9 4"/>
                        <path d="M9 21v-8a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v8"/>
                    </svg>
                </div>
                <p class="empty-title">Site Bakımda</p>
                <p class="empty-subtitle text-muted">
                    Sitemiz şu anda bakımdadır. Lütfen daha sonra tekrar deneyiniz.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
