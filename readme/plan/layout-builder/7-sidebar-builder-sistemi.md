# 🎯 SIDEBAR BUILDER SİSTEMİ

## 📋 Sidebar Bileşeni Özeti

Sidebar, web sitesinin yan panelinde yer alan ve dinamik widget'lar içeren esnek bir layout bileşenidir. Blog yazıları, kategoriler, son ürünler, sosyal medya feeds ve custom HTML içerikleri barındırabilir.

## 🏗️ Sidebar Yapısı ve Bileşenler

### Ana Bileşenler
1. **Widget Areas** - Dinamik widget konteynerleri
2. **Sticky Elements** - Sabit pozisyonlu widget'lar
3. **Responsive Toggles** - Mobilde daraltma/genişletme
4. **Custom Widgets** - Özel HTML/JavaScript widget'ları
5. **Dynamic Content** - Veritabanından gelen dinamik içerik

## 🎨 Sidebar Layout Tipleri

### 1. Left Sidebar
```
┌─────────┬─────────────────────────────────┐
│ SIDEBAR │                                 │
│         │         MAIN CONTENT            │
│ Widget1 │                                 │
│ Widget2 │                                 │
│ Widget3 │                                 │
│         │                                 │
└─────────┴─────────────────────────────────┘
```

### 2. Right Sidebar
```
┌─────────────────────────────────┬─────────┐
│                                 │ SIDEBAR │
│         MAIN CONTENT            │         │
│                                 │ Widget1 │
│                                 │ Widget2 │
│                                 │ Widget3 │
│                                 │         │
└─────────────────────────────────┴─────────┘
```

### 3. Both Sidebars
```
┌─────┬─────────────────────────┬─────────┐
│LEFT │                         │  RIGHT  │
│     │      MAIN CONTENT       │         │
│Wid1 │                         │ Widget1 │
│Wid2 │                         │ Widget2 │
│Wid3 │                         │ Widget3 │
│     │                         │         │
└─────┴─────────────────────────┴─────────┘
```

### 4. Collapsible Sidebar
```
┌─┬───────────────────────────────────────┐
│☰│                                       │
│ │            MAIN CONTENT               │
│ │                                       │
│ │  (Sidebar collapses to icon)          │
│ │                                       │
└─┴───────────────────────────────────────┘
```

## 🔧 Sidebar Konfigürasyon Şeması

