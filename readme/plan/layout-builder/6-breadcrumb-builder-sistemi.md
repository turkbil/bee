# 🎯 BREADCRUMB BUILDER SİSTEMİ

## 📋 Breadcrumb Bileşeni Özeti

Breadcrumb (ekmek kırıntısı), kullanıcının site içindeki konumunu gösteren navigasyon yardımcısıdır. SEO açısından kritik olan bu bileşen, kullanıcı deneyimini artırır ve arama motorlarına site yapısı hakkında bilgi verir.

## 🏗️ Breadcrumb Yapısı ve Bileşenler

### Ana Bileşenler
1. **Home Link** - Ana sayfa bağlantısı
2. **Category/Parent Links** - Üst kategori bağlantıları
3. **Current Page** - Mevcut sayfa (genellikle link değil)
4. **Separators** - Ayırıcı karakterler/ikonlar
5. **Schema Markup** - SEO için yapılandırılmış veri

## 🎨 Breadcrumb Stil Tipleri

### 1. Arrow Style (Ok)
```
Ana Sayfa → Kategoriler → Web Tasarım → Hizmetler
```

### 2. Slash Style (Eğik Çizgi)
```
Ana Sayfa / Kategoriler / Web Tasarım / Hizmetler
```

### 3. Dot Style (Nokta)
```
Ana Sayfa • Kategoriler • Web Tasarım • Hizmetler
```

### 4. Greater Than Style
```
Ana Sayfa > Kategoriler > Web Tasarım > Hizmetler
```

### 5. Icon Style (İkonlar)
```
🏠 Ana Sayfa ➤ 📁 Kategoriler ➤ 💻 Web Tasarım ➤ 🛠️ Hizmetler
```

### 6. Custom Style (Özel)
```
Ana Sayfa ┃ Kategoriler ┃ Web Tasarım ┃ Hizmetler
```

## 🔧 Breadcrumb Konfigürasyon Şeması

```json
{
  "breadcrumb": {
    "show": true,
    "position": "before_title|after_title|above_content|in_subheader",
    "style": "arrows|slashes|dots|greater_than|icons|custom",
    "separator": {
      "arrows": "→",
      "slashes": "/",
      "dots": "•",
      "greater_than": ">",
      "custom": "┃"
    },
    "home": {
      "show": true,
      "text": "Ana Sayfa",
      "url": "/",
      "icon": "home|house|fa-home",
      "icon_only": false
    },
    "current_page": {
      "show": true,
      "clickable": false,
      "style": "normal|bold|different_color"
    },
    "max_items": {
      "desktop": 7,
      "mobile": 3,
      "show_ellipsis": true,
      "ellipsis_text": "..."
    },
    "truncation": {
      "enable": true,
      "max_length": 30,
      "method": "ellipsis|word_break",
      "position": "middle|end"
    },
    "links": {
      "show_icons": false,
      "icon_position": "left|right",
      "target": "_self",
      "nofollow": false
    },
    "styling": {
      "alignment": "left|center|right",
      "background": "transparent|#f8f9fa|custom",
      "padding": {
        "top": "10px",
        "bottom": "10px",
        "left": "0px",
        "right": "0px"
      },
      "margin": {
        "top": "0px",
        "bottom": "15px"
      },
      "border": {
        "show": false,
        "position": "top|bottom|both|around",
        "color": "#dee2e6",
        "width": "1px",
        "style": "solid|dashed|dotted"
      },
      "colors": {
        "text_color": "#6c757d",
        "link_color": "#007bff",
        "link_hover_color": "#0056b3",
        "current_color": "#495057",
        "separator_color": "#6c757d",
        "background_color": "transparent"
      },
      "typography": {
        "font_family": "inherit|Arial|Georgia|custom",
        "font_size": "14px",
        "font_weight": "normal|bold|400|500|600",
        "text_transform": "none|uppercase|lowercase|capitalize"
      },
      "spacing": {
        "item_spacing": "8px",
        "separator_spacing": "8px"
      }
    },
    "responsive": {
      "mobile": {
        "hide": false,
        "collapse": true,
        "show_only_current": false,
        "max_items": 2,
        "font_size": "12px",
        "alignment": "left"
      },
      "tablet": {
        "max_items": 4,
        "font_size": "13px"
      }
    },
    "animation": {
      "enable": false,
      "type": "fadeIn|slideIn|none",
      "duration": "300ms",
      "delay": "0ms"
    },
    "seo": {
      "structured_data": true,
      "schema_type": "BreadcrumbList",
      "add_to_head": true,
      "microdata": false
    },
    "accessibility": {
      "aria_label": "Breadcrumb navigation",
      "skip_links": true,
      "focus_management": true
    },
    "custom_rules": {
      "hide_on_homepage": true,
      "hide_on_pages": [],
      "custom_separators_per_level": false,
      "level_specific_styling": false
    },
    "dynamic_content": {
      "auto_generate": true,
      "use_page_hierarchy": true,
      "use_category_hierarchy": true,
      "fallback_to_menu": true,
      "custom_mappings": {
        "/blog": "Blog",
        "/products": "Ürünler",
        "/services": "Hizmetler"
      }
    }
  }
}
```

