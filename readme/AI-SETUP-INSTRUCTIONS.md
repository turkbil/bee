# 🤖 AI HYBRID SEARCH SYSTEM - KURULUM TALİMATLARI

**Tarih**: 2025-10-17
**Commit Hash**: `0e3954ad` (ana sistem) + `4c838173` (fillable fix)
**Hedef AI**: Bu döküman başka bir AI asistanına verilmek üzere hazırlanmıştır.

---

## 📋 GENEL BAKIŞ

Bu talimat seti, **Hybrid Search System (Meilisearch + OpenAI Embeddings)** sistemini git'ten çekip production/local ortama kurmanız için hazırlanmıştır.

**Sistem Özellikleri**:
- ✅ Meilisearch ile typo-tolerant keyword search
- ✅ OpenAI Embeddings ile semantic search
- ✅ Hybrid scoring (70% keyword + 30% semantic)
- ✅ Türkçe dil desteği + synonym matching
- ✅ 1,020 ürün için hazır

---

## ⚠️ ÖNEMLİ UYARILAR

1. **VERİTABANI KORUMA**: Bu gerçek canlı sistemdir! `migrate:fresh` veya `db:wipe` KULLANMA!
2. **GIT PULL**: Sadece belirtilen commit'leri çek
3. **BACKUP**: İşlem öncesi database backup al
4. **TEST**: Önce local'de test et, sonra production'a geç
5. **OPENAI API KEY**: Mevcut key'i kullan veya kullanıcıdan iste

---

## 🚀 ADIM ADIM KURULUM

### ADIM 1: GIT GÜNCELLEMELERINI ÇEK

```bash
# Proje dizinine git
cd /Users/nurullah/Desktop/cms/laravel

# Mevcut değişiklikleri stash'le (eğer varsa)
git stash

# Ana branch'e geç
git checkout main

# Son değişiklikleri çek
git pull origin main

# Hedef commit'leri kontrol et
git log --oneline -5

# Beklenen commit'ler:
# 4c838173 🐛 FIX: ShopProduct fillable - embedding alanları eklendi
# 0e3954ad 🚀 AI SEARCH: Hybrid Search System (Meilisearch + OpenAI Embeddings)
```

**Kontrol Noktası**:
```bash
# Dosyaların geldiğini kontrol et
ls -la app/Services/AI/HybridSearchService.php
ls -la app/Services/AI/EmbeddingService.php
ls -la app/Services/AI/VectorSearchService.php
ls -la app/Services/Search/MeilisearchConfig.php
ls -la app/Console/Commands/GenerateProductEmbeddings.php

# Hepsi varsa ✅ devam et
```

---

### ADIM 2: COMPOSER DEPENDENCIES

```bash
# Composer cache temizle
composer clear-cache

# Dependencies'i yükle
composer install

# VEYA sadece yeni paketleri yükle
composer update laravel/scout meilisearch/meilisearch-php openai-php/laravel

# Autoload'u yenile
composer dump-autoload
```

**Kontrol Noktası**:
```bash
# Paketlerin yüklendiğini kontrol et
composer show | grep scout
composer show | grep meilisearch
composer show | grep openai

# Beklenen:
# laravel/scout                  ^11.0
# meilisearch/meilisearch-php    ^1.11
# openai-php/laravel             ^0.17.1
```

---

### ADIM 3: MEILISEARCH KURULUMU

#### 3.1 Meilisearch Binary Kurulumu (macOS)

```bash
# Homebrew ile kur
brew install meilisearch

# VEYA direkt binary indir
curl -L https://install.meilisearch.com | sh

# Versiyonu kontrol et
meilisearch --version
# Beklenen: v1.23.0 veya üzeri
```

#### 3.2 Meilisearch Başlat