```json
{
  "sidebar": {
    "show": true,
    "position": "left|right|both",
    "layout": "standard|sticky|collapsible|overlay",
    "width": {
      "left": "25%|300px|custom",
      "right": "25%|300px|custom",
      "custom_width": "350px"
    },
    "sticky": {
      "enable": true,
      "offset_top": "20px",
      "stop_at_footer": true,
      "breakpoint": "desktop|tablet|never"
    },
    "collapsible": {
      "enable": false,
      "default_state": "expanded|collapsed",
      "toggle_position": "top|bottom",
      "animation": "slide|fade|none",
      "remember_state": true
    },
    "widgets": [
      {
        "id": "recent_posts",
        "type": "recent_posts",
        "position": "left|right",
        "order": 1,
        "title": "Son Yazılar",
        "show_title": true,
        "content": {
          "count": 5,
          "show_date": true,
          "show_excerpt": false,
          "show_image": true,
          "image_size": "thumbnail|small|medium",
          "date_format": "d.m.Y",
          "excerpt_length": 100
        }
      },
      {
        "id": "categories",
        "type": "categories",
        "position": "left|right",
        "order": 2,
        "title": "Kategoriler",
        "show_title": true,
        "content": {
          "show_count": true,
          "hierarchical": true,
          "max_depth": 3,
          "hide_empty": true,
          "orderby": "name|count|menu_order",
          "order": "ASC|DESC",
          "show_icons": false
        }
      },
      {
        "id": "tags_cloud",
        "type": "tag_cloud", 
        "position": "right",
        "order": 3,
        "title": "Etiketler",
        "show_title": true,
        "content": {
          "max_tags": 20,
          "min_font_size": "12px",
          "max_font_size": "18px",
          "orderby": "name|count|random",
          "order": "ASC|DESC"
        }
      },
      {
        "id": "search_widget",
        "type": "search",
        "position": "left|right",
        "order": 4,
        "title": "Arama",
        "show_title": true,
        "content": {
          "placeholder": "Site içinde ara...",
          "button_text": "Ara",
          "show_button": true,
          "style": "minimal|modern|rounded",
          "autocomplete": true,
          "search_in": "posts|pages|all"
        }
      },
      {
        "id": "social_follow",
        "type": "social_media",
        "position": "right",
        "order": 5,
        "title": "Takip Edin",
        "show_title": true,
        "content": {
          "style": "icons|buttons|combined",
          "size": "small|medium|large",
          "show_counters": false,
          "platforms": {
            "facebook": {
              "url": "https://facebook.com/company",
              "show": true,
              "icon": "fab fa-facebook"
            },
            "twitter": {
              "url": "https://twitter.com/company",
              "show": true,
              "icon": "fab fa-twitter"
            }
          }
        }
      },
      {
        "id": "newsletter_signup",
        "type": "newsletter",
        "position": "right",
        "order": 6,
        "title": "E-Bülten",
        "show_title": true,
        "content": {
          "description": "Yeni yazılarımızdan haberdar olun!",
          "email_placeholder": "E-posta adresiniz",
          "button_text": "Abone Ol",
          "success_message": "Başarıyla abone oldunuz!",
          "privacy_text": "Gizlilik politikamızı kabul ediyorum.",
          "privacy_required": true
        }
      },
      {
        "id": "custom_html",
        "type": "custom_html",
        "position": "left|right",
        "order": 7,
        "title": "Özel İçerik",
        "show_title": false,
        "content": {
          "html": "<div class=\"custom-widget\">Custom HTML Content</div>",
          "css": ".custom-widget { padding: 20px; }",
          "js": "console.log('Custom widget loaded');"
        }
      }
    ],
    "styling": {
      "background": "#ffffff",
      "border": {
        "show": true,
        "color": "#dee2e6",
        "width": "1px",
        "style": "solid",
        "radius": "4px"
      },
      "padding": {
        "top": "20px",
        "bottom": "20px",
        "left": "20px",
        "right": "20px"
      },
      "margin": {
        "top": "0px",
        "bottom": "30px",
        "left": "0px",
        "right": "0px"
      },
      "widget_spacing": "25px",
      "colors": {
        "text_color": "#333333",
        "heading_color": "#2c3e50",
        "link_color": "#007bff",
        "link_hover_color": "#0056b3",
        "border_color": "#dee2e6"
      },
      "typography": {
        "font_family": "inherit|Arial|Georgia|custom",
        "font_size": "14px",
        "line_height": "1.6",
        "heading_font_size": "16px",
        "heading_font_weight": "600"
      },
      "shadow": {
        "show": false,
        "blur": "10px",
        "color": "rgba(0,0,0,0.1)",
        "offset_x": "0px",
        "offset_y": "2px"
      }
    },
    "responsive": {
      "mobile": {
        "hide": false,
        "position": "bottom|top|modal",
        "full_width": true,
        "collapsible": true,
        "default_collapsed": true,
        "toggle_text": "Widget'ları Göster/Gizle"
      },
      "tablet": {
        "width": "30%",
        "position": "right",
        "sticky": false
      }
    },
    "conditional_display": {
      "pages": {
        "show_on": ["blog", "single_post", "category", "search"],
        "hide_on": ["homepage", "contact", "about"]
      },
      "user_roles": {
        "show_for": ["all"],
        "hide_for": []
      },
      "device": {
        "show_on_mobile": true,
        "show_on_tablet": true,
        "show_on_desktop": true
      }
    },
    "performance": {
      "lazy_load": true,
      "cache_widgets": true,
      "cache_duration": "1 hour",
      "async_load": false
    }
  }
}
```

## 🎛️ Sidebar Builder Interface

### Konfigürasyon Sekmeleri
1. **Layout** - Pozisyon, genişlik, sticky ayarları
2. **Widgets** - Widget yönetimi ve sıralama
3. **Styling** - Görünüm ve renk ayarları
4. **Responsive** - Mobil/tablet davranışı
5. **Conditional** - Gösterim koşulları
6. **Performance** - Cache ve optimizasyon

