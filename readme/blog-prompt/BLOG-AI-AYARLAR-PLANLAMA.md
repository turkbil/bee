# 🤖 BLOG AI AYARLARI - PLANLAMA

**Tarih**: 2025-11-14
**Lokasyon**: `/admin/settingmanagement/values/18` (Blog - Yapay Zeka)
**Group ID**: 18 (Mevcut, içinde ayar yok)

---

## 📊 MEVCUT SETTINGMANAGEMENT YAPISI

### ✅ Analiz Edilen Yapı:
- **Model**: `SettingGroup` (Central DB) + `Setting` (Central DB) + `SettingValue` (Tenant DB)
- **Livewire Component**: `ValuesComponent.php`
- **View**: Dynamic form builder (layout JSON veya fallback HTML)
- **Desteklenen Field Type'lar**:
  - `text`, `textarea`, `number`, `email`, `url`, `tel`, `password`
  - `select`, `checkbox`, `date`, `time`, `color`
  - `image`, `file`, `favicon`, `image_multiple`

### 🎯 Layout System (JSON):
```json
{
  "elements": [
    {
      "type": "field",
      "setting_key": "blog_ai_daily_count",
      "width": 6
    },
    {
      "type": "section",
      "title": "Konu Kaynakları",
      "subtitle": "Blog konularının nereden alınacağını belirleyin",
      "elements": [...]
    }
  ]
}
```

---

## 🎨 BLOG AI AYARLARI - TAM LİSTE

### 1️⃣ TEMEL AYARLAR (Sistem Kontrol)

#### ✅ `blog_ai_enabled` (checkbox)
- **Label**: Blog AI Sistemi Aktif
- **Key**: `blog_ai_enabled`
- **Type**: `checkbox`
- **Default**: `0` (Kapalı)
- **Açıklama**: Sistemi tamamen açar/kapatır

#### ✅ `blog_ai_daily_count` (number)
- **Label**: Günlük Blog Sayısı
- **Key**: `blog_ai_daily_count`
- **Type**: `number`
- **Default**: `2`
- **Min**: 1, **Max**: 50
- **Açıklama**: Her gün kaç blog oluşturulsun?

#### ✅ `blog_ai_auto_publish` (checkbox)
- **Label**: Otomatik Yayınlama
- **Key**: `blog_ai_auto_publish`
- **Type**: `checkbox`
- **Default**: `1` (Açık)
- **Açıklama**: Blog yazılınca otomatik yayınlansın mı?

---

### 2️⃣ KONU KAYNAKLARI (Topic Sources)

#### ✅ `blog_ai_topic_source` (select)
- **Label**: Konu Kaynağı
- **Key**: `blog_ai_topic_source`
- **Type**: `select`
- **Choices**:
  - `manual`: Manuel (Aşağıdaki listeden)
  - `auto`: Otomatik (Ürün/Kategori analizi)
  - `mixed`: Karma (Önce manuel, sonra otomatik)
- **Default**: `mixed`

#### ✅ `blog_ai_manual_topics` (textarea) **YENİ ÖZELLİK!**
- **Label**: Ana Konular (Manuel Liste)
- **Key**: `blog_ai_manual_topics`
- **Type**: `textarea`
- **Rows**: 15
- **Placeholder**:
```
transpalet
forklift
akülü istif makinesi
reach truck
```
- **Default**: `null`
- **Açıklama**:
  > 📝 **Manuel konu girişi**:
  > - Her satıra bir ana konu yazın (örn: "transpalet")
  > - Sistem bu konuları genişletecek:
  >   - transpalet nedir
  >   - nasıl kullanılır
  >   - elektrikli transpalet nedir
  >   - manuel transpalet nedir
  >   - en iyi transpaletler
  >   - en iyi transpalet markaları
  > - Boş bırakırsanız otomatik konu bulur (ürün/kategori)
  > - **Duplicate kontrol**: Mevcut blog başlıklarına bakar, aynı başlık oluşturmaz

---

### 3️⃣ KONU GENİŞLETME SİSTEMİ **YENİ!**

