# 🧠 AI Dinamik Bilgi Bankası (FAQ/Q&A) Sistemi

**Tarih:** 2025-10-13
**Durum:** ✅ TAMAMLANDI

---

## 📋 Genel Bakış

Settings tablosuna **dinamik FAQ/Q&A sistemi** eklendi. Artık admin panelinden:

✅ Yeni soru-cevap çiftleri eklenebilir
✅ Mevcut sorular düzenlenebilir/silinebilir
✅ Kategorilere ayrılabilir (Genel, Teknik, Satış, Kiralama, vb.)
✅ Aktif/pasif yapılabilir (is_active)
✅ Sıralama düzenlenebilir (sort_order)

**AI Otomatik Öğrenir:**
- Settings'e eklenen her soru-cevap çifti **otomatik olarak AI context'ine dahil edilir**
- AI, benzer sorular sorulduğunda bu bilgileri kullanır
- Yeni bilgi eklenince tekrar deploy/restart gerekmez - **anında aktif olur**

---

## 🏗️ Mimari

### 1. Database Yapısı

**Settings Tablosu:**
```
key: ai_knowledge_base
type: json
default_value: [
  {
    "id": 1,
    "category": "Genel",
    "question": "...",
    "answer": "...",
    "info": "...",
    "is_active": true,
    "sort_order": 1
  },
  ...
]
```

**JSON Schema:**
```json
{
  "id": "number - Unique identifier",
  "category": "string - Kategori (Genel, Teknik, Satış, etc.)",
  "question": "string - Soru metni",
  "answer": "string - Yanıt metni",
  "info": "string - Dahili not (AI'a gösterilmez, admin için açıklama)",
  "is_active": "boolean - Aktif/pasif durum",
  "sort_order": "number - Sıralama"
}
```

---

## 🛠️ Kod Detayları

### AISettingsHelper Metodları

#### 1. getKnowledgeBase()
```php
AISettingsHelper::getKnowledgeBase();

// Döner:
[
  [
    'id' => 1,
    'category' => 'Genel',
    'question' => 'Forklift alırken nelere dikkat etmeliyim?',
    'answer' => 'Forklift alırken şu faktörlere dikkat etmelisiniz...',
    'info' => 'Bu bilgi müşterilere forklift seçiminde yardımcı olmak için kullanılır.',
    'is_active' => true,
    'sort_order' => 1,
  ],
  // ... daha fazla item
]
```

**Özellikler:**
- ✅ Sadece `is_active = true` olanları döner
- ✅ `sort_order`'a göre otomatik sıralar
- ✅ JSON parse hatalarını handle eder

#### 2. getKnowledgeBaseByCategory()
```php
AISettingsHelper::getKnowledgeBaseByCategory();

// Döner:
[
  'Genel' => [
    ['id' => 1, 'question' => '...', 'answer' => '...'],
    ['id' => 2, 'question' => '...', 'answer' => '...'],
  ],
  'Teknik' => [
    ['id' => 3, 'question' => '...', 'answer' => '...'],
  ],
  'Satış' => [
    ['id' => 4, 'question' => '...', 'answer' => '...'],
  ],
]
```

#### 3. buildKnowledgeBasePrompt()
```php
AISettingsHelper::buildKnowledgeBasePrompt();

// Döner:
"
=== BİLGİ BANKASI (SIK SORULAN SORULAR) ===
Aşağıdaki sorular sana öğretildi. Müşteriler benzer sorular sorduğunda bu bilgileri kullan:

**SORU #1 - [Genel]**: Forklift alırken nelere dikkat etmeliyim?
**YANIT**: Forklift alırken şu faktörlere dikkat etmelisiniz: 1) Kaldırma kapasitesi...

**SORU #2 - [Teknik]**: Elektrikli forklift mi yoksa dizel forklift mi almalıyım?
**YANIT**: Kapalı alanlarda (depo, fabrika) elektrikli forklift tercih edilir...

⚠️ ÖNEMLİ:
- Benzer sorular için yukarıdaki bilgileri kullan
- Listelenmeyen bir soru gelirse 'Bu konuda detaylı bilgim yok' de
- Yanıtları kendi kelimelerinle yeniden ifade edebilirsin (kopyala-yapıştır yapma)
"
```

#### 4. findKnowledgeItem()
```php
// ID ile bulma
AISettingsHelper::findKnowledgeItem(3);

// Soru metni ile bulma (partial match)
AISettingsHelper::findKnowledgeItem('forklift bakım');

// Döner:
[
  'id' => 3,
  'category' => 'Bakım',
  'question' => 'Forklift bakımı ne kadar sürer?',
  'answer' => 'Rutin bakım genellikle 250-500 çalışma saatinde bir yapılır...',
  'is_active' => true,
]
```

---

## 📚 Örnek Senaryolar

