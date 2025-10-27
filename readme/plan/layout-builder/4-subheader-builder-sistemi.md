# 🎯 SUBHEADER BUILDER SİSTEMİ

## 📋 Subheader Bileşeni Özeti

Subheader, ana header ile content arasında yer alan ikincil başlık alanıdır. Sayfa başlığı, breadcrumb navigasyonu, call-to-action butonları ve sayfa-spesifik bilgileri içerir.

## 🏗️ Subheader Yapısı ve Bileşenler

### Ana Bileşenler
1. **Page Title** - Sayfa başlığı (H1)
2. **Subtitle/Description** - Sayfa açıklaması
3. **Breadcrumb Navigation** - Navigasyon yolu
4. **Call-to-Action Buttons** - Eylem butonları
5. **Search Box** - Sayfa-spesifik arama
6. **Background Elements** - Arkaplan, overlay, animasyonlar

## 🎨 Subheader Layout Tipleri

### 1. Simple Subheader
```
┌─────────────────────────────────────────────────────────────┐
│ Ana Sayfa > Hakkımızda                                      │
│ Hakkımızda                                                  │
└─────────────────────────────────────────────────────────────┘
```

### 2. Hero Subheader
```
┌─────────────────────────────────────────────────────────────┐
│                         HAKKIMIZDA                          │
│                Şirketimiz hakkında bilgi alın               │
│            [İletişime Geç]  [Katalog İndir]                │
│                                                             │
│ Ana Sayfa > Hakkımızda                                      │
└─────────────────────────────────────────────────────────────┘
```

### 3. Banner Subheader
```
┌─────────────────────────────────────────────────────────────┐
│ HAKKIMIZDA                           [Site İçinde Ara...]  │
│ Ana Sayfa > Hakkımızda                                      │
└─────────────────────────────────────────────────────────────┘
```

### 4. Split Subheader
```
┌─────────────────────────────────────────────────────────────┐
│ HAKKIMIZDA                                   [📞 Hemen Ara] │
│ 25 yılın tecrübesi                       [✉️ E-posta Gönder] │
│ Ana Sayfa > Hakkımızda                                      │
└─────────────────────────────────────────────────────────────┘
```

### 5. Video Background Subheader
```
┌─────────────────────────────────────────────────────────────┐
│ [Video Background with Overlay]                             │
│                         HAKKIMIZDA                          │
│                     Profesyonel Hizmet                     │
│                      [Hizmetlerimiz]                       │
│ Ana Sayfa > Hakkımızda                                      │
└─────────────────────────────────────────────────────────────┘
```

## 🔧 Subheader Konfigürasyon Şeması