#### ✅ `blog_ai_topic_expand_enabled` (checkbox)
- **Label**: Konu Genişletme Aktif
- **Key**: `blog_ai_topic_expand_enabled`
- **Type**: `checkbox`
- **Default**: `1` (Açık)
- **Açıklama**: Manuel konuları otomatik genişletsin mi?

#### ✅ `blog_ai_topic_expand_count` (number)
- **Label**: Her Konudan Kaç Başlık Üretilsin
- **Key**: `blog_ai_topic_expand_count`
- **Type**: `number`
- **Default**: `10`
- **Min**: 5, **Max**: 100
- **Açıklama**:
  > Örnek: "transpalet" → 10 farklı blog başlığı
  > - transpalet nedir
  > - transpalet çeşitleri
  > - elektrikli transpalet özellikleri
  > ... (10 başlık)

#### ✅ `blog_ai_duplicate_check` (checkbox)
- **Label**: Mevcut Bloglara Bak (Duplicate Engelle)
- **Key**: `blog_ai_duplicate_check`
- **Type**: `checkbox`
- **Default**: `1` (Açık)
- **Açıklama**: Aynı başlıklı blog varsa oluşturmasın

---

### 4️⃣ OTOMATİK KONU BULMA (Auto Discovery)

#### ✅ `blog_ai_auto_source_products` (checkbox)
- **Label**: Ürünlerden Konu Bul
- **Key**: `blog_ai_auto_source_products`
- **Type**: `checkbox`
- **Default**: `1`

#### ✅ `blog_ai_auto_source_categories` (checkbox)
- **Label**: Kategorilerden Konu Bul
- **Key**: `blog_ai_auto_source_categories`
- **Type**: `checkbox`
- **Default**: `1`

#### ✅ `blog_ai_auto_priority` (select)
- **Label**: Otomatik Önceliklendirme
- **Key**: `blog_ai_auto_priority`
- **Type**: `select`
- **Choices**:
  - `most_viewed`: En çok görüntülenen ürünler
  - `newest`: En yeni ürünler
  - `no_blog`: Blogu olmayan ürünler
  - `mixed`: Karma (Hepsini karıştır)
- **Default**: `most_viewed`

---

### 5️⃣ İÇERİK STİLİ (Writing Style)

#### ✅ `blog_ai_style_rotation` (checkbox)
- **Label**: Stil Rotasyonu (Otomatik Değiştir)
- **Key**: `blog_ai_style_rotation`
- **Type**: `checkbox`
- **Default**: `1` (Açık)

#### ✅ `blog_ai_style_order` (select)
- **Label**: Stil Sırası
- **Key**: `blog_ai_style_order`
- **Type**: `select`
- **Choices**:
  - `professional_friendly_expert`: Profesyonel → Samimi → Uzman
  - `friendly_professional_expert`: Samimi → Profesyonel → Uzman
  - `expert_professional_friendly`: Uzman → Profesyonel → Samimi
  - `random`: Rastgele
- **Default**: `professional_friendly_expert`

---

### 6️⃣ SEO & İÇERİK AYARLARI

#### ✅ `blog_ai_min_words` (number)
- **Label**: Minimum Kelime Sayısı
- **Key**: `blog_ai_min_words`
- **Type**: `number`
- **Default**: `2000`
- **Min**: 500, **Max**: 5000

#### ✅ `blog_ai_max_words` (number)
- **Label**: Maximum Kelime Sayısı
- **Key**: `blog_ai_max_words`
- **Type**: `number`
- **Default**: `2500`
- **Min**: 1000, **Max**: 10000

#### ✅ `blog_ai_language` (select)
- **Label**: Blog Dili
- **Key**: `blog_ai_language`
- **Type**: `select`
- **Choices**:
  - `tr`: Türkçe
  - `en`: İngilizce
  - `ar`: Arapça
- **Default**: `tr`

#### ✅ `blog_ai_seo_2025_enabled` (checkbox)
- **Label**: 2025 SEO Standartları Aktif
- **Key**: `blog_ai_seo_2025_enabled`
- **Type**: `checkbox`
- **Default**: `1`
- **Açıklama**: E-E-A-T, Core Web Vitals, Schema markup