### Senaryo 1: Yeni Bilgi Ekleme

**Admin yapar:**
1. Settings > Yapay Zeka > AI Bilgi Bankası
2. JSON'a yeni item ekler:
```json
{
  "id": 6,
  "category": "Fiyat",
  "question": "Forklift fiyatları ne kadar?",
  "answer": "Forklift fiyatları kapasiteye ve özelliğe göre değişir. Elektrikli forklift 150.000-500.000 TL, Dizel forklift 200.000-800.000 TL arası değişir. Tam fiyat bilgisi için lütfen bizimle iletişime geçin.",
  "info": "Fiyat hassas konu, genel aralık ver ve iletişime yönlendir",
  "is_active": true,
  "sort_order": 6
}
```
3. Kaydet

**AI otomatik öğrenir:**
- Ayar kaydedilir kaydedilmez, sonraki chat'te aktif olur
- Kullanıcı "Forklift fiyatları ne kadar?" sorarsa, AI yukarıdaki cevabı kullanır
- Tekrar deploy/restart gerekmez

### Senaryo 2: Bilgi Güncelleme

**Admin yapar:**
```json
{
  "id": 3,
  "category": "Bakım",
  "question": "Forklift bakımı ne kadar sürer?",
  "answer": "GÜNCEL: Rutin bakım 300-600 çalışma saatinde bir yapılır. Süre 3-5 saat.",
  "is_active": true,
  "sort_order": 3
}
```

**AI otomatik günceller:**
- Sonraki chat'te güncel bilgiyi kullanır
- Eski yanıt artık verilmez

### Senaryo 3: Bilgi Devre Dışı Bırakma

**Admin yapar:**
```json
{
  "id": 4,
  "category": "Satış",
  "question": "İkinci el forklift alsam mı yoksa sıfır mı?",
  "answer": "...",
  "is_active": false,  // ❌ Devre dışı
  "sort_order": 4
}
```

**AI artık bu bilgiyi görmez:**
- Bu soru sorulursa "Bu konuda bilgim yok" der
- Context'e dahil edilmez

---

## 🔄 Otomatik Entegrasyon

### ModuleContextOrchestrator
```php
public function buildSystemPrompt(): string
{
    $prompts = [];

    // Personality prompt
    $prompts[] = AISettingsHelper::buildPersonalityPrompt();
    $prompts[] = "";

    // Contact info
    $contactPrompt = AISettingsHelper::buildContactPrompt();
    if (!empty($contactPrompt)) {
        $prompts[] = $contactPrompt;
        $prompts[] = "";
    }

    // ⭐ Knowledge Base (otomatik eklendi)
    $knowledgePrompt = AISettingsHelper::buildKnowledgeBasePrompt();
    if (!empty($knowledgePrompt)) {
        $prompts[] = $knowledgePrompt;
        $prompts[] = "";
    }

    return implode("\n", $prompts);
}
```

**Sonuç:**
- Her AI request'te `buildSystemPrompt()` çağrılır
- Knowledge base otomatik olarak system prompt'a eklenir
- Settings'ten canlı olarak okunur (cache yok)

---

## 📊 Varsayılan Bilgiler (Seeder)

Aşağıdaki 5 örnek soru-cevap varsayılan olarak eklenir:

| ID | Kategori | Soru | Yanıt Özeti |
|----|----------|------|-------------|
| 1 | Genel | Forklift alırken nelere dikkat etmeliyim? | Kapasite, ortam, yükseklik, marka, servis ağı |
| 2 | Teknik | Elektrikli forklift mi dizel mi? | Kapalı alan: elektrikli, açık alan: dizel |
| 3 | Bakım | Forklift bakımı ne kadar sürer? | 250-500 saat arası, 2-4 saat sürer |
| 4 | Satış | İkinci el mi sıfır mı? | Bütçe kısıtlıysa ikinci el, yoğun kullanımsa sıfır |
| 5 | Kiralama | Forklift kiralamak mantıklı mı? | Kısa projeler için evet, 3+ yıl için satın al |

---

## 🧪 Test Adımları

### 1. Seeder Kontrolü
```bash
# Seeder'ı çalıştır
php artisan db:seed --class="Modules\SettingManagement\Database\Seeders\AISettingsSeeder"

# Settings tablosuna bak
SELECT * FROM settings WHERE key = 'ai_knowledge_base';
```

### 2. Helper Testi
```php
// Tinker ile test
php artisan tinker

>>> \App\Helpers\AISettingsHelper::getKnowledgeBase();
// 5 item döner

>>> \App\Helpers\AISettingsHelper::getKnowledgeBaseByCategory();
// Kategorilere göre gruplu döner

>>> \App\Helpers\AISettingsHelper::buildKnowledgeBasePrompt();
// Prompt string döner
```

