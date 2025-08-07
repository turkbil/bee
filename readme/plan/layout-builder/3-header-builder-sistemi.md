# 🎯 HEADER BUILDER SİSTEMİ

## 📋 Header Bileşeni Özeti

Header, web sitesinin en üst kısmında yer alan ve tüm sayfalarda görünen kritik layout bileşenidir. Logo, ana navigasyon, iletişim bilgileri ve kullanıcı etkileşim alanlarını içerir.

## 🏗️ Header Yapısı ve Bileşenler

### Ana Bileşenler
1. **Logo Area** - Marka kimliği ve ana sayfa linki
2. **Navigation Menu** - Ana menü sistemi
3. **Contact Info** - Telefon, e-posta, adres bilgileri
4. **Social Media** - Sosyal medya ikonları
5. **Search Box** - Site içi arama
6. **Language Switcher** - Dil değiştirici
7. **User Area** - Giriş/kayıt, kullanıcı profili

## 🎨 Header Layout Tipleri

### 1. Horizontal Header (Yatay)
```
┌─────────────────────────────────────────────────────────────┐
│ [Logo]    [Ana Menü]           [Arama] [Dil] [Sosyal]      │
└─────────────────────────────────────────────────────────────┘
```

### 2. Split Header (Bölünmüş)
```
┌─────────────────────────────────────────────────────────────┐
│           [Logo]                    [İletişim Bilgileri]   │
│ [Ana Menü]              [Arama] [Dil] [Sosyal] [Kullanıcı] │
└─────────────────────────────────────────────────────────────┘
```

### 3. Centered Header (Ortalanmış)
```
┌─────────────────────────────────────────────────────────────┐
│                          [Logo]                            │
│        [Menü]  [Arama]  [Dil]  [Sosyal]  [İletişim]       │
└─────────────────────────────────────────────────────────────┘
```

### 4. Vertical Header (Dikey - Sidebar)
```
┌─────┬───────────────────────────────────┐
│Logo │                                   │
│─────│            CONTENT                │
│Menü │             AREA                  │
│Items│                                   │
│─────│                                   │
│Info │                                   │
└─────┴───────────────────────────────────┘
```

## 🔧 Header Konfigürasyon Şeması