---

### 7️⃣ AI PROVIDER AYARLARI

#### ✅ `blog_ai_provider` (select)
- **Label**: AI Sağlayıcı
- **Key**: `blog_ai_provider`
- **Type**: `select`
- **Choices**:
  - `openai`: OpenAI (GPT-4 Turbo)
  - `anthropic`: Anthropic (Claude)
  - `system`: Sistem AI (Mevcut AI modülü)
- **Default**: `system`

#### ✅ `blog_ai_model` (text)
- **Label**: AI Model
- **Key**: `blog_ai_model`
- **Type**: `text`
- **Default**: `gpt-4-turbo`
- **Placeholder**: `gpt-4-turbo`, `claude-3-opus`, `system-default`

#### ✅ `blog_ai_temperature` (number)
- **Label**: AI Yaratıcılık (Temperature)
- **Key**: `blog_ai_temperature`
- **Type**: `number`
- **Default**: `0.7`
- **Min**: 0.1, **Max**: 1.0, **Step**: 0.1
- **Açıklama**: Düşük = Daha tutarlı, Yüksek = Daha yaratıcı

---

### 8️⃣ ZAMANLAMA (Scheduling)

#### ✅ `blog_ai_schedule_times` (textarea)
- **Label**: Çalışma Saatleri (Cron Times)
- **Key**: `blog_ai_schedule_times`
- **Type**: `textarea`
- **Rows**: 5
- **Placeholder**:
```
06:00
12:00
18:00
20:00
```
- **Default**:
```
06:00
20:00
```
- **Açıklama**: Günlük blog sayısına göre otomatik bölünür

---

### 9️⃣ GELİŞMİŞ AYARLAR

#### ✅ `blog_ai_featured_image_v2` (checkbox)
- **Label**: Otomatik Görsel Üretimi (v2.0 - Yakında)
- **Key**: `blog_ai_featured_image_v2`
- **Type**: `checkbox`
- **Default**: `0` (Kapalı)
- **Disabled**: true (Şimdilik devre dışı)

#### ✅ `blog_ai_queue_enabled` (checkbox)
- **Label**: Queue Sistemi Kullan
- **Key**: `blog_ai_queue_enabled`
- **Type**: `checkbox`
- **Default**: `1`
- **Açıklama**: Arka planda işleme (önerilir)

#### ✅ `blog_ai_retry_on_fail` (number)
- **Label**: Hata Durumunda Tekrar Dene
- **Key**: `blog_ai_retry_on_fail`
- **Type**: `number`
- **Default**: `3`
- **Min**: 0, **Max**: 10

---

## 🎨 LAYOUT TASARIMI (JSON)

