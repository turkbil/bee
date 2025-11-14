# 🤖 BLOG AI AYARLARI - SADECE GEREKLİLER

**Tarih**: 2025-11-14
**Revizyon**: v2 - Gereksiz ayarlar temizlendi
**Lokasyon**: `/admin/settingmanagement/values/18`

---

## ⚡ PRENSIP: SADE VE KULLANICI DOSTU

**❌ Müşteriye sorma:**
- Kelime sayısı (prompt'ta 2000-2500 kelime otomatik)
- SEO açık mı (zaten hep açık olmalı, sormaya gerek yok)
- AI provider (sistem AI kullanılır)
- Temperature (0.7 otomatik)
- Language (tenant'ın dili kullanılır)
- Retry count (otomatik 3)

**✅ Sadece gerçekten gerekli ayarlar:**
- Sistemi aç/kapat
- Günlük blog sayısı
- Manuel konu listesi
- Basit kontrol ayarları

---

## 📊 FİNAL AYAR LİSTESİ (14 AYAR)

### 1️⃣ TEMEL KONTROL (3 Ayar)

#### `blog_ai_enabled` (checkbox)
- **Label**: Blog AI Sistemi Aktif
- **Default**: `0` (Kapalı)
- **Açıklama**: Sistemi aç/kapat

#### `blog_ai_daily_count` (number)
- **Label**: Günlük Blog Sayısı
- **Default**: `10`
- **Min**: 1, **Max**: 50
- **Açıklama**: Her gün kaç blog yazılsın?

#### `blog_ai_auto_publish` (checkbox)
- **Label**: Otomatik Yayınlama
- **Default**: `1` (Açık)
- **Açıklama**: Blog yazılınca otomatik yayınlansın mı?

---

### 2️⃣ KONU KAYNAKLARI (2 Ayar)

#### `blog_ai_topic_source` (select)
- **Label**: Konu Kaynağı
- **Choices**:
  - `manual`: Manuel (Sadece aşağıdaki listeden)
  - `auto`: Otomatik (Ürün/Kategori analizi)
  - `mixed`: Karma (Önce manuel, sonra otomatik)
- **Default**: `mixed`

#### `blog_ai_manual_topics` (textarea)
- **Label**: Ana Konular (Manuel Liste)
- **Rows**: 15
- **Placeholder**:
```
transpalet
forklift
akülü istif makinesi
```
- **Default**: `null` (Boş)
- **Açıklama**: Her satıra bir ana konu. Sistem otomatik genişletir.

---

### 3️⃣ KONU GENİŞLETME (3 Ayar)

#### `blog_ai_topic_expand_enabled` (checkbox)
- **Label**: Konu Genişletme Aktif
- **Default**: `1` (Açık)
- **Açıklama**: "transpalet" → 10 farklı başlık üretir

#### `blog_ai_topic_expand_count` (number)
- **Label**: Her Konudan Kaç Başlık Üretilsin
- **Default**: `10`
- **Min**: 5, **Max**: 100

#### `blog_ai_duplicate_check` (checkbox)
- **Label**: Mevcut Bloglara Bak (Duplicate Engelle)
- **Default**: `1` (Açık)
- **Açıklama**: Aynı başlıklı blog varsa oluşturmasın

---

### 4️⃣ OTOMATİK KONU BULMA (3 Ayar)

#### `blog_ai_auto_source_products` (checkbox)
- **Label**: Ürünlerden Konu Bul
- **Default**: `1` (Açık)

#### `blog_ai_auto_source_categories` (checkbox)
- **Label**: Kategorilerden Konu Bul
- **Default**: `1` (Açık)

#### `blog_ai_auto_priority` (select)
- **Label**: Hangi Ürünler Önce İşlensin?
- **Choices**:
  - `most_viewed`: En çok görüntülenen
  - `newest`: En yeni
  - `no_blog`: Blogu olmayan
  - `mixed`: Karma
- **Default**: `most_viewed`

---

### 5️⃣ İÇERİK STİLİ (2 Ayar)

#### `blog_ai_style_rotation` (checkbox)
- **Label**: Stil Rotasyonu (Her Blogda Farklı)
- **Default**: `1` (Açık)
- **Açıklama**: Blog 1 → Profesyonel, Blog 2 → Samimi, Blog 3 → Uzman

#### `blog_ai_style_order` (select)
- **Label**: Stil Sırası
- **Choices**:
  - `professional_friendly_expert`: Profesyonel → Samimi → Uzman
  - `friendly_professional_expert`: Samimi → Profesyonel → Uzman
  - `expert_professional_friendly`: Uzman → Profesyonel → Samimi
  - `random`: Rastgele
- **Default**: `professional_friendly_expert`

---

### 6️⃣ SİSTEM OPTİMİZASYONU (1 Ayar)

#### `blog_ai_queue_enabled` (checkbox)
- **Label**: Kuyruk Sistemi Kullan (Performans)
- **Default**: `1` (Açık)
- **Açıklama**: Bloglar arka planda işlenir

---

## 🔧 PROMPT İÇİNDE OTOMATIK OLANLAR

**Bu ayarlar müşteriye sorulmaz, prompt'ta sabit:**

### SEO & İçerik
- **Kelime sayısı**: 2000-2500 kelime (otomatik)
- **Dil**: Tenant'ın dili (auto-detect)
- **SEO 2025**: Her zaman aktif (E-E-A-T, Core Web Vitals, Schema)

### AI Provider
- **Provider**: Sistem AI (mevcut AI modülü)
- **Temperature**: 0.7 (dengeli)
- **Retry**: 3 (hata durumunda)

### Zamanlama
- **Cron**: Her 2 saatte bir çalışır
- **Saatler**: Günlük blog sayısına göre otomatik dağıtılır
  - Örnek: 10 blog/gün → 2 saatte bir 1-2 blog

### Görsel
- **v2.0'da**: Otomatik görsel üretimi eklenecek
- **Şimdilik**: Manuel görsel ekleme veya varsayılan

---

## 🎨 LAYOUT JSON (Basitleştirilmiş)

```json
{
  "elements": [
    {
      "type": "section",
      "title": "Temel Kontrol",
      "subtitle": "Sistemi aç/kapat ve günlük sayıyı belirle",
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
      "subtitle": "Blog konularını buradan alacak",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_topic_source", "width": 12},
        {"type": "field", "setting_key": "blog_ai_manual_topics", "width": 12},
        {"type": "alert", "variant": "info", "content": "💡 Her satıra bir ana konu yaz. Sistem otomatik genişletir: 'transpalet' → 'transpalet nedir', 'elektrikli transpalet', vb.", "width": 12}
      ]
    },
    {
      "type": "section",
      "title": "Konu Genişletme",
      "subtitle": "Bir konudan onlarca başlık üret",
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
      "subtitle": "Manuel liste boşsa sistem otomatik bulur",
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
      "subtitle": "Her blogda farklı yazım stili",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_style_rotation", "width": 6},
        {"type": "field", "setting_key": "blog_ai_style_order", "width": 6}
      ]
    },
    {
      "type": "section",
      "title": "Sistem Optimizasyonu",
      "subtitle": "Performans ayarları",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_queue_enabled", "width": 12}
      ]
    }
  ]
}
```

---

## 📋 KARŞILAŞTIRMA

| Önceki | Yeni | Neden Kaldırıldı? |
|--------|------|-------------------|
| 22 ayar | 14 ayar | Gereksiz teknik detaylar |
| 9 kategori | 6 kategori | Sadeleştirme |
| Kelime sayısı ayarı | ❌ Yok | Prompt'ta otomatik 2000-2500 |
| SEO açık/kapalı | ❌ Yok | Her zaman açık olmalı |
| AI provider seçimi | ❌ Yok | Sistem AI kullanılır |
| AI temperature | ❌ Yok | 0.7 otomatik |
| Dil seçimi | ❌ Yok | Tenant dili kullanılır |
| Retry sayısı | ❌ Yok | 3 otomatik |
| Cron saatleri | ❌ Yok | Her 2 saat otomatik |
| Görsel v2 | ❌ Yok | v2.0'da eklenecek |

---

## ✅ SONUÇ

**14 basit ayar, tüm teknik detaylar arka planda:**

1. ✅ Sistemi aç/kapat
2. ✅ Günlük blog sayısı
3. ✅ Otomatik yayın
4. ✅ Konu kaynağı (manuel/oto/karma)
5. ✅ Manuel konu listesi (textarea)
6. ✅ Genişletme aktif/pasif
7. ✅ Kaç başlık üretilsin
8. ✅ Duplicate kontrol
9. ✅ Ürünlerden bul
10. ✅ Kategorilerden bul
11. ✅ Önceliklendirme
12. ✅ Stil rotasyonu
13. ✅ Stil sırası
14. ✅ Queue kullan

**Müşteri sadece bunları görür. Geri kalan herşey otomatik! 🎯**