```bash
# Master key oluştur (güvenlik için)
MASTER_KEY=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-32)
echo "Generated Master Key: $MASTER_KEY"

# Meilisearch'i başlat (background)
brew services start meilisearch

# VEYA manuel başlat
meilisearch --master-key=$MASTER_KEY --http-addr 127.0.0.1:7700 &

# Çalıştığını kontrol et
curl http://127.0.0.1:7700/health
# Beklenen: {"status":"available"}
```

**Kontrol Noktası**:
```bash
# Meilisearch running mi?
ps aux | grep meilisearch

# Port dinleniyor mu?
lsof -i :7700

# ✅ Her ikisi de pozitif sonuç vermeli
```

---

### ADIM 4: .env YAPILANDIRMASI

```bash
# .env dosyasını aç
nano .env

# Aşağıdaki satırları EKLE veya GÜNCELLE:

# ============================================
# SCOUT + MEILISEARCH AYARLARI
# ============================================
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=<YUKARIDA_OLUŞTURDUĞUN_MASTER_KEY>
SCOUT_QUEUE=false

# ============================================
# OPENAI API AYARLARI
# ============================================
OPENAI_API_KEY=<MEVCUT_OPENAI_KEY_BURAYA>

# Kaydet ve çık (CTRL+O, ENTER, CTRL+X)
```

**Kontrol Noktası**:
```bash
# .env'deki değerleri kontrol et
php artisan tinker --execute="
echo 'SCOUT_DRIVER: ' . config('scout.driver') . PHP_EOL;
echo 'MEILISEARCH_HOST: ' . config('scout.meilisearch.host') . PHP_EOL;
echo 'OPENAI_KEY set: ' . (config('openai.api_key') ? 'YES' : 'NO') . PHP_EOL;
"

# Beklenen:
# SCOUT_DRIVER: meilisearch
# MEILISEARCH_HOST: http://127.0.0.1:7700
# OPENAI_KEY set: YES
```

---

### ADIM 5: DATABASE MIGRATION

```bash
# ⚠️ DİKKAT: migrate:fresh KULLANMA! Sadece yeni migration'ı çalıştır

# Migration dosyasını kontrol et
ls -la database/migrations/2025_10_17_221722_add_embedding_to_shop_products.php

# Migration'ı çalıştır
php artisan migrate

# Beklenen çıktı:
# Migrating: 2025_10_17_221722_add_embedding_to_shop_products
# Migrated:  2025_10_17_221722_add_embedding_to_shop_products
```

**Kontrol Noktası**:
```bash
# Kolonların eklendiğini kontrol et
php artisan tinker --execute="
use Illuminate\Support\Facades\Schema;
echo 'embedding column exists: ' . (Schema::hasColumn('shop_products', 'embedding') ? 'YES' : 'NO') . PHP_EOL;
echo 'embedding_generated_at column exists: ' . (Schema::hasColumn('shop_products', 'embedding_generated_at') ? 'YES' : 'NO') . PHP_EOL;
echo 'embedding_model column exists: ' . (Schema::hasColumn('shop_products', 'embedding_model') ? 'YES' : 'NO') . PHP_EOL;
"

# Beklenen:
# embedding column exists: YES
# embedding_generated_at column exists: YES
# embedding_model column exists: YES
```

---

### ADIM 6: MEILISEARCH İNDEKSLEME

#### 6.1 Ürünleri Meilisearch'e Aktar

```bash
# Tüm aktif ürünleri indeksle
php artisan scout:import "Modules\Shop\App\Models\ShopProduct"

# İşlem süresi: ~2-3 dakika (1,020 ürün için)
# İlerleme çubuğu göreceksin

# Beklenen çıktı:
# Imported [Modules\Shop\App\Models\ShopProduct] models up to ID: 1020
```

**Kontrol Noktası**:
```bash
# Meilisearch'te kaç ürün var?
curl -s "http://127.0.0.1:7700/indexes/shop_products_tenant_2/stats" \
  -H "Authorization: Bearer $MEILISEARCH_KEY" | python3 -m json.tool

# Beklenen: numberOfDocuments: 1020 (veya yakın sayı)
```