### Group Layout Config:
```json
{
  "elements": [
    {
      "type": "section",
      "title": "Sistem Kontrol",
      "subtitle": "Blog AI sistemini açın/kapatın ve genel ayarları yapın",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_enabled", "width": 4},
        {"type": "field", "setting_key": "blog_ai_daily_count", "width": 4},
        {"type": "field", "setting_key": "blog_ai_auto_publish", "width": 4}
      ]
    },
    {
      "type": "section",
      "title": "Konu Kaynakları",
      "subtitle": "Blog konularını nereden alacağını belirleyin",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_topic_source", "width": 12},
        {"type": "field", "setting_key": "blog_ai_manual_topics", "width": 12},
        {"type": "alert", "variant": "info", "content": "📝 Manuel konu girişi: Her satıra bir ana konu yazın. Sistem bu konuları otomatik genişletecek.", "width": 12}
      ]
    },
    {
      "type": "section",
      "title": "Konu Genişletme Sistemi",
      "subtitle": "Manuel konulardan otomatik başlık üretimi",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_topic_expand_enabled", "width": 4},
        {"type": "field", "setting_key": "blog_ai_topic_expand_count", "width": 4},
        {"type": "field", "setting_key": "blog_ai_duplicate_check", "width": 4}
      ]
    },
    {
      "type": "section",
      "title": "Otomatik Konu Bulma",
      "subtitle": "Manuel liste boşsa sistemin otomatik konu bulmasını sağlayın",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_auto_source_products", "width": 4},
        {"type": "field", "setting_key": "blog_ai_auto_source_categories", "width": 4},
        {"type": "field", "setting_key": "blog_ai_auto_priority", "width": 4}
      ]
    },
    {
      "type": "section",
      "title": "İçerik Stili",
      "subtitle": "Blog yazma stilini özelleştirin",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_style_rotation", "width": 6},
        {"type": "field", "setting_key": "blog_ai_style_order", "width": 6}
      ]
    },
    {
      "type": "section",
      "title": "SEO & İçerik",
      "subtitle": "Kelime sayısı ve SEO ayarları",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_min_words", "width": 4},
        {"type": "field", "setting_key": "blog_ai_max_words", "width": 4},
        {"type": "field", "setting_key": "blog_ai_language", "width": 4},
        {"type": "field", "setting_key": "blog_ai_seo_2025_enabled", "width": 12}
      ]
    },
    {
      "type": "section",
      "title": "AI Provider",
      "subtitle": "Hangi AI sistemini kullanacağınızı seçin",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_provider", "width": 4},
        {"type": "field", "setting_key": "blog_ai_model", "width": 4},
        {"type": "field", "setting_key": "blog_ai_temperature", "width": 4}
      ]
    },
    {
      "type": "section",
      "title": "Zamanlama",
      "subtitle": "Blogların hangi saatlerde yazılacağını belirleyin",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_schedule_times", "width": 12},
        {"type": "alert", "variant": "warning", "content": "⏰ Günlük blog sayısı saatlere otomatik bölünür. Örnek: 2 blog/gün → 06:00, 20:00", "width": 12}
      ]
    },
    {
      "type": "section",
      "title": "Gelişmiş Ayarlar",
      "subtitle": "Queue, retry ve gelecek özellikler",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_featured_image_v2", "width": 4},
        {"type": "field", "setting_key": "blog_ai_queue_enabled", "width": 4},
        {"type": "field", "setting_key": "blog_ai_retry_on_fail", "width": 4}
      ]
    }
  ]
}
```

---

## 💡 MANUEL KONU GENİŞLETME SİSTEMİ - DETAY

### Kullanıcı Senaryosu:
**Kullanıcı yazdı:**
```
transpalet
forklift
akülü istif makinesi
```

### Sistem Ne Yapar?

**1. Konuları Parse Eder:**
```php
$topics = [
    'transpalet',
    'forklift',
    'akülü istif makinesi'
];
```

**2. Her Konu İçin AI'dan Başlık İster:**
```
Prompt: "transpalet" konusu için Google'da en çok aranan 10 blog başlığı oluştur:
- transpalet nedir
- nasıl kullanılır
- elektrikli transpalet nedir
- manuel transpalet nedir
- transpalet çeşitleri
- en iyi transpaletler
- en iyi transpalet markaları
- transpalet fiyatları
- transpalet bakımı
- transpalet seçim rehberi
```

**3. Mevcut Bloglara Bakar (Duplicate Check):**
```sql
SELECT title FROM blogs WHERE
  title LIKE '%transpalet nedir%' OR
  title LIKE '%transpalet çeşitleri%'
  ...
```

**4. Duplicate Olanları Filtreler:**
```
✅ transpalet nedir → Blog var, atla
✅ nasıl kullanılır → Blog yok, ekle
✅ elektrikli transpalet nedir → Blog yok, ekle
...
```

**5. Kuyruğa Ekler:**
```php
BlogTopicQueue::create([
    'main_topic' => 'transpalet',
    'expanded_title' => 'Transpalet Nasıl Kullanılır? Adım Adım Rehber',
    'source' => 'manual_expansion',
    'priority' => 10,
    'status' => 'pending'
]);
```

**6. Cron Çalışınca Sırayla Yazar:**
```
06:00 → "Transpalet Nasıl Kullanılır?" yazıldı
20:00 → "Elektrikli Transpalet Nedir?" yazıldı
06:00 (ertesi gün) → "Manuel Transpalet Nedir?" yazıldı
...
```

