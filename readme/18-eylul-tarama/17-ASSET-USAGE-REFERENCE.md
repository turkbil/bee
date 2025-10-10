# Asset Usage Examples

## In Blade Templates

### Frontend Layout
```blade
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}" defer></script>
```

### Admin Layout
```blade
<link rel="stylesheet" href="{{ asset('admin-assets/css/admin.css') }}">
<script src="{{ asset('admin-assets/js/admin.js') }}" defer></script>
```

### Using Helper Methods
```blade
{!! \App\Helpers\AssetHelper::preloadCss('css/app.css') !!}
{!! \App\Helpers\AssetHelper::deferJs('js/app.js') !!}
```

## Mobile Responsive Classes

### Grid System
```html
<div class="mobile-grid">
    <div class="card">Content</div>
</div>
```

### Utilities
```html
<div class="hide-mobile">Desktop only</div>
<div class="mobile-only">Mobile only</div>
```

## Admin Utils
```javascript
AdminUtils.showNotification('Success!', 'success');
AdminUtils.clearCache();
```
