<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Language Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-5">🔍 Admin Language Switcher Debug</h1>
        
        <!-- Mevcut Durum -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>📊 Mevcut Durum</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Session Site Language:</strong><br>
                        <span class="text-primary">{{ session('site_language', 'YOK') }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Session Locale:</strong><br>
                        <span class="text-success">{{ session('locale', 'YOK') }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>App Locale:</strong><br>
                        <span class="text-info">{{ app()->getLocale() }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Current Time:</strong><br>
                        <span class="text-muted">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Language Switcher Test -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>🔄 Language Switcher (Admin Style)</h5>
            </div>
            <div class="card-body">
                <div class="border border-primary border-2 p-3 rounded">
                    @livewire('languagemanagement::language-switcher', ['style' => 'dropdown', 'showText' => true, 'showFlags' => true])
                </div>
            </div>
        </div>

        <!-- TinyMCE Test -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>📝 TinyMCE Editor Test</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Türkçe Editor</label>
                        <textarea id="editor_tr" class="form-control">Türkçe test içeriği...</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">English Editor</label>
                        <textarea id="editor_en" class="form-control">English test content...</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Arabic Editor</label>
                        <textarea id="editor_ar" class="form-control">محتوى تجريبي عربي...</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Links -->
        <div class="card">
            <div class="card-header">
                <h5>🔗 Test Linkleri</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('debug.language.frontend') }}" class="btn btn-primary w-100 mb-2">
                            Frontend Test
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('debug.language.admin') }}" class="btn btn-danger w-100 mb-2">
                            Admin Test
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('debug.language.session') }}" target="_blank" class="btn btn-success w-100 mb-2">
                            Session JSON
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('debug.language.livewire') }}" target="_blank" class="btn btn-info w-100 mb-2">
                            Livewire JSON
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @include('components.head.tinymce-config')
</body>
</html>