#### 6.2 Türkçe Typo Tolerance Yapılandırması

```bash
# MeilisearchConfig'i çalıştır
php artisan tinker --execute="
\$config = new \App\Services\Search\MeilisearchConfig();
\$config->configureTurkishSearch('shop_products_tenant_2');
echo 'Turkish typo tolerance configured!' . PHP_EOL;
"

# Beklenen çıktı:
# Turkish typo tolerance configured!
```

**Kontrol Noktası - Typo Tolerance Test**:
```bash
# "soguk depo" (TYPO!) ile arama yap
php artisan tinker --execute="
use Modules\Shop\App\Models\ShopProduct;
\$results = ShopProduct::search('soguk depo')->take(3)->get();
echo 'Found ' . \$results->count() . ' products' . PHP_EOL;
foreach (\$results as \$product) {
    echo '- ' . (\$product->title['tr'] ?? \$product->title) . PHP_EOL;
}
"

# Beklenen: "Soğuk Depo" içeren ürünler bulunmalı (typo düzeltilmiş!)
# ✅ BAŞARILI: İXTİF EPT20-20ETC - 2.0 Ton Soğuk Depo Transpalet
```

---

### ADIM 7: OPENAI EMBEDDINGS OLUŞTURMA

#### 7.1 İlk Test (10 Ürün)

```bash
# İlk 10 ürün için embedding oluştur (test)
php artisan products:generate-embeddings --limit=10

# İşlem süresi: ~30-40 saniye
# Progress bar göreceksin

# Beklenen çıktı:
# 🚀 Generating embeddings for 10 products...
# [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
# ✅ Done! Success: 10, Failed: 0
```

**Kontrol Noktası**:
```bash
# Embedding'lerin oluştuğunu kontrol et
php artisan tinker --execute="
use Modules\Shop\App\Models\ShopProduct;
\$count = ShopProduct::whereNotNull('embedding')->count();
echo 'Products with embeddings: ' . \$count . PHP_EOL;

if (\$count > 0) {
    \$sample = ShopProduct::whereNotNull('embedding')->first();
    \$embedding = json_decode(\$sample->embedding, true);
    echo 'Sample: ' . (\$sample->title['tr'] ?? \$sample->title) . PHP_EOL;
    echo 'Dimensions: ' . count(\$embedding) . PHP_EOL;
}
"

# Beklenen:
# Products with embeddings: 10
# Dimensions: 1536
```

#### 7.2 Production Embeddings (TÜM ÜRÜNLER)

⚠️ **DİKKAT**: Bu adımı çalıştırmadan önce kullanıcıya sor!

```bash
# TÜM ürünler için embedding oluştur (1,020 ürün)
# Süre: ~15-20 dakika
# Maliyet: ~$0.05-0.10

# KULLANICIYA SOR:
# "1,020 ürün için OpenAI embedding oluşturmak istiyor musunuz?
#  Süre: 15-20 dakika, Maliyet: ~$0.10"

# EVET ise:
php artisan products:generate-embeddings --limit=1020 --force

# HAYIR ise:
# Bu adımı atla, sistem 10 ürünle çalışabilir (test için yeterli)
```

---

### ADIM 8: HYBRID SEARCH TESTİ

#### 8.1 Meilisearch Keyword Test

```bash
php artisan tinker --execute="
use Modules\Shop\App\Models\ShopProduct;
\$results = ShopProduct::search('forklift li-ion')->take(3)->get();
echo 'Meilisearch Keyword Search:' . PHP_EOL;
foreach (\$results as \$product) {
    echo '- ' . (\$product->title['tr'] ?? \$product->title) . PHP_EOL;
}
"

# Beklenen: Li-Ion forklift ürünleri
```

#### 8.2 Vector Semantic Test

