# AI Assistant Panel - Kullanım Kılavuzu

## 📁 Dosya Yapısı

Bu klasör AI Assistant panel sistemi için gerekli dosyaları içerir:

```
readme/ai-assistant/
├── ai-assistant-panel.blade.php   # Ana panel template dosyası
├── ai-assistant-styles.css        # CSS stil dosyası
├── ai-assistant-scripts.js        # JavaScript işlevsellik
└── README.md                      # Bu kılavuz dosyası
```

## 🚀 Entegrasyon

### 1. Blade Template Entegrasyonu

Panel'i sayfalara eklemek için admin layout dosyasına dahil edin:

```blade
{{-- Admin layout dosyasında --}}
@include('components.ai-assistant-panel')
```

### 2. CSS Entegrasyonu

Stil dosyasını layout'a ekleyin:

```blade
{{-- Head bölümünde --}}
<link rel="stylesheet" href="{{ asset('css/ai-assistant-styles.css') }}">
```

### 3. JavaScript Entegrasyonu

Script dosyasını layout'a ekleyin:

```blade
{{-- Body'nin sonunda --}}
<script src="{{ asset('js/ai-assistant-scripts.js') }}"></script>
```

## 🎯 Özellikler

### Ana Özellikler

- **Modern Floating Design**: Sağ alt köşede floating buton
- **AI Feature Entegrasyonu**: Dinamik AI özellikleri desteği
- **Chat Interface**: AI ile sohbet arayüzü
- **Real-time Analysis**: Canlı analiz sonuçları
- **Responsive Design**: Mobil uyumlu tasarım
- **Dark Mode Support**: Karanlık mod desteği

### AI İşlevleri

- **SEO Analizi**: Sayfa SEO skorlaması
- **İçerik Optimizasyonu**: AI tabanlı içerik önerileri
- **Anahtar Kelime Analizi**: Otomatik anahtar kelime çıkarımı
- **Çeviri Desteği**: Çoklu dil çeviri
- **Performans Analizi**: Sayfa performans değerlendirmesi

## 💻 Teknik Detaylar

### Livewire Entegrasyonu

Panel Livewire 3.5+ ile uyumludur:

```php
// Component'te AI metodları
public function runQuickAnalysis()
{
    // AI analiz işlemi
}

public function executeAIFeature($slug)
{
    // Dinamik AI feature çalıştırma
}
```

### JavaScript API

Panel çeşitli JavaScript fonksiyonları sunar:

```javascript
// Progress gösterme
showAiProgress('AI işlemi devam ediyor...');

// Sonuç gösterme
showAiResults(htmlContent);

// Chat mesajı ekleme
addAiMessage('Mesaj içeriği', false);
```

### Event System

Livewire event'leri ile iletişim:

```javascript
// Analysis tamamlandığında
Livewire.on('ai-analysis-complete', (event) => {
    // Sonuçları görüntüle
});

// Progress başladığında
Livewire.on('ai-progress-start', (event) => {
    // Progress göster
});
```

## 🎨 Tasarım Sistemi

### Renk Paleti

```css
/* Ana renkler */
--ai-primary: #667eea;
--ai-secondary: #764ba2;
--ai-success: #10b981;
--ai-warning: #f59e0b;
--ai-danger: #ef4444;
```

### Icon Mapping

```css
.ai-action-icon.seo { background: linear-gradient(135deg, #10b981, #059669); }
.ai-action-icon.content { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.ai-action-icon.translate { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
```

## 🔧 Özelleştirme

### Yeni AI Feature Ekleme

1. **Database**: `ai_features` tablosuna yeni feature ekleyin
2. **Icon**: CSS'te yeni icon sınıfı tanımlayın
3. **Method**: Component'te feature metodunu yazın

### Stil Özelleştirme

CSS değişkenlerini kullanarak renkleri özelleştirin:

```css
:root {
    --ai-custom-primary: #your-color;
    --ai-custom-secondary: #your-color;
}
```

## 📱 Responsive Davranış

Panel mobil cihazlarda otomatik olarak optimize edilir:

- Tablet: Panel genişliği sınırlanır
- Mobil: Full-width panel (margin ile)
- Touch: Touch-friendly buton boyutları

## 🐛 Debug Özellikleri

Panel debug bilgileri içerir:

- Session durumu kontrolü
- Property durumu kontrolü
- Timestamp bilgileri
- Livewire bağlantı durumu

## 📋 Kurulum Checklist

- [ ] Blade template dahil edildi
- [ ] CSS dosyası yüklendi
- [ ] JavaScript dosyası yüklendi
- [ ] Livewire component hazırlandı
- [ ] AI features database'e eklendi
- [ ] Event listener'lar tanımlandı

## 🚨 Gereksinimler

- Laravel 11+
- Livewire 3.5+
- PHP 8.2+
- Modern browser support
- AI module aktif

## 📞 Destek

Bu sistem CLAUDE.md kurallarına uygun olarak geliştirilmiştir. Herhangi bir sorun durumunda:

1. Console log'larını kontrol edin
2. Livewire bağlantısını doğrulayın
3. AI module'ün aktif olduğunu kontrol edin
4. Database bağlantısını test edin

---

💡 **Not**: Bu panel sistemi tamamen dinamik olarak çalışır ve hardcode içermez. Tüm AI features database'den çekilir ve runtime'da yüklenir.