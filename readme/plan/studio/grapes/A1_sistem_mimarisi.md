# 🏗️ A1 | Sistem Mimarisi

> **Amaç**: İşe başlamadan önce Studio editör sisteminin teknik mimarisini anlamak  
> **Hedef Kitle**: Geliştiriciler, sistem analistleri, proje yöneticileri

## 🎯 Genel Mimari Görünüm

**Studio Modülü**, GrapesJS tabanlı görsel HTML düzenleyici sistemi olarak çalışıyor. Modül yapısı:

```
Modules/Studio/
├── App/
│   ├── Http/
│   │   ├── Controllers/Admin/   # Admin kontrolleri
│   │   ├── Livewire/           # Livewire bileşenler
│   ├── Services/               # İş mantığı servisleri
│   ├── Support/                # Yardımcı sınıflar
│   ├── Renderers/              # Render işlemleri
│   ├── Parsers/                # HTML/CSS ayrıştırıcı
├── resources/views/            # Blade şablonları
├── routes/                     # Rota tanımları
├── config/                     # Konfigürasyon
```

## 🌐 Rota Yapısı ve Akış

### Ana Rota Tanımı
- **URL Pattern**: `/admin/studio/editor/{module}/{id}/{locale?}`  
- **Hedef**: `EditorComponent` (Livewire)
- **Middleware**: `admin`, `tenant`, `module.permission:studio,view`

### Rota Akışı
1. **Giriş**: `admin.studio.editor` rotası
2. **Yönlendirme**: Livewire EditorComponent
3. **Layout**: `studio::layouts.editor`  
4. **View**: `studio::livewire.editor`

## 🎮 Controller Yapısı

### StudioController (Ana Kontrollör)
**Sorumluluklar:**
- İçerik kaydetme (`save()`)
- Asset yükleme (`uploadAssets()`)
- Blok verileri (`getBlocks()`)
- Kaynak yayınlama (`publishResources()`)

**Service Dependencies:**
- `EditorService`: İçerik işlemleri
- `WidgetService`: Widget yönetimi
- `AssetService`: Dosya işlemleri  
- `BlockService`: Blok yönetimi

### EditorComponent (Livewire)
**Temel Özellikler:**
- Dinamik içerik yükleme
- Çoklu dil desteği
- Widget entegrasyonu
- Live kaydetme

## 🗄️ Veri Katmanı & Widget Entegrasyonu

### Model Yapısı
Studio modülü **kendi modellerine sahip değil**, ancak **WidgetManagement modülü ile derin entegrasyon** var:

**Ana Veri Kaynakları:**
- `Page/Portfolio`: Editör için içerik modülleri
- **`Widget`**: Merkezi widget tanımları (Central DB)
- **`WidgetCategory`**: 3-level hiyerarşik kategoriler  
- **`TenantWidget`**: Tenant'a özgü widget örnekleri
- **`WidgetItem`**: Dinamik widget içerikleri

**Plugin/Widget Sistemi:**
- **Studio'nun plugin sistemi = Widget sistemi**
- 4 widget türü: `static`, `dynamic`, `file`, `module`
- Kategorik widget organizasyonu
- Tenant-bazlı customization

**Veri Akışı:**
1. `BlockService::getAllBlocks()` → Widget'ları Studio block'larına dönüştür
2. `WidgetService` → Widget cache & yönetimi
3. `EditorService::getModuleModel()` → İçerik modüllerini yükle
4. Translatable fields → Çoklu dil desteği

## 🎨 Frontend Teknolojileri

### Temel Teknolojiler
- **GrapesJS**: Ana editör altyapısı
- **Bootstrap 5**: UI framework
- **FontAwesome Pro 6.7.1**: İkonlar
- **Monaco Editor**: Code editör (HTML/CSS)
- **jQuery**: DOM manipülasyon