```json
{
  \"subheader\": {
    \"show\": true,
    \"layout\": \"simple|hero|banner|split|video\",
    \"height\": {
      \"desktop\": \"auto|small|medium|large|custom\",
      \"mobile\": \"auto|small|medium\",
      \"custom_height\": \"200px\"
    },
    \"background\": {
      \"type\": \"color|gradient|image|video|slider\",
      \"color\": \"#f8f9fa\",
      \"gradient\": \"linear-gradient(135deg, #667eea 0%, #764ba2 100%)\",
      \"image\": {
        \"url\": \"/storage/tenant/subheader-bg.jpg\",
        \"position\": \"center center\",
        \"size\": \"cover|contain|auto\",
        \"repeat\": \"no-repeat\",
        \"attachment\": \"scroll|fixed\"
      },
      \"video\": {
        \"url\": \"/storage/tenant/subheader-video.mp4\",
        \"poster\": \"/storage/tenant/video-poster.jpg\",
        \"autoplay\": true,
        \"loop\": true,
        \"muted\": true
      },
      \"overlay\": {
        \"show\": true,
        \"type\": \"color|gradient\",
        \"color\": \"#000000\",
        \"opacity\": 0.5,
        \"gradient\": \"linear-gradient(45deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%)\"
      }
    },
    \"content\": {
      \"title\": {
        \"show\": true,
        \"text\": \"{{page_title}}\",
        \"tag\": \"h1|h2|h3\",
        \"custom_text\": \"\",
        \"font_size\": \"default|small|large|custom\",
        \"animation\": \"none|fadeIn|slideUp|typewriter|bounce\"
      },
      \"subtitle\": {
        \"show\": true,
        \"text\": \"{{page_description}}\",
        \"tag\": \"p|h4|h5|h6\",
        \"custom_text\": \"\",
        \"font_size\": \"default|small|large\",
        \"animation\": \"none|fadeIn|slideUp|delay\"
      },
      \"breadcrumb\": {
        \"show\": true,
        \"style\": \"arrows|slashes|dots|custom\",
        \"separator\": \">\",
        \"position\": \"top|bottom|inline\",
        \"home_text\": \"Ana Sayfa\",
        \"show_home\": true,
        \"show_current\": true,
        \"max_items\": 5,
        \"animation\": \"none|slideIn|fadeIn\"
      },
      \"call_to_action\": {
        \"show\": false,
        \"buttons\": [
          {
            \"text\": \"İletişime Geç\",
            \"url\": \"/contact\",
            \"style\": \"primary|secondary|outline|ghost\",
            \"size\": \"small|medium|large\",
            \"icon\": \"phone|envelope|arrow-right\",
            \"target\": \"_self|_blank\",
            \"animation\": \"none|pulse|bounce|shake\"
          },
          {
            \"text\": \"Katalog İndir\",
            \"url\": \"/catalog.pdf\",
            \"style\": \"secondary\",
            \"size\": \"medium\",
            \"icon\": \"download\",
            \"target\": \"_blank\"
          }
        ],
        \"layout\": \"horizontal|vertical|center\",
        \"spacing\": \"small|medium|large\"
      },
      \"search\": {
        \"show\": false,
        \"placeholder\": \"Bu sayfa içinde ara...\",
        \"style\": \"minimal|expanded|modern\",
        \"button_text\": \"Ara\",
        \"width\": \"small|medium|large|full\",
        \"position\": \"center|left|right\"
      },
      \"custom_html\": {
        \"show\": false,
        \"content\": \"<div>Custom HTML Content</div>\",
        \"position\": \"before_title|after_title|after_buttons\"
      }
    },
    \"styling\": {
      \"alignment\": \"left|center|right\",
      \"text_alignment\": \"left|center|right|justify\",
      \"container\": \"fluid|boxed\",
      \"padding\": {
        \"top\": \"40px\",
        \"bottom\": \"40px\",
        \"left\": \"15px\",
        \"right\": \"15px\"
      },
      \"margin\": {
        \"top\": \"0px\",
        \"bottom\": \"0px\"
      },
      \"colors\": {
        \"text_color\": \"#333333\",
        \"title_color\": \"#2c3e50\",
        \"subtitle_color\": \"#7f8c8d\",
        \"breadcrumb_color\": \"#6c757d\",
        \"breadcrumb_active_color\": \"#007bff\",
        \"link_color\": \"#007bff\",
        \"link_hover_color\": \"#0056b3\"
      },
      \"typography\": {
        \"title_font_family\": \"inherit|Arial|Georgia|custom\",
        \"title_font_weight\": \"normal|bold|300|400|500|600|700\",
        \"title_font_size\": \"2rem\",
        \"subtitle_font_family\": \"inherit|Arial|Georgia|custom\",
        \"subtitle_font_size\": \"1.2rem\"
      },
      \"border\": {
        \"show\": false,
        \"position\": \"top|bottom|both\",
        \"color\": \"#dee2e6\",
        \"width\": \"1px|2px|3px\",
        \"style\": \"solid|dashed|dotted\"
      },
      \"shadow\": {
        \"show\": false,
        \"blur\": \"10px\",
        \"color\": \"rgba(0,0,0,0.1)\",
        \"offset_x\": \"0px\",
        \"offset_y\": \"2px\"
      }
    },
    \"responsive\": {
      \"mobile\": {
        \"height\": \"small\",
        \"alignment\": \"center\",
        \"text_alignment\": \"center\",
        \"hide_elements\": [\"search\", \"subtitle\"],
        \"title_font_size\": \"1.5rem\",
        \"padding\": {
          \"top\": \"20px\",
          \"bottom\": \"20px\"
        },
        \"cta_layout\": \"vertical\"
      },
      \"tablet\": {
        \"height\": \"medium\",
        \"alignment\": \"center\",
        \"hide_elements\": [\"search\"],
        \"title_font_size\": \"1.8rem\"
      }
    },
    \"animation\": {
      \"enable\": true,
      \"entrance\": \"none|fadeIn|slideDown|zoomIn|slideInFromLeft\",
      \"scroll_effect\": \"none|parallax|fixed|fade_on_scroll\",
      \"parallax_speed\": \"slow|medium|fast\",
      \"elements_delay\": \"100ms\",
      \"duration\": \"500ms\"
    },
    \"seo\": {
      \"title_as_h1\": true,
      \"schema_markup\": true,
      \"breadcrumb_schema\": true
    }
  }
}
```

## 🎛️ Subheader Builder Interface

### Konfigürasyon Sekmeleri
1. **Layout** - Temel düzen ve görünüm
2. **Background** - Arkaplan ayarları (renk, resim, video)
3. **Content** - Başlık, açıklama, breadcrumb
4. **Actions** - CTA butonları ve arama
5. **Styling** - Tipografi, renkler, spacing
6. **Animation** - Animasyon ve efektler
7. **Mobile** - Responsive ayarlar

