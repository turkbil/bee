# 🧩 B1 | Widget Geliştirme Rehberi

> **Amaç**: Pratik widget geliştirme için step-by-step rehber  
> **Hedef Kitle**: Geliştiriciler, yapay zeka asistanları  
> **Sistem Durumu**: Bebek aşamasında - temel yapı var, geliştirmeler yapılacak

---

## 🎯 Widget Sistemi Felsefesi

### Temel Prensip: "Basitlik Güçtür"

**4 widget türü yeterli, çünkü:**
- `static` → Basit HTML/CSS snippet'ler
- `dynamic` → Database verili widget'lar  
- `file` → **Sihirli tür - Her şeyi yapabilir!**
- `module` → Derin sistem entegrasyonu

**Önemli**: `file` türü ile API, form, conditional, complex logic - her şey yapılabilir. **Yeni tür eklemeye gerek yok!**

---

## 📁 Widget Dosya Yapısı

### Klasör Organizasyonu
```
Modules/WidgetManagement/resources/views/blocks/
├── api/
│   ├── weather.blade.php
│   ├── currency.blade.php
│   └── social-feed.blade.php
├── forms/
│   ├── contact.blade.php
│   ├── newsletter.blade.php
│   └── survey.blade.php
├── conditional/
│   ├── user-role.blade.php
│   ├── time-based.blade.php
│   └── device-specific.blade.php
├── content/
│   ├── hero-banner.blade.php
│   ├── testimonial.blade.php
│   └── pricing-table.blade.php
└── layout/
    ├── grid-container.blade.php
    ├── tabs.blade.php
    └── accordion.blade.php
```

### Widget Blade Template Yapısı
```blade
{{-- resources/views/blocks/api/weather.blade.php --}}
@php
    $city = $settings['city'] ?? 'Istanbul';
    $apiKey = config('services.weather.api_key');
    
    try {
        $weather = Cache::remember("weather_{$city}", 1800, function() use ($city, $apiKey) {
            return Http::get("api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric'
            ])->json();
        });
    } catch (Exception $e) {
        $weather = null;
    }
@endphp

<div class="weather-widget">
    @if($weather)
        <div class="weather-info">
            <h3>{{ $weather['name'] }} Hava Durumu</h3>
            <div class="temperature">{{ round($weather['main']['temp']) }}°C</div>
            <div class="description">{{ $weather['weather'][0]['description'] }}</div>
        </div>
    @else
        <div class="weather-error">
            Hava durumu bilgisi alınamadı.
        </div>
    @endif
</div>

<style>
.weather-widget {
    background: linear-gradient(135deg, #74b9ff, #0984e3);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
.temperature {
    font-size: 2.5em;
    font-weight: bold;
    margin: 10px 0;
}
</style>
```

---

## 🛠️ Widget Türleri Detaylı Kullanım

### 1. Static Widget
**Kullanım**: Basit HTML/CSS snippet'ler
```php
// Widget oluştururken
'type' => 'static',
'content_html' => '<div class="alert alert-info">Bu bir bilgi mesajıdır</div>',
'content_css' => '.alert { padding: 15px; border-radius: 5px; }',
'file_path' => null // Kullanılmaz
```

### 2. Dynamic Widget  
**Kullanım**: Database'den dinamik veri
```php
// Widget oluştururken
'type' => 'dynamic',
'content_html' => '<div>Son {{count}} makale: {{#each articles}}<h3>{{title}}</h3>{{/each}}</div>',
'has_items' => true, // Önemli!
'file_path' => null
```

### 3. File Widget (★ En Güçlü Tür)
**Kullanım**: Her şey! API, form, conditional logic
```php
// Widget oluştururken  
'type' => 'file',
'file_path' => 'api/weather', // resources/views/blocks/api/weather.blade.php
'content_html' => null,
'settings_schema' => [
    [
        'name' => 'city',
        'label' => 'Şehir',
        'type' => 'text',
        'required' => true,
        'default' => 'Istanbul'
    ]
]
```