## 🎛️ Breadcrumb Builder Interface

### Konfigürasyon Sekmeleri
1. **Style** - Görünüm ve ayırıcı stilleri
2. **Content** - İçerik ve metin ayarları
3. **Layout** - Pozisyon ve hizalama
4. **Responsive** - Mobil davranış
5. **SEO** - Schema markup ve accessibility
6. **Advanced** - İleri düzey özellikler

### Visual Builder Controls
```
┌─ BREADCRUMB BUILDER ─────────────────────────────────────┐
│ ☑ Show Breadcrumb  Position: [Before Title ▼]          │
│                                                          │
│ Style Settings:                                          │
│ Style: [Arrows ▼]  Separator: [→]  ☑ Custom Separator  │
│                                                          │
│ Home Link:                                               │
│ ☑ Show Home  Text: [Ana Sayfa]  Icon: [Home ▼]         │
│ ☐ Icon Only                                             │
│                                                          │
│ Current Page:                                            │
│ ☑ Show Current  ☐ Clickable  Style: [Bold ▼]           │
│                                                          │
│ Limitations:                                             │
│ Max Items: [7] Desktop  [3] Mobile  ☑ Show Ellipsis    │
│ Text Truncation: [30] chars  Method: [Ellipsis ▼]      │
│                                                          │
│ Styling:                                                 │
│ Alignment: [Left ▼]  Background: [Transparent ▼]       │
│ Text Color: [#6c757d]  Link Color: [#007bff]           │
│ Font Size: [14px]  Spacing: [8px]                      │
│                                                          │
│ SEO & Accessibility:                                     │
│ ☑ Schema Markup  ☑ ARIA Labels  ☑ Focus Management     │
│                                                          │
│ [Save Configuration] [Preview] [Reset]                  │
└──────────────────────────────────────────────────────────┘
```

## 📱 Responsive Davranış

### Mobile (< 768px)
- Maksimum 2-3 öğe gösterme
- Ellipsis (...) ile kesme
- Sadece mevcut sayfayı gösterme (opsiyonel)
- Küçük font boyutu (12px)
- Touch-friendly link boyutları

### Tablet (768px - 1024px)  
- Maksimum 4-5 öğe
- Orta boyut font (13px)
- Kompakt spacing

### Desktop (> 1024px)
- Tam öğe görünümü (7+ öğe)
- Normal font boyutu (14px)
- Geniş spacing

## 🔍 SEO ve Schema Markup

### JSON-LD Schema Örneği
```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Ana Sayfa",
      "item": "https://example.com/"
    },
    {
      "@type": "ListItem", 
      "position": 2,
      "name": "Hizmetler",
      "item": "https://example.com/services"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Web Tasarım",
      "item": "https://example.com/services/web-design"
    }
  ]
}
```

### Microdata Markup
```html
<nav aria-label="Breadcrumb navigation">
  <ol itemscope itemtype="https://schema.org/BreadcrumbList">
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="/"><span itemprop="name">Ana Sayfa</span></a>
      <meta itemprop="position" content="1" />
    </li>
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="/services"><span itemprop="name">Hizmetler</span></a>
      <meta itemprop="position" content="2" />
    </li>
  </ol>
</nav>
```

## 🎨 Breadcrumb Templates

### E-commerce Template
```json
{
  "style": "arrows",
  "separator": "→",
  "home": {"text": "Mağaza", "icon": "shopping-bag"},
  "show_category_hierarchy": true,
  "colors": {
    "link_color": "#e74c3c",
    "current_color": "#2c3e50"
  }
}
```

### Blog Template
```json
{
  "style": "slashes",
  "separator": "/",
  "home": {"text": "Blog", "icon": "book"},
  "show_category": true,
  "show_tags": false,
  "colors": {
    "link_color": "#3498db",
    "text_color": "#7f8c8d"
  }
}
```

