<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Names Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Module Names Test</h1>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3>Current Language: {{ app()->getLocale() }}</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Default Name</th>
                            <th>Custom Name ({{ app()->getLocale() }})</th>
                            <th>All Languages</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            use App\Services\ModuleSlugService;
                            $modules = ['Page', 'Portfolio', 'Announcement'];
                            $languages = ['tr', 'en', 'ar'];
                        @endphp
                        
                        @foreach($modules as $module)
                        <tr>
                            <td><strong>{{ $module }}</strong></td>
                            <td>{{ $module }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ ModuleSlugService::getModuleName($module, app()->getLocale()) }}
                                </span>
                            </td>
                            <td>
                                @foreach($languages as $lang)
                                    <div>
                                        <strong>{{ strtoupper($lang) }}:</strong> 
                                        {{ ModuleSlugService::getModuleName($module, $lang) }}
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3>Language Switcher</h3>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <a href="?locale=tr" class="btn btn-outline-primary {{ app()->getLocale() == 'tr' ? 'active' : '' }}">Türkçe</a>
                    <a href="?locale=en" class="btn btn-outline-primary {{ app()->getLocale() == 'en' ? 'active' : '' }}">English</a>
                    <a href="?locale=ar" class="btn btn-outline-primary {{ app()->getLocale() == 'ar' ? 'active' : '' }}">العربية</a>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3>Example Usage in Menu</h3>
            </div>
            <div class="card-body">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                        <div class="navbar-nav">
                            @foreach($modules as $module)
                                <a class="nav-link" href="#">
                                    {{ ModuleSlugService::getModuleName($module, app()->getLocale()) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</body>
</html>