### 4. Module Widget
**Kullanım**: Sistem modülü entegrasyonu  
```php
// Widget oluştururken
'type' => 'module',  
'file_path' => 'user-list', // Özel işlem gerektirir
'module_ids' => [1, 2, 3] // Hangi modüllerde kullanılabilir
```

---

## 📝 Widget Oluşturma Adımları

### Adım 1: Widget Kaydını Oluştur
```php
use Modules\WidgetManagement\App\Models\Widget;

Widget::create([
    'name' => 'Hava Durumu',
    'slug' => 'weather-widget',
    'description' => 'Şehir bazlı hava durumu bilgisi gösterir',
    'type' => 'file',
    'file_path' => 'api/weather',
    'widget_category_id' => 1,
    'thumbnail' => '/images/widgets/weather.png',
    'settings_schema' => [
        [
            'name' => 'city',
            'label' => 'Şehir',
            'type' => 'text',
            'required' => true,
            'default' => 'Istanbul'
        ],
        [
            'name' => 'show_forecast',
            'label' => '5 Günlük Tahmin',
            'type' => 'switch',
            'default' => false
        ]
    ],
    'is_active' => true
]);
```

### Adım 2: Blade Template Oluştur
```bash
# Dosya konumu
/Modules/WidgetManagement/resources/views/blocks/api/weather.blade.php
```

### Adım 3: Widget'ı Tenant'a Ekle (Opsiyonel)
```php
use Modules\WidgetManagement\App\Models\TenantWidget;

TenantWidget::create([
    'widget_id' => $widget->id,
    'settings' => [
        'city' => 'Ankara',
        'show_forecast' => true
    ],
    'display_title' => 'Ankara Hava Durumu',
    'is_active' => true,
    'order' => 1
]);
```

---

## 🎨 Widget Template Örnekleri

### API Widget Örneği
```blade
{{-- blocks/api/currency.blade.php --}}
@php
    $from = $settings['from_currency'] ?? 'USD';
    $to = $settings['to_currency'] ?? 'TRY';
    
    $rate = Cache::remember("currency_{$from}_{$to}", 3600, function() use ($from, $to) {
        $response = Http::get("api.exchangerate-api.com/v4/latest/{$from}");
        return $response->json()['rates'][$to] ?? null;
    });
@endphp

<div class="currency-widget">
    @if($rate)
        <div class="rate-display">
            1 {{ $from }} = {{ number_format($rate, 4) }} {{ $to }}
        </div>
        <small>Son güncelleme: {{ now()->format('H:i') }}</small>
    @else
        <div class="error">Döviz kuru alınamadı</div>
    @endif
</div>
```

### Form Widget Örneği
```blade
{{-- blocks/forms/contact.blade.php --}}
<div class="contact-form-widget">
    <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
        @csrf
        
        <div class="form-group">
            <label for="name">Ad Soyad</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="message">Mesaj</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        
        <button type="submit" class="btn-submit">Gönder</button>
    </form>
</div>

<style>
.contact-form-widget {
    max-width: 500px;
    margin: 0 auto;
}
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.btn-submit {
    background: #007bff;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
</style>
```

### Conditional Widget Örneği
```blade
{{-- blocks/conditional/user-role.blade.php --}}
@php
    $user = auth()->user();
    $showPremiumContent = $user && $user->role === 'premium';
    $showTimeBasedContent = now()->hour >= 9 && now()->hour <= 17;
@endphp

<div class="conditional-widget">
    @if($showPremiumContent)
        <div class="premium-content">
            <h3>Premium İçerik</h3>
            <p>Bu içerik sadece premium üyelerimiz için görünür.</p>
            <a href="/premium-features" class="btn btn-gold">Premium Özellikler</a>
        </div>
    @elseif(auth()->check())
        <div class="user-content">
            <h3>Hoşgeldin {{ $user->name }}!</h3>
            <p>Premium üyeliğe geçmek için <a href="/upgrade">tıklayın</a>.</p>
        </div>
    @else
        <div class="guest-content">
            <h3>Giriş Yapın</h3>
            <p>Özel içerikleri görmek için <a href="/login">giriş yapın</a>.</p>
        </div>
    @endif
    
    @if($showTimeBasedContent)
        <div class="business-hours">
            <small>🕒 Çalışma saatleri içindesiniz (09:00 - 17:00)</small>
        </div>
    @endif
</div>
```