### Visual Widget Manager
```
┌─ SIDEBAR BUILDER ────────────────────────────────────────┐
│ Position: [Right ▼]  Width: [25% ▼]  ☑ Sticky          │
│                                                          │
│ Widget Management:                                       │
│ ┌─ LEFT SIDEBAR ────────┬─ RIGHT SIDEBAR ─────────────┐  │
│ │ [≡] Recent Posts (1)  │ [≡] Categories (1)        │  │
│ │ [≡] Search (2)        │ [≡] Tags Cloud (2)        │  │
│ │ [+ Add Widget]        │ [≡] Social Media (3)      │  │
│ │                       │ [≡] Newsletter (4)        │  │
│ │                       │ [+ Add Widget]            │  │
│ └───────────────────────┴───────────────────────────────┘  │
│                                                          │
│ Available Widgets:                                       │
│ [📝 Recent Posts] [📁 Categories] [🏷️ Tags] [🔍 Search]    │
│ [📱 Social Media] [📧 Newsletter] [🎨 Custom HTML]        │
│                                                          │
│ Widget Settings (Selected: Recent Posts):               │
│ Title: [Son Yazılar]  ☑ Show Title                     │
│ Count: [5]  ☑ Show Date  ☑ Show Image                  │
│ Image Size: [Thumbnail ▼]  Date Format: [d.m.Y]        │
│                                                          │
│ Styling:                                                 │
│ Background: [#ffffff]  Border: ☑ Show  Padding: [20px]  │
│ Widget Spacing: [25px]  Shadow: ☐ Show                 │
│                                                          │
│ [Save Configuration] [Preview] [Reset]                  │
└──────────────────────────────────────────────────────────┘
```

## 📱 Responsive Davranış

### Mobile (< 768px)
- Sidebar → Bottom or Modal
- Full width layout
- Collapsible widget groups
- Touch-friendly toggles
- Optimized font sizes

### Tablet (768px - 1024px)
- Sidebar width: 30%
- Sticky disabled (opsiyonel)
- Reduced widget spacing
- Simplified layouts

### Desktop (> 1024px)
- Full sidebar functionality
- Sticky positioning
- All widgets visible
- Hover effects enabled

## 🎨 Sidebar Widget Tipleri

### 1. Recent Posts Widget
```php
'type' => 'recent_posts',
'content' => [
    'count' => 5,
    'show_date' => true,
    'show_excerpt' => true,
    'excerpt_length' => 100,
    'show_image' => true,
    'image_size' => 'thumbnail'
]
```

### 2. Categories Widget
```php
'type' => 'categories',
'content' => [
    'show_count' => true,
    'hierarchical' => true,
    'hide_empty' => true,
    'orderby' => 'name',
    'show_icons' => false
]
```

### 3. Tag Cloud Widget
```php
'type' => 'tag_cloud',
'content' => [
    'max_tags' => 20,
    'min_font_size' => '12px',
    'max_font_size' => '18px',
    'orderby' => 'count'
]
```

### 4. Search Widget
```php
'type' => 'search',
'content' => [
    'placeholder' => 'Ara...',
    'show_button' => true,
    'autocomplete' => true,
    'style' => 'modern'
]
```

### 5. Social Media Widget
```php
'type' => 'social_media',
'content' => [
    'style' => 'icons',
    'show_counters' => false,
    'platforms' => ['facebook', 'twitter', 'instagram']
]
```

### 6. Newsletter Widget
```php
'type' => 'newsletter',
'content' => [
    'description' => 'Subscribe to our newsletter',
    'privacy_required' => true,
    'success_message' => 'Successfully subscribed!'
]
```

### 7. Custom HTML Widget
```php
'type' => 'custom_html',
'content' => [
    'html' => '<div>Custom Content</div>',
    'css' => 'div { color: red; }',
    'js' => 'console.log("loaded");'
]
```

### 8. Archives Widget
```php
'type' => 'archives',
'content' => [
    'type' => 'monthly|yearly',
    'show_count' => true,
    'limit' => 12,
    'dropdown' => false
]
```

### 9. Popular Posts Widget
```php
'type' => 'popular_posts',
'content' => [
    'count' => 5,
    'period' => 'week|month|all_time',
    'show_views' => true,
    'show_date' => true
]
```

### 10. Calendar Widget
```php
'type' => 'calendar',
'content' => [
    'show_navigation' => true,
    'highlight_current' => true,
    'link_posts' => true
]
```

## 🔌 Sidebar API ve Hook'lar