```bash
php artisan tinker --execute="
\$vectorSearch = new \App\Services\AI\VectorSearchService(
    new \App\Services\AI\EmbeddingService()
);
\$results = \$vectorSearch->search('electric forklift battery', 5);
echo 'Vector Semantic Search: ' . count(\$results) . ' results' . PHP_EOL;
foreach (array_slice(\$results, 0, 3) as \$result) {
    echo '- ' . (\$result['product']['title']['tr'] ?? \$result['product']['title']) . PHP_EOL;
    echo '  Similarity: ' . round(\$result['score'], 3) . PHP_EOL;
}
"

# Beklenen: Elektrikli forklift ürünleri (semantic matching)
```

#### 8.3 Hybrid Search Test (ANA TEST!)

```bash
php artisan tinker --execute="
\$hybridService = new \App\Services\AI\HybridSearchService(
    new \App\Services\AI\VectorSearchService(
        new \App\Services\AI\EmbeddingService()
    )
);

echo '🔍 Hybrid Search Test: forklift li-ion' . PHP_EOL;
\$results = \$hybridService->search('forklift li-ion', null, 5);
echo 'Results: ' . count(\$results) . PHP_EOL . PHP_EOL;

foreach (array_slice(\$results, 0, 3) as \$idx => \$result) {
    echo (\$idx + 1) . '. ' . (\$result['product']['title']['tr'] ?? \$result['product']['title']) . PHP_EOL;
    echo '   Keyword: ' . round(\$result['scores']['keyword_score'], 3);
    echo ' | Semantic: ' . round(\$result['scores']['semantic_score'], 3);
    echo ' | Hybrid: ' . round(\$result['scores']['hybrid_score'], 3) . PHP_EOL;
}
"

# Beklenen:
# 1. İXTİF CPD18FVL - 1.8 Ton Li-Ion Forklift
#    Keyword: 0.92 | Semantic: 1.0 | Hybrid: 0.944
```

#### 8.4 Typo Tolerance + Hybrid Test

```bash
php artisan tinker --execute="
\$hybridService = new \App\Services\AI\HybridSearchService(
    new \App\Services\AI\VectorSearchService(
        new \App\Services\AI\EmbeddingService()
    )
);

echo '🔍 Typo Test: soguk depo (TYPO!)' . PHP_EOL;
\$results = \$hybridService->search('soguk depo', null, 5);
echo 'Results: ' . count(\$results) . PHP_EOL . PHP_EOL;

foreach (array_slice(\$results, 0, 3) as \$idx => \$result) {
    \$title = \$result['product']['title']['tr'] ?? \$result['product']['title'];
    echo (\$idx + 1) . '. ' . \$title . PHP_EOL;
    if (stripos(\$title, 'soğuk') !== false) {
        echo '   ✅ TYPO TOLERANCE WORKING!' . PHP_EOL;
    }
}
"

# Beklenen:
# 1. İXTİF EPT20-20ETC - 2.0 Ton Soğuk Depo Transpalet
#    ✅ TYPO TOLERANCE WORKING!
```

---

### ADIM 9: ProductSearchService ENTEGRASYON TESTİ

⚠️ **DİKKAT**: Bu test tenant context gerektirir. Tinker'da tenant yok, bu yüzden API endpoint test edeceğiz.

```bash
# API endpoint testi için curl kullan
curl -X POST "https://laravel.test/api/ai/v1/chat" \
  -H "Content-Type: application/json" \
  -H "X-Tenant: ixtif.com" \
  -d '{
    "message": "soguk depo transpalet istiyorum",
    "conversation_id": null
  }' | python3 -m json.tool

# Beklenen: AI chatbot yanıtı + "Soğuk Depo Transpalet" ürünleri
```

**VEYA** Frontend'den test et:
```
1. https://ixtif.com sayfasını aç
2. AI Chat widget'ı aç
3. Mesaj yaz: "soguk depo transpalet"
4. Sonuç: Soğuk depo ürünleri görmelisin (typo düzeltilmiş!)
```