---

## ⚙️ Settings Schema Kullanımı

### Temel Field Türleri
```php
'settings_schema' => [
    // Text Input
    [
        'name' => 'title',
        'label' => 'Başlık',
        'type' => 'text',
        'required' => true,
        'default' => 'Widget Başlığı'
    ],
    
    // Number Input
    [
        'name' => 'count',
        'label' => 'Gösterilecek Adet',
        'type' => 'number',
        'min' => 1,
        'max' => 50,
        'default' => 10
    ],
    
    // Switch/Toggle
    [
        'name' => 'show_date',
        'label' => 'Tarihi Göster',
        'type' => 'switch',
        'default' => true
    ],
    
    // Select/Dropdown
    [
        'name' => 'layout',
        'label' => 'Düzen',
        'type' => 'select',
        'options' => [
            'grid' => 'Grid',
            'list' => 'Liste',
            'carousel' => 'Carousel'
        ],
        'default' => 'grid'
    ],
    
    // Textarea
    [
        'name' => 'description',
        'label' => 'Açıklama',
        'type' => 'textarea',
        'rows' => 3
    ]
]
```

---

## 🔧 Geliştirme Best Practices

### 1. Error Handling
```php
@php
    try {
        $data = Http::timeout(5)->get($apiUrl);
    } catch (Exception $e) {
        Log::error('Widget API Error: ' . $e->getMessage());
        $data = null;
    }
@endphp

@if($data)
    {{-- Normal görünüm --}}
@else
    <div class="widget-error">
        Veri yüklenirken bir hata oluştu.
    </div>
@endif
```

### 2. Caching
```php
@php
    $cacheKey = "widget_{$settings['type']}_{$settings['id']}";
    $cacheDuration = $settings['cache_minutes'] ?? 60;
    
    $data = Cache::remember($cacheKey, $cacheDuration, function() {
        return expensiveDataOperation();
    });
@endphp
```

### 3. Security
```php
@php
    // User input'ları temizle
    $userCity = Str::slug($settings['city'] ?? '');
    $userLimit = min(max((int)($settings['limit'] ?? 10), 1), 100);
    
    // XSS koruması için escape
    $safeTitle = e($settings['title'] ?? '');
@endphp
```

### 4. Responsive Design
```css
/* Widget CSS'lerinde mobile-first yaklaşım */
.my-widget {
    padding: 1rem;
}

@media (min-width: 768px) {
    .my-widget {
        padding: 2rem;
        display: flex;
        align-items: center;
    }
}
```

---

## 🚀 Sistem Geliştirme Roadmap

### Mevcut Durum (Bebek Aşama)
- [x] Temel widget türleri var
- [x] File-based widget sistemi çalışıyor  
- [x] Studio entegrasyonu aktif
- [ ] Güvenlik açıkları var (kritik!)
- [ ] Widget builder UI yok
- [ ] Template library yok

### Yakın Gelecek (2-4 hafta)
- [ ] Güvenlik açıkları kapatılacak
- [ ] Widget builder tool geliştirilecek
- [ ] Hazır template library oluşturulacak
- [ ] Performance optimizasyonları

### Orta Vadeli (2-3 ay)
- [ ] Widget marketplace
- [ ] Community widget sharing
- [ ] Advanced widget features
- [ ] Analytics & monitoring

---

## 💡 Geliştiriciler İçin Hızlı Başlangıç

### 5 Dakikada Widget Oluştur

1. **Widget kaydı oluştur** (database)
2. **Blade dosyası yaz** (`resources/views/blocks/`)
3. **Studio'da test et** (drag & drop)
4. **Settings ekle** (isteğe bağlı)  
5. **Tenant'a aktar** (production'da kullan)

**Bu kadar basit!** 🎉

---

> **Not**: Bu dokümantasyon sistem geliştikçe güncellenecek. Şu an temel yapıyı anlamak için hazırlandı.