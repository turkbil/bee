# 🎯 FOOTER BUILDER SİSTEMİ

## 📋 Footer Bileşeni Özeti

Footer, web sitesinin alt kısmında yer alan ve genellikle tüm sayfalarda görünen kapsamlı bilgi alanıdır. Link grupları, iletişim bilgileri, sosyal medya, newsletter ve telif hakkı bilgilerini içerir.

## 🏗️ Footer Yapısı ve Bileşenler

### Ana Bileşenler
1. **Widget Columns** - Çok sütunlu widget alanları
2. **Link Groups** - Kategorize edilmiş link listeleri  
3. **Contact Info** - Detaylı iletişim bilgileri
4. **Social Media** - Sosyal medya bağlantıları
5. **Newsletter** - E-posta abonelik formu
6. **Bottom Bar** - Telif hakkı ve yasal linkler
7. **Back to Top** - Sayfa başına dönüş butonu

## 🎨 Footer Layout Tipleri

### 1. Single Column Footer
```
┌─────────────────────────────────────────────────────────────┐
│                        Company Logo                         │
│                   Şirket açıklama metni                     │
│              [Facebook] [Twitter] [Instagram]               │
│                                                             │
│        © 2025 Company Name. Tüm hakları saklıdır.          │
└─────────────────────────────────────────────────────────────┘
```

### 2. Multi Column Footer
```
┌─────────────────────────────────────────────────────────────┐
│ Hakkımızda      │ Hızlı Linkler   │ İletişim      │ Bülten  │
│ • Şirket        │ • Ana Sayfa     │ Adres:        │ E-posta │
│ • Ekip          │ • Hizmetler     │ ABC Sok. 123  │ [____]  │
│ • Kariyer       │ • Blog          │ Tel: 555-123  │ [Abone] │
│ • Basın         │ • İletişim      │ info@site.com │         │
├─────────────────┴─────────────────┴───────────────┴─────────┤
│ [FB] [TW] [IG]    © 2025 Company - Gizlilik | Şartlar      │
└─────────────────────────────────────────────────────────────┘
```

### 3. Mega Footer
```
┌─────────────────────────────────────────────────────────────┐
│ [Logo]                                    [Sosyal Medya]   │
│                                                             │
│ Hizmetler       │ Kurumsal      │ Destek       │ Bülten    │
│ • Web Tasarım   │ • Hakkımızda  │ • SSS        │ E-bülten  │
│ • E-ticaret     │ • Ekibimiz    │ • Yardım     │ aboneliği │
│ • Pazarlama     │ • Kariyer     │ • İletişim   │ [_______] │
│ • Danışmanlık   │ • Basın       │ • Ticket     │ [ABONE]   │
│                                                             │
│ Sektörler       │ Çözümler      │ Kaynaklar    │ İletişim  │
│ • E-ticaret     │ • CRM         │ • Blog       │ ABC Sokak │
│ • Finans        │ • ERP         │ • Whitepaper │ No: 123   │
│ • Sağlık        │ • Analytics   │ • Case Study │ 34000 İST │
│ • Eğitim        │ • Security    │ • Webinar    │ 555-0123  │
├─────────────────┴───────────────┴──────────────┴───────────┤
│ © 2025 Company Name | Gizlilik Politikası | Kullanım Şartları │
└─────────────────────────────────────────────────────────────┘
```

### 4. Minimalist Footer
```
┌─────────────────────────────────────────────────────────────┐
│     Ana Sayfa  •  Hakkımızda  •  Hizmetler  •  İletişim    │
│                [Facebook] [Twitter] [LinkedIn]              │
│             © 2025 Company Name. All rights reserved.      │
└─────────────────────────────────────────────────────────────┘
```

## 🔧 Footer Konfigürasyon Şeması

