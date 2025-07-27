# Global Content Editor - Kullanım Kılavuzu

## 🎯 Amaç
Content Editor artık global bir component olarak tasarlandı. Tüm modüllerde kullanılabilir ve her modülün ihtiyacına göre özelleştirilebilir.

## 📍 Dosya Konumu
```
resources/views/admin/components/content-editor.blade.php
```

## 🚀 Temel Kullanım

### Basit Kullanım (Page modülü gibi)
```blade
@include('admin.components.content-editor', [
    'lang' => $lang,
    'langName' => $langName,
    'langData' => $langData
])
```

### Gelişmiş Kullanım (Özelleştirme ile)
```blade
@include('admin.components.content-editor', [
    'lang' => $lang,
    'langName' => $langName,
    'langData' => $langData,
    'fieldName' => 'description',           // Varsayılan: 'body'
    'modelPath' => 'postInputs',           // Varsayılan: 'multiLangInputs'
    'label' => 'Açıklama',                 // Varsayılan: 'İçerik'
    'placeholder' => 'Açıklamayı yazın',   // Varsayılan: 'İçeriği buraya yazın'
    'height' => '300px',                   // Varsayılan: '500px'
    'required' => false                    // Varsayılan: true
])
```

## 📋 Parametreler

| Parametre | Tip | Varsayılan | Açıklama |
|-----------|-----|------------|----------|
| `$lang` | string | **Zorunlu** | Dil kodu (tr, en, vs.) |
| `$langName` | string | **Zorunlu** | Dil adı (Türkçe, English, vs.) |
| `$langData` | array | **Zorunlu** | Mevcut dil verisi array'i |
| `$fieldName` | string | `'body'` | Field adı (body, content, description, vs.) |
| `$modelPath` | string | `'multiLangInputs'` | Model path (multiLangInputs, content, vs.) |
| `$label` | string | `'İçerik'` | Label metni |
| `$placeholder` | string | `'İçeriği buraya yazın'` | Placeholder metni |
| `$required` | boolean | `true` | Zorunlu field mi? (sadece default dil için) |
| `$height` | string | `'500px'` | Editor yüksekliği |

## 🎨 Kullanım Örnekleri

### 1. Portfolio Modülü için
```blade
@include('admin.components.content-editor', [
    'lang' => $lang,
    'langName' => $langName,
    'langData' => $langData,
    'fieldName' => 'description',
    'modelPath' => 'portfolioInputs',
    'label' => __('portfolio::admin.description'),
    'placeholder' => __('portfolio::admin.description_placeholder'),
    'height' => '400px'
])
```

### 2. Blog Modülü için
```blade
@include('admin.components.content-editor', [
    'lang' => $lang,
    'langName' => $langName,
    'langData' => $langData,
    'fieldName' => 'content',
    'modelPath' => 'blogInputs',
    'label' => __('blog::admin.content'),
    'placeholder' => __('blog::admin.content_placeholder'),
    'height' => '600px'
])
```

### 3. Announcement Modülü için
```blade
@include('admin.components.content-editor', [
    'lang' => $lang,
    'langName' => $langName,
    'langData' => $langData,
    'fieldName' => 'message',
    'modelPath' => 'announcementInputs',
    'label' => __('announcement::admin.message'),
    'placeholder' => __('announcement::admin.message_placeholder'),
    'height' => '300px',
    'required' => true
])
```

## 🔧 Teknik Özellikler

### Auto-generated IDs
Component her kullanımda unique ID'ler oluşturur:
- Editor ID: `editor_{fieldName}_{lang}_{uniqueId}`
- Hidden ID: `hidden_{fieldName}_{lang}_{uniqueId}`

### Livewire Integration
```php
// Wire model otomatik oluşturulur
$wireModel = "{$modelPath}.{$lang}.{$fieldName}";
// Örnek: "multiLangInputs.tr.body"
```

### HugeRTE Integration
- Otomatik initialization
- Custom initialization function desteği
- Fallback mechanism

## 🎯 Avantajlar

✅ **Global Kullanım**: Tüm modüllerde kullanılabilir
✅ **Flexible**: Her modül için özelleştirilebilir
✅ **Consistent**: Tutarlı UI/UX deneyimi
✅ **Maintainable**: Tek yerden yönetim
✅ **Multi-language**: Çoklu dil desteği
✅ **Auto-validation**: Otomatik validation (default dil için)

## 📝 Migration Guide

### Eski kullanım:
```blade
@include('module::admin.includes.content-editor', [
    'lang' => $lang,
    'langName' => $langName,
    'langData' => $langData,
])
```

### Yeni kullanım:
```blade
@include('admin.components.content-editor', [
    'lang' => $lang,
    'langName' => $langName,
    'langData' => $langData,
    'fieldName' => 'body',
    'label' => __('module::admin.content'),
    'placeholder' => __('module::admin.content_placeholder')
])
```

## 🚨 Önemli Notlar

1. **Lang Data Format**: `$langData[$fieldName]` formatında veri beklenir
2. **Required Fields**: Sadece default dil için zorunlu field işaretlemesi
3. **Unique IDs**: Her include otomatik unique ID oluşturur
4. **HugeRTE**: Otomatik initialization ile çalışır

## 🔮 Gelecek Geliştirmeler

- [ ] Multiple editor types (TinyMCE, CKEditor vs.)
- [ ] Rich text formatting options
- [ ] File upload integration
- [ ] Template system
- [ ] Auto-save functionality