### 3. AI Chat Testi
1. Shop product detail sayfasını aç
2. Inline widget'ta soru sor: "Forklift alırken nelere dikkat etmeliyim?"
3. AI yanıtında bilgi bankasındaki cevabı kullanmalı

### 4. Canlı Test Senaryoları

**Test 1: Kayıtlı soru**
```
User: "Elektrikli forklift mi alsam dizel mi?"
AI: "Kapalı alanlarda çalışacaksanız elektrikli forklift tercih edilir çünkü egzoz gazı çıkarmaz..."
```

**Test 2: Kayıtlı olmayan soru**
```
User: "Forklift lastiklerini nereden alabilirim?"
AI: "Bu konuda detaylı bilgim yok. Daha fazla bilgi için 0216 755 3 555 numaramızdan bize ulaşabilirsiniz."
```

**Test 3: Benzer soru (semantic match)**
```
User: "Bakım kaç ayda bir yapılıyor?"
AI: "Rutin bakım genellikle 250-500 çalışma saatinde bir yapılır (yaklaşık 3-6 ayda bir)..."
```

---

## ⚙️ Admin UI için Öneriler (Gelecek)

Şu an JSON olarak saklanıyor, gelecekte şunlar eklenebilir:

### FAQ Manager Component
```blade
{{-- resources/views/admin/settings/components/faq-manager.blade.php --}}

<div x-data="faqManager()">
    <div class="mb-4">
        <button @click="addNew()" class="btn btn-primary">
            <i class="fa fa-plus"></i> Yeni Soru Ekle
        </button>
    </div>

    <template x-for="(item, index) in items" :key="item.id">
        <div class="card mb-3">
            <div class="card-body">
                <input type="text" x-model="item.question" placeholder="Soru" />
                <textarea x-model="item.answer" placeholder="Yanıt"></textarea>
                <select x-model="item.category">
                    <option value="Genel">Genel</option>
                    <option value="Teknik">Teknik</option>
                    <option value="Satış">Satış</option>
                    <option value="Kiralama">Kiralama</option>
                    <option value="Bakım">Bakım</option>
                </select>
                <button @click="remove(index)">Sil</button>
            </div>
        </div>
    </template>
</div>
```

**Özellikler:**
- ✅ Drag & drop ile sıralama
- ✅ Kategori renklendirme
- ✅ Preview (AI'ın göreceği prompt'u göster)
- ✅ Bulk import/export (CSV, Excel)
- ✅ Arama ve filtreleme

---

## 💡 Gelişmiş Kullanım

### Kategoriye Göre Farklı Prompt
```php
public static function buildCategoryPrompt(string $category): string
{
    $items = self::getKnowledgeBaseByCategory()[$category] ?? [];

    if (empty($items)) {
        return '';
    }

    $prompt = ["=== {$category} BİLGİLERİ ==="];
    // ...
}
```

### Vector Search Entegrasyonu (Gelecek)
```php
// Semantic search ile en uygun FAQ'ı bul
public static function findSimilarQuestion(string $userQuestion): ?array
{
    // OpenAI Embeddings API kullan
    // Cosine similarity hesapla
    // En yakın soruyu döndür
}
```

### Analytics
```php
// Hangi sorular en çok soruluyor?
public static function getPopularQuestions(): array
{
    // ai_messages tablosunu analiz et
    // FAQ ID'lerini match et
    // Top 10 döndür
}
```

---

## 🎯 Avantajlar

✅ **Dinamik:** Admin panelinden anında güncelleme
✅ **Kolay Yönetim:** JSON format, kolayca düzenlenebilir
✅ **Kategorize:** Konulara göre gruplandırma
✅ **Filtrelenebilir:** Aktif/pasif yapma
✅ **Sıralanabilir:** Sort order ile öncelik
✅ **Merkezi:** Tüm bilgiler tek yerde
✅ **Tenant-specific:** Her tenant kendi FAQ'larını yönetir
✅ **Zero-downtime:** Güncelleme için restart gerekmez

---

## 📝 Özet

**Eklenen Dosyalar:**
- Hiç! (Mevcut dosyalar güncellendi)

**Güncellenen Dosyalar:**
1. `AISettingsSeeder.php` - ai_knowledge_base ayarı eklendi (5 örnek FAQ)
2. `AISettingsHelper.php` - 4 yeni metod eklendi
3. `ModuleContextOrchestrator.php` - Knowledge base prompt entegrasyonu

**Toplam Satır:**
- Seeder: +60 satır
- Helper: +100 satır
- Orchestrator: +8 satır
- **Toplam: ~170 satır**

**Test Durumu:**
✅ Seeder çalıştırıldı
✅ Settings'e eklendi
✅ Helper metodları çalışıyor
✅ System prompt'a entegre

**Production Ready:** ✅ Evet

---

**Generated with:** Claude Code
**Date:** 2025-10-13
**Version:** 1.0.0