```json
{
  "footer": {
    "show": true,
    "layout": "single|multi_column|mega|minimalist",
    "columns": 4,
    "container": "fluid|boxed",
    "widgets": [
      {
        "id": "about",
        "type": "about_company",
        "column": 1,
        "order": 1,
        "title": "Hakkımızda",
        "content": {
          "logo": {
            "show": true,
            "image": "/storage/tenant/footer-logo.png",
            "size": "small|medium|large",
            "link": "/"
          },
          "description": "Şirket açıklama metni...",
          "max_length": 200,
          "show_read_more": false
        }
      },
      {
        "id": "quick_links",
        "type": "link_group",
        "column": 2,
        "order": 1,
        "title": "Hızlı Linkler",
        "content": {
          "links": [
            {
              "text": "Ana Sayfa",
              "url": "/",
              "target": "_self",
              "icon": "home"
            },
            {
              "text": "Hizmetler",
              "url": "/services",
              "target": "_self",
              "icon": "cog"
            },
            {
              "text": "Blog",
              "url": "/blog",
              "target": "_self",
              "icon": "book"
            }
          ],
          "show_icons": false,
          "icon_position": "left|right"
        }
      },
      {
        "id": "contact_info",
        "type": "contact",
        "column": 3,
        "order": 1,
        "title": "İletişim",
        "content": {
          "address": {
            "show": true,
            "text": "ABC Sokak No: 123\\nBeyoğlu, İstanbul",
            "icon": "map-marker",
            "google_maps_link": "https://maps.google.com/..."
          },
          "phone": {
            "show": true,
            "numbers": ["0555 123 45 67", "0212 555 12 34"],
            "icon": "phone",
            "clickable": true,
            "whatsapp": "0555 123 45 67"
          },
          "email": {
            "show": true,
            "addresses": ["info@company.com", "destek@company.com"],
            "icon": "envelope",
            "clickable": true
          },
          "working_hours": {
            "show": true,
            "text": "Pazartesi - Cuma: 09:00 - 18:00\\nCumartesi: 10:00 - 16:00",
            "icon": "clock"
          }
        }
      },
      {
        "id": "newsletter",
        "type": "newsletter",
        "column": 4,
        "order": 1,
        "title": "E-Bülten",
        "content": {
          "description": "Yeni ürün ve kampanyalardan haberdar olun!",
          "form": {
            "email_placeholder": "E-posta adresiniz",
            "button_text": "Abone Ol",
            "button_style": "primary|secondary|outline",
            "success_message": "Başarıyla abone oldunuz!",
            "error_message": "Bir hata oluştu, lütfen tekrar deneyin."
          },
          "privacy_text": "Gizlilik politikamızı kabul ediyorum.",
          "privacy_link": "/privacy"
        }
      }
    ],
    "social_media": {
      "show": true,
      "position": "top|bottom|widget",
      "widget_column": 1,
      "style": "icons|text|combined",
      "size": "small|medium|large",
      "alignment": "left|center|right",
      "target": "_blank",
      "platforms": {
        "facebook": {
          "url": "https://facebook.com/company",
          "show": true,
          "icon": "fab fa-facebook",
          "color": "#1877f2"
        },
        "twitter": {
          "url": "https://twitter.com/company",
          "show": true,
          "icon": "fab fa-twitter",
          "color": "#1da1f2"
        },
        "instagram": {
          "url": "https://instagram.com/company",
          "show": true,
          "icon": "fab fa-instagram",
          "color": "#e4405f"
        },
        "linkedin": {
          "url": "https://linkedin.com/company",
          "show": true,
          "icon": "fab fa-linkedin",
          "color": "#0077b5"
        },
        "youtube": {
          "url": "https://youtube.com/company",
          "show": false,
          "icon": "fab fa-youtube",
          "color": "#ff0000"
        },
        "tiktok": {
          "url": "https://tiktok.com/@company",
          "show": false,
          "icon": "fab fa-tiktok",
          "color": "#000000"
        }
      }
    },
    "bottom_bar": {
      "show": true,
      "background": "#2c3e50",
      "text_color": "#ffffff",
      "padding": "15px 0",
      "border_top": {
        "show": true,
        "color": "#34495e",
        "width": "1px"
      },
      "copyright": {
        "show": true,
        "text": "© {{current_year}} {{company_name}}. Tüm hakları saklıdır.",
        "position": "left|center|right"
      },
      "legal_links": {
        "show": true,
        "position": "left|center|right",
        "separator": "|",
        "links": [
          {
            "text": "Gizlilik Politikası",
            "url": "/privacy"
          },
          {
            "text": "Kullanım Şartları",
            "url": "/terms"
          },
          {
            "text": "Çerez Politikası",
            "url": "/cookies"
          }
        ]
      },
      "payment_methods": {
        "show": false,
        "title": "Kabul Edilen Kartlar:",
        "methods": ["visa", "mastercard", "amex", "paypal"]
      }
    },
    "back_to_top": {
      "show": true,
      "style": "button|text|icon",
      "position": "right|left|center",
      "text": "↑ Yukarı",
      "icon": "arrow-up",
      "animation": "fade|slide|bounce",
      "show_threshold": 300
    },
    "styling": {
      "background": "#2c3e50",
      "text_color": "#ffffff",
      "heading_color": "#ffffff",
      "link_color": "#3498db",
      "link_hover_color": "#2980b9",
      "border_color": "#34495e",
      "padding": {
        "top": "60px",
        "bottom": "30px",
        "left": "0px",
        "right": "0px"
      },
      "typography": {
        "font_family": "inherit|Arial|Georgia|custom",
        "font_size": "14px",
        "line_height": "1.6",
        "heading_font_weight": "600"
      },
      "borders": {
        "widget_separator": {
          "show": false,
          "color": "#34495e",
          "width": "1px",
          "position": "right"
        }
      }
    },
    "responsive": {
      "mobile": {
        "columns": 1,
        "stack_widgets": true,
        "hide_widgets": ["newsletter"],
        "social_position": "bottom",
        "padding": {
          "top": "40px",
          "bottom": "20px"
        }
      },
      "tablet": {
        "columns": 2,
        "hide_widgets": [],
        "social_position": "bottom"
      }
    },
    "seo": {
      "nofollow_external_links": true,
      "structured_data": {
        "organization": true,
        "contact_point": true,
        "social_media": true
      }
    }
  }
}
```

