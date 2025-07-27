# AI Assistant Panel - Kullanım Kılavuzu

## 📋 İçerik

Bu klasörde AI assistant panel dosyaları ve kodları bulunmaktadır.

## 📁 Dosyalar

### 1. `ai-assistant-panel.blade.php`
- **Açıklama**: Ana Blade template dosyası (CSS ve JS kodları kaldırılmış)
- **İçerik**: Sadece HTML yapısı ve Livewire entegrasyonu
- **Kullanım**: Include olarak Blade template'lere dahil edilir

### 2. `ai-assistant-styles.css`
- **Açıklama**: Tüm CSS stilleri
- **Özellikler**:
  - Modern floating design
  - Responsive tasarım
  - Dark mode desteği
  - Animasyonlar ve geçişler

### 3. `ai-assistant-scripts.js`
- **Açıklama**: Tüm JavaScript kodları
- **Özellikler**:
  - Panel açma/kapama işlevselliği
  - Livewire event listener'ları
  - AI işlem takip sistemi
  - Sohbet mesaj yönetimi

## 🚀 Kurulum ve Kullanım

### 1. CSS Dosyasını Dahil Etme
```blade
@push('styles')
<link rel="stylesheet" href="{{ asset('readme/ai-assistant/ai-assistant-styles.css') }}">
@endpush
```

### 2. JavaScript Dosyasını Dahil Etme
```blade
@push('scripts')
<script src="{{ asset('readme/ai-assistant/ai-assistant-scripts.js') }}"></script>
@endpush
```

### 3. Blade Template'i Dahil Etme
```blade
@include('path.to.ai-assistant-panel')
```

## 📋 Özellikler

### 🎨 UI/UX Özellikleri
- **Floating Design**: Sağ alt köşede sabit konum
- **Modern Animasyonlar**: Yumuşak geçişler ve hover efektleri
- **Responsive**: Mobil uyumlu tasarım
- **Dark Mode**: Otomatik karanlık mod desteği

### ⚡ Fonksiyonel Özellikler
- **AI Features**: Dinamik AI özellik butonları
- **Sohbet Arayüzü**: Gerçek zamanlı AI sohbet
- **Analiz Sonuçları**: Görsel analiz raporları
- **Progress Tracking**: İşlem takip sistemi

### 🔧 Teknik Özellikler
- **Livewire Entegrasyonu**: Gerçek zamanlı güncellemeler
- **Event System**: Custom event yönetimi
- **Token Tracking**: AI token kullanım takibi
- **Debug Mode**: Geliştirici debug bilgileri

## 🎯 AI Özellikleri

### Hızlı İşlemler
- SEO Analizi
- İçerik Optimizasyonu
- Anahtar Kelime Analizi
- Çeviri Hizmetleri

### Sohbet Sistemi
- Anlık AI sohbet
- Mesaj geçmişi
- Otomatik scroll

### Sonuç Gösterimi
- Görsel skor kartları
- Detaylı öneriler
- Renk kodlu değerlendirmeler

## 🛠️ Geliştirici Notları

### CSS Yapısı
- **Componentler**: Modüler CSS yapısı
- **Variables**: CSS custom properties kullanımı
- **Animations**: Keyframe animasyonları

### JavaScript Yapısı
- **Event Listeners**: DOM ve Livewire event'ları
- **Helper Functions**: Utility fonksiyonları
- **Error Handling**: Hata yönetimi

### Livewire Entegrasyonu
- **Custom Events**: Özel event sistemi
- **Real-time Updates**: Gerçek zamanlı güncellemeler
- **Session Management**: Oturum yönetimi

## 📝 Changelog

### Version 1.0 (27.07.2025)
- ✅ CSS ve JS kodları ayrıştırıldı
- ✅ Modüler yapı oluşturuldu
- ✅ Dokümantasyon eklendi
- ✅ readme/ai-assistant klasörüne taşındı

## 🔮 Gelecek Planları

- [ ] Component library entegrasyonu
- [ ] TypeScript desteği
- [ ] Advanced analytics
- [ ] Multi-language support

## 📞 Destek

Bu sistem Turkbil Bee projesi için geliştirilmiştir. Herhangi bir sorun için proje deposuna issue açabilirsiniz.