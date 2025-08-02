# 🎯 SETTING HELPER SİSTEMİ - DETAYLI KULLANIM KILAVUZU

## 📋 GENEL BAKIŞ

Setting Helper sistemi, Laravel projenizdeki ayarları kolayca yönetmenizi sağlayan güçlü bir helper fonksiyon setidir. Hem central hem de tenant bazlı ayarları yönetebilirsiniz.

## 🔧 ÇALIŞMA MANTIĞI

### 3 Katmanlı Değer Sistemi:
1. **Tenant Değeri**: Öncelik tenant'ın `settings_values` tablosundaki değerdedir
2. **Default Değer**: Tenant'ta yoksa central'daki `settings` tablosundan `default_value` alınır
3. **Fallback Değer**: O da yoksa fonksiyona verdiğiniz fallback değer kullanılır

## 📚 HELPER FONKSİYONLAR

### 1. `setting()` - Tekil Değer Okuma

```php
// ID ile kullanım
$primaryColor = setting(6);  // "#0ea5e9"

// KEY ile kullanım
$siteName = setting('site_name');  // "My Website"

// Fallback değerli kullanım
$siteTitle = setting('site_title', 'Varsayılan Site');

// Olmayan bir ayar için fallback
$logo = setting('site_logo', '/images/default-logo.png');
```

### 2. `settings()` - Çoklu Değer Okuma

```php
// Birden fazla ayarı tek seferde al
$siteSettings = settings([
    'site_name',
    'site_description', 
    'theme_primary_color',
    'contact_email'
]);

// Sonuç:
// [
//     'site_name' => 'My Website',
//     'site_description' => 'Welcome to our site',
//     'theme_primary_color' => '#0ea5e9',
//     'contact_email' => 'info@example.com'
// ]

// ID'ler ile de kullanılabilir
$colors = settings([6, 7, 8]); // theme colors
```

### 3. `setting_update()` - Değer Güncelleme (Sadece Tenant'ta)

```php
// KEY ile güncelleme
$result = setting_update('site_name', 'Yeni Site Adı');
// true: başarılı, false: başarısız

// ID ile güncelleme  
$result = setting_update(6, '#ff0000'); // Rengi kırmızı yap

// JSON değer güncelleme
$result = setting_update('social_media', json_encode([
    'facebook' => 'https://facebook.com/mypage',
    'twitter' => 'https://twitter.com/mypage'
]));

// NOT: Bu fonksiyon sadece tenant içinde çalışır!
// Central domain'de false döner
```

### 4. `setting_clear_cache()` - Cache Temizleme

```php
// Tüm setting cache'lerini temizle
setting_clear_cache();

// Kullanım senaryoları:
// - Toplu güncelleme sonrası
// - Debug/test sırasında
// - Sistem bakımı sonrası
```

## 🎨 GERÇEK KULLANIM ÖRNEKLERİ

### 1. Tema Renkleri

```php
// Layout dosyanızda (header.blade.php)
<style>
:root {
    --primary-color: {{ setting('theme_primary_color', '#0ea5e9') }};
    --secondary-color: {{ setting('theme_secondary_color', '#64748b') }};
    --dark-color: {{ setting('theme_dark_color', '#1e293b') }};
}
</style>
```

### 2. Site Başlığı ve Meta Taglar

```php
// app.blade.php veya layout dosyanızda
<head>
    <title>{{ setting('site_title', config('app.name')) }}</title>
    <meta name="description" content="{{ setting('site_description', 'Welcome to our website') }}">
    <meta name="keywords" content="{{ setting('site_keywords', '') }}">
    
    <!-- Social Media -->
    <meta property="og:title" content="{{ setting('og_title', setting('site_title')) }}">
    <meta property="og:image" content="{{ setting('og_image', '/images/og-default.jpg') }}">
</head>
```

### 3. Footer İletişim Bilgileri

```php
// footer.blade.php
<footer>
    <div class="contact-info">
        <p>📧 {{ setting('contact_email', 'info@example.com') }}</p>
        <p>📱 {{ setting('contact_phone', '+90 555 123 4567') }}</p>
        <p>📍 {{ setting('contact_address', 'İstanbul, Türkiye') }}</p>
    </div>
    
    @if($socialLinks = setting('social_media'))
        @php $social = json_decode($socialLinks, true) @endphp
        <div class="social-links">
            @if($social['facebook'] ?? false)
                <a href="{{ $social['facebook'] }}">Facebook</a>
            @endif
            @if($social['twitter'] ?? false)
                <a href="{{ $social['twitter'] }}">Twitter</a>
            @endif
        </div>
    @endif
</footer>
```

### 4. E-posta Ayarları

```php
// Mail gönderiminde
Mail::send('emails.contact', $data, function($message) {
    $message->from(
        setting('mail_from_address', config('mail.from.address')),
        setting('mail_from_name', config('mail.from.name'))
    );
    $message->subject(setting('mail_subject_prefix', '[Site]') . ' İletişim Formu');
});
```

### 5. Component'lerde Kullanım

```php
// Livewire Component
class SettingsComponent extends Component
{
    public $siteName;
    public $primaryColor;
    
    public function mount()
    {
        $this->siteName = setting('site_name', 'My Site');
        $this->primaryColor = setting('theme_primary_color', '#0ea5e9');
    }
    
    public function updateSettings()
    {
        setting_update('site_name', $this->siteName);
        setting_update('theme_primary_color', $this->primaryColor);
        
        // Cache'i temizle
        setting_clear_cache();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Ayarlar güncellendi'
        ]);
    }
}
```