## 🎛️ Footer Builder Interface

### Konfigürasyon Sekmeleri
1. **Layout** - Genel düzen ve sütun sayısı
2. **Widgets** - Widget yönetimi ve içerik
3. **Social Media** - Sosyal medya entegrasyonu
4. **Bottom Bar** - Alt bar ve telif hakkı
5. **Styling** - Renkler, tipografi, spacing
6. **Mobile** - Responsive ayarlar

### Widget Builder Controls
```
┌─ FOOTER BUILDER ─────────────────────────────────────────┐
│ Layout: [Multi Column ▼]  Columns: [4]  ☑ Show        │
│                                                          │
│ Widget Management:                                       │
│ ┌─ Column 1 ────────┬─ Column 2 ────────┬─ Column 3 ──┐ │
│ │ [Hakkımızda]      │ [Hızlı Linkler]  │ [İletişim]  │ │
│ │ ☑ Logo            │ • Ana Sayfa       │ ☑ Adres     │ │
│ │ ☑ Açıklama        │ • Hizmetler       │ ☑ Telefon   │ │
│ │ [Düzenle]         │ • Blog            │ ☑ E-posta   │ │
│ │                   │ [+ Link Ekle]     │ [Düzenle]   │ │
│ └───────────────────┴───────────────────┴─────────────────┘ │
│                                                          │
│ Column 4: [Newsletter ▼]                                 │
│ ☑ E-posta formu  ☑ Gizlilik onayı                      │
│                                                          │
│ Social Media: ☑ Show  Position: [Bottom ▼]             │
│ [☑ Facebook] [☑ Twitter] [☑ Instagram] [☐ LinkedIn]     │
│                                                          │
│ Bottom Bar:                                              │
│ ☑ Telif Hakkı  ☑ Yasal Linkler  ☑ Geri Dön Butonu     │
│                                                          │
│ [Save Configuration] [Preview] [Add Widget]             │
└──────────────────────────────────────────────────────────┘
```

### 🚨 Footer Builder iFrame Preview Sistemi