```json
{
  "header": {
    "layout": {
      "type": "horizontal|split|centered|vertical",
      "sticky": true,
      "height": "auto|60px|80px|100px|custom",
      "container": "fluid|boxed"
    },
    "logo": {
      "show": true,
      "position": "left|center|right",
      "size": {
        "width": "auto|150px|200px|custom",
        "height": "auto|40px|60px|custom"
      },
      "image": "/storage/tenant/logo.png",
      "alt_text": "Company Logo",
      "link": "/",
      "mobile": {
        "size": "small|medium|large",
        "position": "left|center"
      }
    },
    "navigation": {
      "show": true,
      "style": "minimal|modern|classic|dropdown",
      "position": "left|center|right|below_logo",
      "alignment": "left|center|right|justify",
      "max_levels": 3,
      "show_icons": false,
      "mobile": {
        "type": "hamburger|slide|accordion|bottom_nav",
        "position": "left|right|center",
        "overlay": true
      },
      "mega_menu": {
        "enable": false,
        "columns": 4,
        "show_images": true
      }
    },
    "contact_info": {
      "show": true,
      "position": "top_bar|header|both",
      "layout": "horizontal|vertical|icons_only",
      "phone": {
        "numbers": ["0555 123 45 67", "0555 987 65 43"],
        "show": true,
        "icon": "phone|mobile|whatsapp",
        "clickable": true
      },
      "email": {
        "address": "info@example.com",
        "show": true,
        "icon": "envelope|mail",
        "clickable": true
      },
      "address": {
        "text": "Example Address, City",
        "show": false,
        "icon": "map-marker|location",
        "clickable": false
      },
      "working_hours": {
        "text": "Pzt-Cum: 09:00-18:00",
        "show": false,
        "icon": "clock|time"
      }
    },
    "social_media": {
      "show": true,
      "position": "header|top_bar",
      "style": "icons|text|combined",
      "size": "small|medium|large",
      "target": "_blank|_self",
      "platforms": {
        "facebook": {
          "url": "https://facebook.com/company",
          "show": true,
          "icon": "facebook|fab fa-facebook"
        },
        "twitter": {
          "url": "https://twitter.com/company", 
          "show": true,
          "icon": "twitter|fab fa-twitter"
        },
        "instagram": {
          "url": "https://instagram.com/company",
          "show": true,
          "icon": "instagram|fab fa-instagram"
        },
        "linkedin": {
          "url": "https://linkedin.com/company",
          "show": false,
          "icon": "linkedin|fab fa-linkedin"
        },
        "youtube": {
          "url": "https://youtube.com/company",
          "show": false,
          "icon": "youtube|fab fa-youtube"
        }
      }
    },
    "search": {
      "show": true,
      "style": "minimal|expanded|modal|dropdown",
      "position": "header|top_bar",
      "placeholder": "Site içinde ara...",
      "button_text": "Ara",
      "autocomplete": true,
      "categories": true
    },
    "language_switcher": {
      "show": true,
      "style": "flags|text|dropdown|combined",
      "position": "header|top_bar",
      "show_current_only": false,
      "show_flag": true,
      "show_text": true
    },
    "user_area": {
      "show": true,
      "position": "header|top_bar",
      "login_text": "Giriş Yap",
      "register_text": "Kayıt Ol",
      "profile_dropdown": true,
      "show_avatar": true
    },
    "cta_button": {
      "show": false,
      "text": "İletişime Geç",
      "url": "/contact",
      "style": "primary|secondary|outline",
      "position": "header|top_bar"
    },
    "top_bar": {
      "show": false,
      "background": "#f8f9fa",
      "text_color": "#6c757d",
      "height": "35px",
      "content": "announcement|contact|social|custom",
      "announcement": {
        "text": "🎉 Yeni ürünlerimizi keşfedin!",
        "link": "/products",
        "closable": true
      }
    },
    "styling": {
      "background": {
        "type": "color|gradient|image",
        "value": "#ffffff",
        "opacity": 1,
        "gradient": "linear-gradient(90deg, #667eea 0%, #764ba2 100%)"
      },
      "text_color": "#333333",
      "link_color": "#007bff",
      "link_hover_color": "#0056b3",
      "border": {
        "show": false,
        "position": "bottom|top|both",
        "color": "#dee2e6",
        "width": "1px"
      },
      "shadow": {
        "show": true,
        "blur": "4px",
        "color": "rgba(0,0,0,0.1)"
      },
      "padding": {
        "top": "15px",
        "bottom": "15px",
        "left": "auto",
        "right": "auto"
      }
    },
    "responsive": {
      "mobile": {
        "hide_elements": ["search", "social", "contact"],
        "hamburger_style": "lines|dots|cross",
        "logo_size": "small"
      },
      "tablet": {
        "navigation_style": "compact",
        "hide_elements": ["working_hours"]
      }
    },
    "animation": {
      "scroll_effect": "none|fade|slide|sticky_reveal",
      "menu_animation": "none|fade|slide|bounce",
      "logo_animation": "none|pulse|rotate|bounce"
    }
  }
}
```

## 🎛️ Header Builder Interface

### Konfigürasyon Sekmeleri
1. **Layout** - Genel düzen ve pozisyon ayarları
2. **Logo** - Logo yükleme ve boyutlandırma
3. **Navigation** - Menü sistemi ve stiller
4. **Contact** - İletişim bilgileri
5. **Social** - Sosyal medya entegrasyonu
6. **Search** - Arama kutusu ayarları
7. **Styling** - Renkler, tipografi, efektler
8. **Mobile** - Responsive ayarlar

### Visual Builder Controls
```
┌─ HEADER BUILDER ─────────────────────────────────────────┐
│ Layout: [Horizontal ▼]  Height: [Auto ▼]  ☑ Sticky     │
│                                                          │
│ Logo:                                                    │
│ [📁 Upload Logo]  Position: [Left ▼]  Size: [Medium ▼]  │
│                                                          │
│ Navigation:                                              │
│ Style: [Modern ▼]  ☑ Show Icons  Max Levels: [3]        │
│ Mobile: [Hamburger ▼]                                    │
│                                                          │
│ Contact Info:                                            │
│ ☑ Phone: [0555 123 45 67] [+ Add Phone]                 │
│ ☑ Email: [info@example.com]                             │
│ ☐ Address: [Company Address]                             │
│                                                          │
│ Social Media: ☑ Show                                     │
│ [☑ Facebook] [☑ Twitter] [☑ Instagram] [☐ LinkedIn]     │
│                                                          │
│ Search: ☑ Show  Style: [Minimal ▼]                      │
│ Language: ☑ Show  Style: [Flags ▼]                      │
│                                                          │
│ [Save Configuration] [Preview] [Reset to Default]       │
└──────────────────────────────────────────────────────────┘
```

### 🚨 Header Builder iFrame Preview Sistemi