### Visual Builder Controls
```
┌─ SUBHEADER BUILDER ──────────────────────────────────────┐
│ Layout: [Hero ▼]  Height: [Medium ▼]  ☑ Show           │
│                                                          │
│ Background:                                              │
│ Type: [Image ▼]  [📁 Upload Image]  ☑ Overlay          │
│ Overlay Color: [#000000]  Opacity: [50%]               │
│                                                          │
│ Content:                                                 │
│ ☑ Title: [{{page_title}}]  Tag: [H1 ▼]                 │
│ ☑ Subtitle: [{{page_description}}]                      │
│ ☑ Breadcrumb  Style: [Arrows ▼]  Position: [Bottom ▼]  │
│                                                          │
│ Call-to-Action:                                          │
│ ☑ Show CTA  [+ Add Button]                             │
│ Button 1: [İletişime Geç] [/contact] [Primary ▼]       │
│ Button 2: [Katalog] [/catalog.pdf] [Secondary ▼]       │
│                                                          │
│ Styling:                                                 │
│ Alignment: [Center ▼]  Text Color: [#ffffff]           │
│ Title Size: [2rem]  Animation: [FadeIn ▼]              │
│                                                          │
│ [Save Configuration] [Preview] [Reset]                  │
└──────────────────────────────────────────────────────────┘
```

## 📱 Responsive Davranış

### Mobile (< 768px)
- Yükseklik otomatik küçültme
- Ortalanmış metin hizalaması
- CTA butonları dikey dizilim
- Subtitle gizleme (opsiyonel)
- Daha küçük font boyutları

### Tablet (768px - 1024px)
- Orta boyut yükseklik
- Arama kutusu gizleme
- Font boyutu optimizasyonu

### Desktop (> 1024px)
- Tam özellik görünümü
- Parallax efektleri
- Video background desteği

## 🎨 Subheader Templates

### Blog Post Template
```json
{
  \"layout\": \"simple\",
  \"title\": {\"text\": \"{{post_title}}\"},
  \"subtitle\": {\"text\": \"{{post_excerpt}}\"},
  \"breadcrumb\": {\"show\": true},
  \"background\": {\"type\": \"color\", \"color\": \"#f8f9fa\"}
}
```

### Product Page Template
```json
{
  \"layout\": \"split\",
  \"title\": {\"text\": \"{{product_name}}\"},
  \"cta\": {
    \"buttons\": [
      {\"text\": \"Sepete Ekle\", \"style\": \"primary\"},
      {\"text\": \"Favorilere Ekle\", \"style\": \"outline\"}
    ]
  }
}
```

### Contact Page Template
```json
{
  \"layout\": \"hero\",
  \"background\": {\"type\": \"image\", \"overlay\": true},
  \"title\": {\"text\": \"İletişime Geçin\"},
  \"subtitle\": {\"text\": \"Size nasıl yardımcı olabiliriz?\"},
  \"cta\": {
    \"buttons\": [
      {\"text\": \"Hemen Ara\", \"icon\": \"phone\"},
      {\"text\": \"E-posta Gönder\", \"icon\": \"envelope\"}
    ]
  }
}
```

### Landing Page Template
```json
{
  \"layout\": \"video\",
  \"background\": {\"type\": \"video\", \"autoplay\": true},
  \"title\": {\"animation\": \"typewriter\"},
  \"cta\": {
    \"buttons\": [
      {\"text\": \"Ücretsiz Deneyin\", \"style\": \"primary\", \"animation\": \"pulse\"}
    ]
  }
}
```

## 🔌 Subheader API ve Hook'lar

### Template Variables
```php
// Dinamik değişkenler
{{page_title}}        // Sayfa başlığı
{{page_description}}   // Sayfa açıklaması
{{breadcrumb_trail}}   // Breadcrumb yolu
{{current_date}}       // Güncel tarih
{{tenant_name}}        // Tenant adı
{{user_name}}          // Kullanıcı adı (giriş yapmışsa)
```

### Custom Hooks
```php
// Subheader render öncesi
do_action('subheader_before_render', $config, $page);

// Title render filter
apply_filters('subheader_title_text', $title, $config);

// CTA buttons filter
apply_filters('subheader_cta_buttons', $buttons, $config);

// Background filter
apply_filters('subheader_background_html', $html, $config);
```

## 🎯 Kullanım Senaryoları

### E-ticaret
- Ürün kategorisi sayfalarında kategori başlığı
- Ürün detay sayfalarında ürün adı ve fiyat
- Sepet sayfasında adım göstergesi

### Kurumsal
- Hizmet sayfalarında hizmet başlığı ve açıklama
- Hakkımızda sayfasında şirket sloganı
- İletişim sayfasında konum bilgisi

### Blog
- Makale sayfalarında başlık ve yazar bilgisi  
- Kategori sayfalarında kategori açıklaması
- Arşiv sayfalarında tarih filtreleri

### Portfolio
- Proje detaylarında proje adı ve client
- Kategori sayfalarında portfolio türü
- Galeri sayfalarında albüm bilgisi

---

*Subheader Builder, sayfa içeriğine giriş yapan ve kullanıcıyı yönlendiren kritik bir bileşendir. Hero section'lar ve landing page'ler için özellikle güçlüdür.*