---

## 🎯 ÖNERİLER & İYİLEŞTİRMELER

### ✅ 1. CHECKBOX İLE TOPLU BAŞLIK ÜRETİMİ (v1.5)
**Kullanıcı talebi:**
> "gerekirse bana da 100 tane içerik üretir. ben önlerine checkbox yaparım. onları hazırlar."

**Çözüm:**
- **Yeni Sayfa**: `/admin/blog-ai/topic-generator`
- **Component**: `BlogTopicGeneratorComponent.php`
- **Özellikler**:
  - Manuel konu gir (textarea)
  - "100 Başlık Üret" butonu
  - Livewire table ile checkbox'lı liste
  - Seçilenleri kuyruğa ekle
  - "Yenile" butonu (yeni 100 başlık)

**Avantajlar:**
- Kullanıcı kontrol sahibi
- İstemediği başlıkları eler
- Checkbox seçimi kolay
- Settings'den ayrı, özel sayfa

**İmplementasyon:**
- v1.0'da Settings ayarları
- v1.5'te Topic Generator sayfası

---

### ✅ 2. "ŞİMDİ OLUŞTUR" BUTONU
**Lokasyon**: Settings sayfasında büyük buton

**Özellik:**
```blade
<div class="card border-primary mt-3">
  <div class="card-body text-center">
    <h3>🚀 Şimdi Blog Oluştur</h3>
    <p>Hemen 1 blog yaz ve yayınla (test için ideal)</p>
    <button wire:click="generateNow" class="btn btn-primary btn-lg">
      <i class="fas fa-magic me-2"></i> OLUŞTUR
    </button>
  </div>
</div>
```

**Backend:**
```php
public function generateNow()
{
    // Queue'ya bypass, direkt blog oluştur
    dispatch(new GenerateBlogNowJob())->onQueue('high');

    $this->dispatch('toast', [
        'title' => 'Blog Oluşturuluyor!',
        'message' => '5 dakika içinde hazır olacak...',
        'type' => 'info'
    ]);
}
```

---

### ✅ 3. DUPLICATE KONTROL ALGORİTMASI
**Akıllı Benzerlik Kontrolü:**

```php
// Basit başlık karşılaştırma
if (Str::slug($newTitle) === Str::slug($existingTitle)) {
    return true; // Duplicate
}

// Levenshtein distance (benzerlik oranı)
$similarity = similar_text($newTitle, $existingTitle, $percent);
if ($percent > 85) {
    return true; // %85 benzer, duplicate say
}

// Anahtar kelime benzerliği
$newKeywords = extractKeywords($newTitle);
$existingKeywords = extractKeywords($existingTitle);
$matchCount = count(array_intersect($newKeywords, $existingKeywords));
if ($matchCount >= 3) {
    return true; // 3'ten fazla ortak kelime, duplicate
}
```

---

## 📝 SONRAKI ADIMLAR

### Seçenekler:

**A) Direkt Başla (Hızlı)**
- Settings kayıtlarını oluştur
- Layout JSON'u ekle
- Hemen test et

**B) Topic Generator'ı da Ekle (Orta)**
- Settings + Topic Generator sayfası
- Checkbox'lı sistem
- v1.5 olarak tanımla

**C) Detaylı Plan (Yavaş)**
- Service/Repository class'ları yaz
- Job/Queue yapısını hazırla
- Migration'ları oluştur

---

## ❓ SANA SORULAR

1. **Direkt Settings'e başlayalım mı?** Yoksa önce Topic Generator sayfası da mı olsun?
2. **Layout JSON'u kullanmak ister misin?** Yoksa fallback HTML yeterli mi?
3. **Hangi özellikler v1.0'da olsun?** (Şimdilik basit mi, yoksa tam özellikli mi?)
4. **"ŞİMDİ OLUŞTUR" butonu önemli mi?** Hemen ekleyelim mi?

**Hazırım! Onayını bekli yorum.** 🚀