### CSS Modülleri (15 adet)
1. `grapes.min.css` - GrapesJS core
2. `core.css` - Ana stil
3. `layout.css` - Layout yapısı
4. `panel.css` - Sol/sağ paneller
5. `toolbar.css` - Üst araç çubuğu
6. `forms.css` - Form stilleri
7. `canvas.css` - Editör alanı
8. `components.css` - Component stilleri
9. `layers.css` - Katman paneli
10. `colors.css` - Renk sistemi
11. `devices.css` - Cihaz görünümleri
12. `modal.css` - Modal pencereler
13. `toast.css` - Bildirimler
14. `context-menu.css` - Sağ click menü
15. `responsive.css` - Responsive davranış

### JavaScript Modülleri (20+ adet)
**Core Modüller:**
- `studio-config.js` - Temel konfigürasyon
- `studio-core.js` - Ana fonksiyonalite
- `app.js` - Uygulama başlatıcı

**UI Modülleri:**
- `studio-ui.js`, `studio-ui-tabs.js`, `studio-ui-panels.js`
- `studio-ui-devices.js` - Cihaz değiştirici

**Widget Sistemi:**
- `studio-widget-manager.js` - Widget yöneticisi
- `studio-widget-loader.js` - Widget yükleyici
- `studio-widget-components.js` - Widget bileşenleri

## 🔗 Service Katmanı

### EditorService
**Temel Görevler:**
- İçerik yükleme/kaydetme
- Dil bazında içerik yönetimi
- Modül adaptasyonu

### WidgetService (KRİTİK!)
**Ana Sorumluluklar:**
- Widget'ları Studio block'larına dönüştürme  
- Tenant widget yönetimi & cache
- Widget content rendering
- Widget lifecycle (create, update, delete)

**Widget Processing:**
- 4 widget türü destegi: static, dynamic, file, module
- Tenant-specific customization
- Category-based organization

### BlockService (WIDGET HUB!)
**Core İşlevler:**
- **Widget Discovery**: Tüm aktif widget'ları topla
- **Block Transformation**: Widget → GrapesJS block conversion
- **Category Mapping**: 3-level widget kategorilerini organize et
- **Content Preparation**: Türe göre widget content hazırla
- **Rendering Pipeline**: Widget output generation

**Widget Integration:**
- `getActiveTenantWidgets()`: Aktif tenant widget'ları
- `prepareTenantWidgetContent()`: Tenant-specific rendering
- `getCategoryInfo()`: Hierarchical category mapping

### AssetService
**Kapsamı:**
- Dosya yükleme
- Asset optimizasyon  
- Widget asset management

## 🌍 Çoklu Dil Sistemi

### Dil Yönetimi
- **Tenant Languages** tablosundan aktif diller
- URL parametresi ile dil değişimi  
- **Translatable fields**: `body`, `css`, `js`, `title`
- Varsayılan dil: `tr`

### Dil Değişim Akışı
1. Header'da dil dropdown'u
2. Rota parametresi güncelleme
3. EditorService'te locale belirleme
4. İçeriği yeniden yükleme

## 🎛️ UI/UX Yapısı

### Layout Sistemi  
- **Header**: Araç çubuğu, dil seçici, cihaz değiştirici
- **Sol Panel**: Bileşenler + Katmanlar  
- **Merkez**: GrapesJS editör canvas
- **Sağ Panel**: Konfigürasyon + Tasarım

### Panel Sistem
**Sol Panel Sekmeleri:**
- Components (Bileşenler)
- Layers (Katmanlar)  

**Sağ Panel Sekmeleri:**
- Configure (Konfigürasyon)
- Design (Tasarım/Stil)

### Toolbar Özellikleri
- Cihaz değiştirici (Desktop/Tablet/Mobile)
- Görünüm kontrolleri (sınır göster/gizle)
- Aksiyon butonları (undo/redo/clear)
- Code editörleri (HTML/CSS)
- Kaydetme/Preview

Bu mimari analiz, Studio modülünün **modüler**, **esnek** ve **ölçeklenebilir** bir yapıya sahip olduğunu gösteriyor.