### Widget Registration
```php
// Yeni widget tipi kaydetme
SidebarBuilder::registerWidget('custom_widget', [
    'name' => 'Custom Widget',
    'description' => 'A custom widget',
    'fields' => [
        'title' => 'text',
        'content' => 'textarea',
        'count' => 'number'
    ],
    'render_callback' => 'render_custom_widget'
]);

// Widget render callback
function render_custom_widget($config) {
    return view('widgets.custom', $config)->render();
}
```

### Dynamic Widget Loading
```php
// Runtime'da widget ekleme
SidebarBuilder::addWidget('left', [
    'type' => 'recent_posts',
    'title' => 'Son Yazılar',
    'order' => 1
]);

// Widget kaldırma
SidebarBuilder::removeWidget('left', 'recent_posts');

// Widget sıralama
SidebarBuilder::reorderWidget('left', 'search', 2);
```

### Hook'lar ve Filters
```php
// Sidebar render öncesi
do_action('sidebar_before_render', $position, $widgets);

// Widget render öncesi/sonrası
apply_filters('sidebar_widget_html', $html, $widget, $config);

// Widget başlık filter
apply_filters('sidebar_widget_title', $title, $widget);

// Sidebar pozisyon filter
apply_filters('sidebar_positions', ['left', 'right']);
```

### Conditional Widget Display
```php
// Sayfa tipine göre widget gösterimi
add_filter('sidebar_show_widget', function($show, $widget, $context) {
    if ($context['page_type'] === 'homepage' && $widget['type'] === 'recent_posts') {
        return false; // Homepage'de son yazılar widget'ını gizle
    }
    return $show;
}, 10, 3);
```

## 🚀 İleri Düzey Özellikler

### Widget Caching
```php
// Widget-specific caching
Cache::remember("sidebar_widget_{$widgetId}", 3600, function() use ($widget) {
    return SidebarBuilder::renderWidget($widget);
});

// Smart cache invalidation
SidebarBuilder::invalidateWidgetCache('recent_posts', 'post_updated');
```

### AJAX Widget Loading
```javascript
// Asenkron widget yükleme
class SidebarManager {
    loadWidget(widgetId, position) {
        fetch(`/api/sidebar/widget/${widgetId}`)
            .then(response => response.text())
            .then(html => {
                document.querySelector(`#sidebar-${position} .widget-${widgetId}`)
                    .innerHTML = html;
            });
    }
}
```

### Widget Analytics
```php
// Widget etkileşim tracking
SidebarBuilder::trackWidgetInteraction('recent_posts', 'click', [
    'post_id' => $postId,
    'position' => 'left',
    'user_id' => auth()->id()
]);

// Widget performans metrikleri
SidebarBuilder::getWidgetMetrics('recent_posts', [
    'views', 'clicks', 'conversion_rate'
]);
```

## 🎛️ Widget Template System

### Widget Template Structure
```php
// widgets/recent-posts.blade.php
<div class="widget widget-recent-posts">
    @if($showTitle)
        <h3 class="widget-title">{{ $title }}</h3>
    @endif
    
    <div class="widget-content">
        @foreach($posts as $post)
            <div class="recent-post-item">
                @if($showImage && $post->featured_image)
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}">
                @endif
                
                <div class="post-info">
                    <h4><a href="{{ $post->url }}">{{ $post->title }}</a></h4>
                    
                    @if($showDate)
                        <span class="post-date">{{ $post->published_at->format($dateFormat) }}</span>
                    @endif
                    
                    @if($showExcerpt)
                        <p class="post-excerpt">{{ Str::limit($post->excerpt, $excerptLength) }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
```

### Widget CSS Framework
```css
/* Widget base styles */
.widget {
    margin-bottom: var(--widget-spacing, 25px);
    padding: var(--widget-padding, 20px);
    background: var(--widget-bg, #ffffff);
    border: var(--widget-border, 1px solid #dee2e6);
    border-radius: var(--widget-radius, 4px);
}

.widget-title {
    margin-bottom: 15px;
    font-size: var(--widget-title-size, 16px);
    font-weight: var(--widget-title-weight, 600);
    color: var(--widget-title-color, #2c3e50);
}

.widget-content {
    font-size: var(--widget-content-size, 14px);
    line-height: var(--widget-line-height, 1.6);
}
```

---

*Sidebar Builder, content-rich sitelerde kullanıcı etkileşimini artıran ve site içi navigasyonu güçlendiren önemli bir bileşendir. Widget sistemi ile tamamen özelleştirilebilir.*