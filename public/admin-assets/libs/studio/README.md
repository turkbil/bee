# Studio Editor Enhanced System

## 🚀 Genel Bakış

Studio Editor Enhanced System, orijinal Studio Editor'a güvenlik, performans ve memory management iyileştirmeleri getiren gelişmiş bir sistemdir.

## 🔧 Yeni Özellikler

### 1. 🛡️ Güvenlik Sistemi (StudioSecurity)
- **XSS Koruması**: HTML sanitization ile güvenli içerik renderı
- **Code Injection Önleme**: JavaScript kodu güvenlik validation
- **CSP Validation**: Content Security Policy kontrolü
- **Güvenlik Logging**: Tüm güvenlik olaylarını backend'e gönderme

### 2. 🧠 Memory Management (StudioMemoryManager)
- **Event Listener Cleanup**: AbortController ile otomatik temizleme
- **MutationObserver Yönetimi**: Observer disconnect'leri
- **Timer Management**: Interval/timeout otomatik temizleme
- **Widget Cleanup**: Widget silindiğinde memory temizleme

### 3. ⚡ Performance Optimizasyonu (StudioPerformance)
- **DOM Query Caching**: Optimized DOM sorguları
- **Debouncing/Throttling**: Performans iyileştirmeleri
- **Virtual Scrolling**: Büyük listelerde performans
- **Lazy Loading**: Resim ve içerik lazy loading
- **Batch DOM Operations**: Toplu DOM işlemleri

### 4. 🚨 Error Handling (StudioErrorHandler)
- **Merkezi Error Logging**: Tüm hataları tek merkezden yönetme
- **Error Classification**: Hata tipi ve seviye sınıflandırması
- **User-Friendly Messages**: Kullanıcı dostu hata mesajları
- **Automatic Retry**: Widget yükleme hatalarında otomatik retry

### 5. 🔧 Widget Utilities (StudioWidgetUtilities)
- **Modular Widget Loading**: Büyük fonksiyonları parçalara bölme
- **Widget State Management**: Widget durumlarını yönetme
- **Template Processing**: Handlebars template işleme
- **Security Integration**: Güvenlik sistemleri entegrasyonu

## 📁 Dosya Yapısı

```
/admin-assets/libs/studio/
├── studio-enhanced.js           # Ana yükleyici
├── partials/
│   ├── studio-security.js       # Güvenlik sistemi
│   ├── studio-memory-manager.js # Memory management
│   ├── studio-performance.js    # Performance optimizasyonu
│   ├── studio-error-handler.js  # Error handling
│   └── studio-widget-utilities.js # Widget utilities
├── app.js                       # Orijinal uygulama
├── grapes.min.js               # GrapesJS
└── README.md                   # Bu dosya
```

## 🔌 Kurulum ve Kullanım

### 1. Enhanced System'i Yükle

```html
<!-- Enhanced system'i yükle -->
<script src="/admin-assets/libs/studio/studio-enhanced.js"></script>

<!-- Sonra normal Studio Editor'ı yükle -->
<script src="/admin-assets/libs/studio/app.js"></script>
```

### 2. System Health Check

```javascript
// Sistem sağlık kontrolü
const health = window.StudioEnhanced.healthCheck();
console.log('System Health:', health);
```

### 3. Manual Cleanup

```javascript
// Manuel temizlik
window.StudioEnhanced.cleanup();
```

## 🔍 API Referansı

### StudioSecurity

```javascript
// HTML sanitization
const cleanHtml = window.StudioSecurity.sanitizeHtml(dirtyHtml);

// JavaScript validation
const isValid = window.StudioSecurity.validateJavaScript(jsCode);

// Güvenli innerHTML
window.StudioSecurity.safeInnerHTML(element, html);
```

### StudioMemoryManager

```javascript
// Event listener ekleme
window.StudioMemoryManager.addEventListener(element, 'click', handler, 'namespace');

// Observer oluşturma
const observer = window.StudioMemoryManager.createObserver(callback, target, options, 'namespace');

// Timer oluşturma
const timerId = window.StudioMemoryManager.setInterval(callback, delay, 'namespace');

// Widget cleanup
window.StudioMemoryManager.cleanupWidget(widgetId);
```

### StudioPerformance

```javascript
// Optimized DOM query
const elements = window.StudioPerformance.query('.selector');

// Debounce function
const debouncedFn = window.StudioPerformance.debounce(func, 300);

// Virtual scrolling
const virtualList = window.StudioPerformance.virtualScroll(container, items, renderItem);

// Lazy loading
window.StudioPerformance.lazyLoad(element, loader);
```

### StudioErrorHandler

```javascript
// Error logging
window.StudioErrorHandler.logError(error, context, additionalData);

// Widget error handling
window.StudioErrorHandler.handleWidgetError(widgetId, error, operation);

// Function wrapping
const safeFn = window.StudioErrorHandler.wrap(unsafeFunction, 'context');
```

### StudioWidgetUtilities

```javascript
// Widget container bulma
const container = window.StudioWidgetUtilities.findWidgetContainer(widgetId);

// Widget content render
window.StudioWidgetUtilities.renderWidgetContent(widgetId, html);

// Widget CSS inject
window.StudioWidgetUtilities.injectWidgetCSS(widgetId, css);

// Widget cleanup
window.StudioWidgetUtilities.cleanupWidget(widgetId);
```

## 📊 Performance Metrikleri

Enhanced system aşağıdaki metrikleri takip eder:

- **DOM Query Count**: DOM sorgu sayısı
- **Render Time**: Render süreleri
- **Memory Usage**: Memory kullanımı
- **Error Count**: Hata sayısı
- **Cache Hit Rate**: Cache hit oranı

## 🐛 Hata Ayıklama

### Console Komutları

```javascript
// Performance metrikleri
console.log(window.StudioPerformance.getMetrics());

// Error statistics
console.log(window.StudioErrorHandler.getErrorStats());

// Memory usage
window.StudioMemoryManager.monitorMemoryUsage();

// System health
console.log(window.StudioEnhanced.healthCheck());
```

### Log Seviyeleri

- **INFO**: Bilgilendirme mesajları
- **WARNING**: Uyarı mesajları
- **ERROR**: Hata mesajları
- **CRITICAL**: Kritik hatalar

## 🔄 Versiyon Geçmişi

### v1.0.0 (2025-01-17)
- ✅ XSS koruması implementasyonu
- ✅ Memory leak düzeltmeleri
- ✅ Performance optimizasyonları
- ✅ Merkezi error handling
- ✅ Code refactoring ve modülarizasyon

## 🤝 Katkıda Bulunma

1. Güvenlik açıklarını bildirirken lütfen detaylı bilgi verin
2. Performance sorunları için profiling sonuçları ekleyin
3. Yeni özellik önerileri için use case'ler belirtin

## 📄 Lisans

Bu proje MIT lisansı altındadır.

---

**Not**: Bu enhanced system mevcut Studio Editor'ı etkilemez, sadece üzerine eklemeler yapar. Orijinal fonksiyonalite korunur.