**Admin Panel'den Frontend Header Önizlemesi:**
```javascript
// HeaderBuilderPreview.js
class HeaderBuilderPreview {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.config = {};
    this.initializePreview();
  }

  initializePreview() {
    // iFrame oluşturma
    this.previewFrame = document.createElement('iframe');
    this.previewFrame.className = 'header-preview-frame';
    this.previewFrame.style.width = '100%';
    this.previewFrame.style.height = '150px'; // Header yüksekliği
    this.previewFrame.style.border = '1px solid #dee2e6';
    this.previewFrame.style.borderRadius = '4px';
    
    // Tailwind + Alpine.js template
    this.previewFrame.srcdoc = `<!DOCTYPE html>
      <html>
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      </head>
      <body class="m-0 p-0">
        <div id="header-preview-root"></div>
        <script>
          window.addEventListener('message', (event) => {
            if (event.data.type === 'updateHeader') {
              document.getElementById('header-preview-root').innerHTML = event.data.content;
              if (window.Alpine) {
                Alpine.initTree(document.getElementById('header-preview-root'));
              }
            }
          });
        </script>
      </body>
      </html>`;
    
    this.container.appendChild(this.previewFrame);
  }

  // Header config değiştiğinde preview güncelleme
  updatePreview(headerConfig) {
    const headerHTML = this.generateHeaderHTML(headerConfig);
    this.previewFrame.contentWindow.postMessage({
      type: 'updateHeader',
      content: headerHTML
    }, '*');
  }

  // Header HTML oluşturma (Tailwind CSS ile)
  generateHeaderHTML(config) {
    const stickyClass = config.sticky ? 'sticky top-0 z-50' : '';
    const bgColor = config.styling?.background?.value || 'bg-white';
    
    return `
      <header class="${stickyClass} ${bgColor} shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4">
          <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
              ${this.renderLogo(config.logo)}
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
              ${this.renderNavigation(config.navigation)}
            </nav>
            
            <!-- Right Side Items -->
            <div class="hidden md:flex items-center space-x-4">
              ${config.search?.show ? this.renderSearch(config.search) : ''}
              ${config.language_switcher?.show ? this.renderLanguage(config.language_switcher) : ''}
              ${config.user_area?.show ? this.renderUserArea(config.user_area) : ''}
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden">
              <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2">
                <i class="fas fa-bars text-gray-600"></i>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden">
          <div class="px-2 pt-2 pb-3 space-y-1">
            ${this.renderMobileNavigation(config.navigation)}
          </div>
        </div>
      </header>
    `;
  }
}
```

**Preview Container Styling:**
```css
/* Header preview container */
.header-preview-wrapper {
  position: relative;
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.header-preview-frame {
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Device mode buttons */
.preview-device-switcher {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  gap: 5px;
}

.device-btn {
  padding: 5px 10px;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  cursor: pointer;
}

.device-btn.active {
  background: #007bff;
  color: white;
  border-color: #007bff;
}
```

## 📱 Responsive Davranış

### Mobile (< 768px)
- Hamburger menü
- Logo küçültme
- Arama ve sosyal medya gizleme (opsiyonel)
- Touch-friendly buton boyutları

### Tablet (768px - 1024px)
- Kompakt navigasyon
- Orta boyut logo
- Bazı ikincil bilgileri gizleme

### Desktop (> 1024px)
- Tam özellik görünümü
- Büyük logo ve menü
- Tüm bilgi alanları aktif

## 🔌 Header API ve Hooks

### Render Hook'ları
```php
// Header render öncesi
do_action('header_before_render', $config);

// Logo render öncesi/sonrası
apply_filters('header_logo_html', $html, $config);

// Menü render öncesi/sonrası
apply_filters('header_menu_html', $html, $config);

// Header render sonrası
do_action('header_after_render', $config);
```

### Custom CSS/JS Integration
```php
// Dinamik CSS enjeksiyonu
HeaderBuilder::addCustomCSS($tenantId, $customCSS);

// JavaScript callbacks
HeaderBuilder::addScript('mobile-menu-toggle', $jsCode);
```

## 🎯 Header Templates

### Business Template
- Sol logo, sağ iletişim bilgileri
- Orta kısımda ana menü
- Üst bar'da sosyal medya

### E-commerce Template  
- Logo + arama + kullanıcı alanı
- Kategori menüsü
- Sepet ve wishlist ikonları

### Portfolio Template
- Ortalanmış logo
- Minimal menü
- Sosyal medya ikonları

### Corporate Template
- Üst bar announcement
- Logo + menü + CTA button
- İletişim bilgileri

---

*Header Builder, Layout Builder sisteminin temel bileşenidir ve sitenin ilk izlenimini belirleyen kritik bir alandır.*