### Corporate Template
```json
{
  "style": "greater_than",
  "separator": ">",
  "home": {"text": "Ana Sayfa", "icon": "home"},
  "styling": {
    "background": "#f8f9fa",
    "border": true,
    "padding": "15px"
  }
}
```

### Minimalist Template
```json
{
  "style": "dots",
  "separator": "•",
  "home": {"icon_only": true, "icon": "home"},
  "styling": {
    "background": "transparent",
    "font_size": "12px",
    "text_color": "#999999"
  }
}
```

## 🔌 Breadcrumb API ve Hook'lar

### Dynamic Generation
```php
// Otomatik breadcrumb oluşturma
BreadcrumbBuilder::generate($currentPage);

// Manuel breadcrumb ekleme
BreadcrumbBuilder::add('Kategoriler', '/categories');
BreadcrumbBuilder::add('Web Tasarım', '/categories/web-design');
BreadcrumbBuilder::setCurrent('Hizmetler');

// Breadcrumb reset
BreadcrumbBuilder::reset();
```

### Hook'lar ve Filters
```php
// Breadcrumb render öncesi
do_action('breadcrumb_before_render', $items);

// Breadcrumb items filter
$items = apply_filters('breadcrumb_items', $items, $config);

// Schema markup filter
$schema = apply_filters('breadcrumb_schema', $schema, $items);

// Separator filter
$separator = apply_filters('breadcrumb_separator', $separator, $level);
```

### Custom Implementation
```php
// Özel breadcrumb provider
class CustomBreadcrumbProvider implements BreadcrumbProviderInterface
{
    public function generate(Request $request): array
    {
        // Custom logic
        return $breadcrumbItems;
    }
}

// Provider kaydetme
BreadcrumbBuilder::registerProvider('custom', CustomBreadcrumbProvider::class);
```

## 🚀 İleri Düzey Özellikler

### Multi-language Support
```php
// Dil bazında breadcrumb
$breadcrumb = BreadcrumbBuilder::forLocale('tr');

// Breadcrumb çevirisi
__('breadcrumb.home', [], 'tr'); // "Ana Sayfa"
__('breadcrumb.categories', [], 'en'); // "Categories"
```

### Conditional Display
```php
// Sayfa tipine göre breadcrumb
if (is_front_page()) {
    BreadcrumbBuilder::hide();
}

// Kullanıcı rolüne göre
if (user_can('administrator')) {
    BreadcrumbBuilder::addAdminLinks();
}
```

### Performance Optimization
```php
// Cache breadcrumb
Cache::remember("breadcrumb.{$pageId}", 3600, function() {
    return BreadcrumbBuilder::generate();
});

// Lazy loading
BreadcrumbBuilder::lazy(true);
```

## 🔧 Teknik Gereksinimler

### HTML Structure
```html
<nav class="breadcrumb-nav" aria-label="Breadcrumb navigation">
  <ol class="breadcrumb-list">
    <li class="breadcrumb-item">
      <a href="/" class="breadcrumb-link">
        <i class="fa fa-home"></i> Ana Sayfa
      </a>
    </li>
    <li class="breadcrumb-separator">→</li>
    <li class="breadcrumb-item">
      <a href="/services" class="breadcrumb-link">Hizmetler</a>
    </li>
    <li class="breadcrumb-separator">→</li>
    <li class="breadcrumb-item breadcrumb-current">
      <span>Web Tasarım</span>
    </li>
  </ol>
</nav>
```

### CSS Classes
```css
.breadcrumb-nav { /* Navigation container */ }
.breadcrumb-list { /* List container */ }
.breadcrumb-item { /* Individual item */ }
.breadcrumb-link { /* Link styling */ }
.breadcrumb-separator { /* Separator styling */ }
.breadcrumb-current { /* Current page styling */ }
.breadcrumb-ellipsis { /* Truncation indicator */ }
```

### JavaScript Integration
```javascript
// Breadcrumb interactions
class BreadcrumbManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindTruncation();
        this.bindMobileToggle();
    }
    
    bindTruncation() {
        // Long text handling
    }
    
    bindMobileToggle() {
        // Mobile collapse/expand
    }
}
```

---

*Breadcrumb Builder, kullanıcı deneyimi ve SEO açısından kritik bir navigasyon bileşenidir. Özellikle derin site yapılarında kullanıcıların yönünü bulmasına yardımcı olur.*