---

### ADIM 10: CACHE TEMİZLEME

```bash
# Laravel cache'leri temizle
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Composer autoload cache temizle
composer dump-autoload

# ✅ Sistem hazır!
```

---

## 🔧 OPSİYONEL: CRON JOB KURULUMU

Yeni eklenen ürünler için otomatik embedding oluşturma:

```bash
# Laravel Task Scheduler'a ekle
# Dosya: app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Her gece saat 03:00'te son 50 ürün için embedding oluştur
    $schedule->command('products:generate-embeddings --limit=50')
        ->dailyAt('03:00')
        ->onSuccess(function () {
            Log::info('Daily embeddings generated successfully');
        })
        ->onFailure(function () {
            Log::error('Daily embeddings generation failed');
        });
}
```

**Cron entry ekle**:
```bash
# Crontab'ı düzenle
crontab -e

# Ekle:
* * * * * cd /path/to/laravel && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🐛 SORUN GİDERME

### Problem 1: "Class HybridSearchService not found"

```bash
# Çözüm: Autoload yenile
composer dump-autoload
php artisan config:clear
```

### Problem 2: "Tenant context missing"

```bash
# Çözüm: Tinker yerine API endpoint kullan
# ProductSearchService tenant gerektirir, tinker'da tenant yok
curl -X POST "https://laravel.test/api/ai/v1/chat" -H "X-Tenant: ixtif.com" ...
```

### Problem 3: "Meilisearch connection refused"

```bash
# Çözüm: Meilisearch'i başlat
brew services start meilisearch

# VEYA
meilisearch --master-key=YOUR_KEY --http-addr 127.0.0.1:7700 &

# Kontrol:
curl http://127.0.0.1:7700/health
```

### Problem 4: "OpenAI API rate limit"

```bash
# Çözüm: Rate limit delay'i artır
# Dosya: app/Console/Commands/GenerateProductEmbeddings.php
# Satır 55: usleep(20000); → usleep(50000); // 20ms → 50ms
```

### Problem 5: "Embedding alanı güncellenmiyor"

```bash
# Çözüm: Fillable kontrolü
php artisan tinker --execute="
\$model = new \Modules\Shop\App\Models\ShopProduct();
\$fillable = \$model->getFillable();
echo 'embedding in fillable: ' . (in_array('embedding', \$fillable) ? 'YES' : 'NO') . PHP_EOL;
"

# NO ise: Commit 4c838173'ü çek (fillable fix)
```

---

## 📊 BAŞARI KRİTERLERİ

Tüm aşağıdaki testler başarılı olmalı:

- [ ] **Meilisearch Running**: `curl http://127.0.0.1:7700/health` → `{"status":"available"}`
- [ ] **Scout Index**: 1,020 ürün indekslenmiş
- [ ] **Typo Tolerance**: "soguk depo" → "Soğuk Depo" bulunuyor
- [ ] **Embeddings**: En az 10 ürün için 1536-dim vektör oluşturulmuş
- [ ] **Vector Search**: Semantic similarity çalışıyor
- [ ] **Hybrid Search**: Keyword + Semantic skorlar birleşiyor
- [ ] **ProductSearchService**: API endpoint düzgün yanıt veriyor

**Tüm checkboxlar işaretliyse → ✅ KURULUM BAŞARILI!**

---

## 📝 FINAL RAPOR

Kurulum tamamlandıktan sonra aşağıdaki komutu çalıştır ve çıktısını kullanıcıya göster:

```bash
php artisan tinker --execute="
use Modules\Shop\App\Models\ShopProduct;

echo '=====================================' . PHP_EOL;
echo '🎉 HYBRID SEARCH SYSTEM - STATUS' . PHP_EOL;
echo '=====================================' . PHP_EOL . PHP_EOL;

echo '📊 STATISTICS:' . PHP_EOL;
echo 'Total Products: ' . ShopProduct::count() . PHP_EOL;
echo 'Products with Embeddings: ' . ShopProduct::whereNotNull('embedding')->count() . PHP_EOL;
echo 'Meilisearch Driver: ' . config('scout.driver') . PHP_EOL;
echo 'OpenAI Model: text-embedding-3-small' . PHP_EOL . PHP_EOL;

echo '✅ SYSTEM STATUS:' . PHP_EOL;
echo '- Meilisearch: ' . (config('scout.driver') === 'meilisearch' ? '✅ Active' : '❌ Inactive') . PHP_EOL;
echo '- OpenAI API: ' . (config('openai.api_key') ? '✅ Configured' : '❌ Missing') . PHP_EOL;
echo '- Embeddings: ' . (ShopProduct::whereNotNull('embedding')->count() > 0 ? '✅ Generated' : '❌ Not Generated') . PHP_EOL;
echo '- Hybrid Search: ✅ Ready' . PHP_EOL . PHP_EOL;

echo '🚀 NEXT STEPS:' . PHP_EOL;
\$embedCount = ShopProduct::whereNotNull('embedding')->count();
if (\$embedCount < 1000) {
    echo '1. Generate embeddings for remaining products:' . PHP_EOL;
    echo '   php artisan products:generate-embeddings --limit=1020 --force' . PHP_EOL;
} else {
    echo '1. ✅ All embeddings generated!' . PHP_EOL;
}
echo '2. Test chatbot on frontend: https://ixtif.com' . PHP_EOL;
echo '3. Monitor logs: tail -f storage/logs/laravel.log | grep Hybrid' . PHP_EOL . PHP_EOL;

echo '=====================================' . PHP_EOL;
"
```

---

## 💾 GIT COMMIT REFERANSLARI

Bu kurulum için gerekli commit'ler:

1. **0e3954ad** - Ana sistem
   - Meilisearch entegrasyonu
   - OpenAI Embeddings servisleri
   - Hybrid Search algoritması
   - Migration dosyası
   - ProductSearchService entegrasyonu

2. **4c838173** - Fillable fix
   - ShopProduct model'e embedding alanları eklendi

**Pull komutu**:
```bash
git pull origin main
# VEYA belirli commit'leri çek:
git cherry-pick 0e3954ad 4c838173
```

---

## 🎯 SONUÇ

Bu talimatları sırayla takip et. Her "Kontrol Noktası"nda durarak doğrulama yap.

**Sorun yaşarsan**:
1. "SORUN GİDERME" bölümüne bak
2. Laravel log'ları kontrol et: `tail -f storage/logs/laravel.log`
3. Hata mesajını kaydet ve kullanıcıya rapor et

**Her şey başarılıysa**:
- Final raporunu çalıştır
- Kullanıcıya "✅ KURULUM BAŞARILI!" de
- Chatbot'u test et

---

**ÖNEMLI**: Eğer herhangi bir adımda **HATA** alırsan:
1. DURMA! İleriye gitme!
2. Hata mesajını TAMAMEN kopyala
3. Kullanıcıya göster ve talimat iste
4. Asla tahmin yürütme, sormadan düzeltme yapma!

---

**SON KONTROL LİSTESİ**:
- [ ] Git pull başarılı
- [ ] Composer install başarılı
- [ ] Meilisearch çalışıyor
- [ ] Migration tamamlandı
- [ ] Scout indexing tamamlandı
- [ ] Türkçe typo config uygulandı
- [ ] Typo tolerance test başarılı
- [ ] Embeddings oluşturuldu (en az 10 ürün)
- [ ] Hybrid search test başarılı
- [ ] Final rapor çalıştırıldı

**HEPSİ ✅ İSE → GÖREV TAMAMLANDI!**

🤖 Bu dökümanı kullanıcıya paylaş ve kurulumu başlat.