**Admin Panel'den Frontend Footer Önizlemesi:**
```javascript
// FooterBuilderPreview.js
class FooterBuilderPreview {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.config = {};
    this.initializePreview();
  }

  initializePreview() {
    // iFrame oluşturma
    this.previewFrame = document.createElement('iframe');
    this.previewFrame.className = 'footer-preview-frame';
    this.previewFrame.style.width = '100%';
    this.previewFrame.style.minHeight = '400px';
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
        <style>
          body { margin: 0; padding: 0; min-height: 100vh; display: flex; flex-direction: column; }
          #footer-preview-root { margin-top: auto; }
        </style>
      </head>
      <body>
        <div id="footer-preview-root"></div>
        <script>
          window.addEventListener('message', (event) => {
            if (event.data.type === 'updateFooter') {
              document.getElementById('footer-preview-root').innerHTML = event.data.content;
              if (window.Alpine) {
                Alpine.initTree(document.getElementById('footer-preview-root'));
              }
            }
          });
        </script>
      </body>
      </html>`;
    
    this.container.appendChild(this.previewFrame);
  }

  // Footer config değiştiğinde preview güncelleme
  updatePreview(footerConfig) {
    const footerHTML = this.generateFooterHTML(footerConfig);
    this.previewFrame.contentWindow.postMessage({
      type: 'updateFooter',
      content: footerHTML
    }, '*');
  }

  // Footer HTML oluşturma (Tailwind CSS ile)
  generateFooterHTML(config) {
    const bgColor = config.styling?.background || 'bg-gray-800';
    const textColor = config.styling?.text_color || 'text-gray-300';
    
    return `
      <footer class="${bgColor} ${textColor}" x-data="{ backToTop: false }" @scroll.window="backToTop = window.scrollY > 300">
        <!-- Main Footer -->
        <div class="container mx-auto px-4 py-12">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-${config.columns || 4} gap-8">
            ${this.renderFooterColumns(config.widgets)}
          </div>
          
          ${config.social_media?.show ? this.renderSocialMedia(config.social_media) : ''}
        </div>
        
        <!-- Bottom Bar -->
        ${config.bottom_bar?.show ? this.renderBottomBar(config.bottom_bar) : ''}
        
        <!-- Back to Top Button -->
        ${config.back_to_top?.show ? `
          <button x-show="backToTop" 
                  x-transition
                  @click="window.scrollTo({top: 0, behavior: 'smooth'})"
                  class="fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700">
            <i class="fas fa-arrow-up"></i>
          </button>
        ` : ''}
      </footer>
    `;
  }

  // Widget columns render
  renderFooterColumns(widgets) {
    return widgets.map((widget, index) => `
      <div class="footer-column">
        ${this.renderWidget(widget)}
      </div>
    `).join('');
  }

  // Widget tipine göre render
  renderWidget(widget) {
    switch(widget.type) {
      case 'about_company':
        return `
          <div>
            ${widget.content.logo ? `<img src="${widget.content.logo}" alt="Logo" class="h-10 mb-4">` : ''}
            <p class="mb-4">${widget.content.description}</p>
          </div>
        `;
      
      case 'link_group':
        return `
          <div>
            <h3 class="text-white font-semibold mb-4">${widget.title}</h3>
            <ul class="space-y-2">
              ${widget.content.links.map(link => 
                `<li><a href="${link.url}" class="hover:text-white transition">${link.text}</a></li>`
              ).join('')}
            </ul>
          </div>
        `;
      
      case 'contact_info':
        return `
          <div>
            <h3 class="text-white font-semibold mb-4">${widget.title}</h3>
            <div class="space-y-2">
              ${widget.content.address ? `<p><i class="fas fa-map-marker-alt mr-2"></i>${widget.content.address}</p>` : ''}
              ${widget.content.phone ? `<p><i class="fas fa-phone mr-2"></i>${widget.content.phone}</p>` : ''}
              ${widget.content.email ? `<p><i class="fas fa-envelope mr-2"></i>${widget.content.email}</p>` : ''}
            </div>
          </div>
        `;
      
      case 'newsletter':
        return `
          <div>
            <h3 class="text-white font-semibold mb-4">${widget.title}</h3>
            <p class="mb-4">${widget.content.description}</p>
            <form class="space-y-2">
              <input type="email" placeholder="E-posta adresiniz" class="w-full px-4 py-2 bg-gray-700 text-white rounded">
              <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Abone Ol
              </button>
            </form>
          </div>
        `;
      
      default:
        return '';
    }
  }
}
```

**Preview Container Styling:**
```css
/* Footer preview container */
.footer-preview-wrapper {
  position: relative;
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.footer-preview-frame {
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  overflow: auto;
}

/* Footer widget drag-drop indicators */
.widget-dropzone {
  min-height: 100px;
  border: 2px dashed #dee2e6;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
}

.widget-dropzone.drag-over {
  border-color: #007bff;
  background: rgba(0, 123, 255, 0.1);
}
```

## 📱 Responsive Davranış

### Mobile (< 768px)
- Tek sütun layout
- Widget'ları dikey sıralama
- Newsletter widget gizleme (opsiyonel)
- Sosyal medya bottom'da
- Kompakt padding değerleri

### Tablet (768px - 1024px)
- 2 sütun layout
- Sosyal medya bottom pozisyon
- Orta boyut padding

### Desktop (> 1024px)
- Tam sütun görünümü (4 sütun)
- Tüm widget'lar aktif
- Büyük padding değerleri

## 🎨 Footer Widget Tipleri

### 1. About Company Widget
```php
'type' => 'about_company',
'content' => [
    'logo' => '/storage/logo.png',
    'description' => 'Company description...',
    'social_links' => true
]
```

### 2. Link Group Widget
```php
'type' => 'link_group',
'content' => [
    'links' => [
        ['text' => 'Home', 'url' => '/'],
        ['text' => 'About', 'url' => '/about']
    ]
]
```

### 3. Contact Info Widget
```php
'type' => 'contact_info',
'content' => [
    'address' => 'Company Address',
    'phone' => '+90 555 123 45 67',
    'email' => 'info@company.com'
]
```

### 4. Newsletter Widget
```php
'type' => 'newsletter',
'content' => [
    'title' => 'E-Bülten',
    'description' => 'Subscribe to our newsletter',
    'privacy_required' => true
]
```

### 5. Recent Posts Widget
```php
'type' => 'recent_posts',
'content' => [
    'count' => 3,
    'show_date' => true,
    'show_excerpt' => false
]
```

### 6. Gallery Widget
```php
'type' => 'gallery',
'content' => [
    'images' => [
        '/storage/gallery/1.jpg',
        '/storage/gallery/2.jpg'
    ],
    'columns' => 3
]
```

## 🔌 Footer API ve Hook'lar

### Widget Registration
```php
// Custom widget kaydetme
FooterBuilder::registerWidget('custom_widget', [
    'name' => 'Custom Widget',
    'description' => 'Custom widget description',
    'fields' => [...],
    'render_callback' => 'render_custom_widget'
]);
```

### Render Hooks
```php
// Footer render öncesi
do_action('footer_before_render', $config);

// Widget render öncesi/sonrası
apply_filters('footer_widget_html', $html, $widget, $config);

// Social media filter
apply_filters('footer_social_media_html', $html, $config);

// Copyright text filter
apply_filters('footer_copyright_text', $text, $config);
```

### Custom CSS/JS
```php
// Footer'a özel CSS ekleme
FooterBuilder::addCustomCSS($tenantId, $customCSS);

// Newsletter form JavaScript
FooterBuilder::addScript('newsletter-form', $jsCode);
```

## 🎯 Footer Templates

### Business Template
- 4 sütun: Hakkımızda, Hizmetler, İletişim, Newsletter
- Sosyal medya widget'ta
- Detaylı iletişim bilgileri

### E-commerce Template
- Ürün kategorileri, Müşteri hizmetleri, Hesabım, Ödeme yöntemleri
- Newsletter ve sosyal medya
- Güvenlik sertifikaları

### Blog Template
- Son yazılar, Kategoriler, Etiketler, Hakkımızda
- Sosyal medya prominent
- RSS feed linki

### Portfolio Template
- Minimal design
- Sosyal medya odaklı
- Basit iletişim bilgileri

## 💡 İleri Düzey Özellikler

### Dynamic Content
- Son blog yazıları widget'ı
- Popüler ürünler listesi
- Sosyal medya feed entegrasyonu

### Analytics Integration
- Newsletter signup tracking
- Social media click tracking
- Footer link analytics

### Multi-language Support
- Widget içeriklerinin çevrilmesi
- Dil bazında farklı footer'lar
- RTL desteği

---

*Footer Builder, web sitesinin bilgi mimarisi ve kullanıcı yönlendirmesi açısından kritik bir bileşendir. SEO ve kullanıcı deneyimi için optimize edilmiştir.*