### 6. Middleware'de Kullanım

```php
// Bakım modu middleware
class MaintenanceMode
{
    public function handle($request, Closure $next)
    {
        if (setting('maintenance_mode', false) === 'true') {
            if (!$request->is('admin/*')) {
                return response()->view('maintenance', [], 503);
            }
        }
        
        return $next($request);
    }
}
```

### 7. Controller'da Toplu Kullanım

```php
class HomeController extends Controller
{
    public function index()
    {
        // Tek seferde birden fazla ayar al
        $settings = settings([
            'home_hero_title',
            'home_hero_subtitle',
            'home_hero_image',
            'home_features_enabled',
            'home_testimonials_count'
        ]);
        
        return view('home', compact('settings'));
    }
}
```

### 8. API Response'larında

```php
// API Controller
public function getConfig()
{
    return response()->json([
        'site' => [
            'name' => setting('site_name'),
            'logo' => setting('site_logo'),
            'colors' => settings(['theme_primary_color', 'theme_secondary_color'])
        ],
        'features' => [
            'registration' => setting('enable_registration', true),
            'comments' => setting('enable_comments', true),
            'api_rate_limit' => setting('api_rate_limit', 60)
        ]
    ]);
}
```

## 🔥 İLERİ SEVİYE KULLANIM

### 1. Dinamik Form Builder

```php
// Ayar tipine göre dinamik form elemanı
@php
$settingType = setting('field_type_' . $field->id, 'text');
@endphp

@switch($settingType)
    @case('text')
        <input type="text" value="{{ setting($field->key) }}">
        @break
    @case('textarea')
        <textarea>{{ setting($field->key) }}</textarea>
        @break
    @case('select')
        <select>
            @foreach(json_decode(setting($field->key . '_options', '[]'), true) as $option)
                <option>{{ $option }}</option>
            @endforeach
        </select>
        @break
@endswitch
```

### 2. Performans Optimizasyonu

```php
// Sayfa başında tüm ayarları bir kerede çek
class BaseController extends Controller
{
    protected $siteSettings;
    
    public function __construct()
    {
        // Tüm sayfalarda kullanılacak ayarları cache'le
        $this->siteSettings = Cache::remember('site.settings', 3600, function() {
            return settings([
                'site_name',
                'site_logo',
                'theme_primary_color',
                'maintenance_mode',
                'google_analytics_id'
            ]);
        });
        
        View::share('siteSettings', $this->siteSettings);
    }
}
```

### 3. Tenant-Specific Özelleştirmeler

```php
// Tenant'a özel tema
@if(tenant())
    <link rel="stylesheet" href="/css/themes/{{ setting('tenant_theme', 'default') }}.css">
    
    @if(setting('tenant_custom_css'))
        <style>{{ setting('tenant_custom_css') }}</style>
    @endif
@endif

// Tenant'a özel logo
<img src="{{ setting('tenant_logo', setting('site_logo', '/images/logo.png')) }}" alt="Logo">
```

## 🛡️ GÜVENLİK VE EN İYİ UYGULAMALAR

### 1. HTML/JS İçeren Ayarlar

```php
// Güvenli kullanım
{!! setting('custom_footer_scripts') !!}  // Sadece güvenilir kaynaklardan

// Güvensiz içerik için
{{ setting('user_bio') }}  // HTML escape edilir
```

### 2. Tip Dönüşümleri

```php
// Boolean değerler
$maintenance = setting('maintenance_mode', 'false') === 'true';

// Numeric değerler
$itemsPerPage = (int) setting('items_per_page', 10);
$taxRate = (float) setting('tax_rate', 18.0);

// Array/JSON değerler
$features = json_decode(setting('enabled_features', '[]'), true);
```

### 3. Validation

```php
// Update öncesi validation
public function updateSetting($key, $value)
{
    $rules = [
        'contact_email' => 'email',
        'items_per_page' => 'integer|min:1|max:100',
        'theme_primary_color' => 'regex:/^#[0-9A-F]{6}$/i'
    ];
    
    if (isset($rules[$key])) {
        $validator = Validator::make([$key => $value], [$key => $rules[$key]]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    
    return setting_update($key, $value);
}
```

## 📊 PERFORMANS İPUÇLARI

1. **Toplu Okuma**: Tek tek `setting()` yerine `settings()` kullanın
2. **Cache Süresi**: Kritik ayarlar için cache süresini artırın
3. **Eager Loading**: İlişkili ayarları önceden yükleyin
4. **Static Ayarlar**: Değişmeyen ayarları config dosyalarına taşıyın

## 🔧 SORUN GİDERME

### Cache Sorunları
```php
// Cache temizleme
setting_clear_cache();
php artisan cache:clear
```

### Tenant Sorunları
```php
// Hangi tenant'tayız?
dd(tenant()?->id);

// Setting hangi tablodan geliyor?
$setting = Setting::find(1);
dd($setting->getValue()); // Tenant aware değer
```

## 📝 NOTLAR

- Setting helper'lar global olarak her yerden erişilebilir
- Cache mekanizması 1 saat (3600 saniye) varsayılan
- Tenant değişikliklerinde cache otomatik temizlenir
- Central domain'de `setting_update()` çalışmaz (sadece tenant'larda)

---

**Daha fazla bilgi için**: `Modules/SettingManagement/app/Helpers/setting_helpers.php`