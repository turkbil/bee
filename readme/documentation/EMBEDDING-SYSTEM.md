# 🤖 Otomatik Embedding Güncelleme Sistemi

Bu döküman, ürün embedding'lerinin otomatik olarak oluşturulması ve güncellenmesi için kurulan sistemin detaylarını içerir.

## 📋 İçindekiler
- [Genel Bakış](#genel-bakış)
- [Kurulum](#kurulum)
- [Supervisor Yönetimi](#supervisor-yönetimi)
- [Kullanım](#kullanım)
- [Sorun Giderme](#sorun-giderme)

---

## 🎯 Genel Bakış

### Sistem Bileşenleri

1. **Supervisor**: Queue worker'ları sürekli çalıştırır (auto-restart)
2. **Laravel Queue Workers**: Redis queue üzerinden job'ları işler
3. **Model Event Listeners**: Ürün güncellemelerini dinler
4. **GenerateProductEmbedding Job**: Embedding oluşturur ve kaydeder
5. **EmbeddingService**: OpenAI API ile embedding generate eder

### Nasıl Çalışır?

```
Ürün Güncelleme → Model Event → Job Dispatch (5s delay) → Queue Worker → EmbeddingService → OpenAI API → Database Update
```

---

## 🚀 Kurulum

### 1. Gereksinimler

- ✅ Supervisor kurulu olmalı
- ✅ Redis çalışıyor olmalı
- ✅ OpenAI API key tanımlı olmalı (`.env` dosyasında)
- ✅ `AI_ENABLED=true` olmalı

### 2. Migration

Embedding kolonları eklemek için migration'ı çalıştırın:

```bash
# Tenant database'lerde
php artisan tenants:migrate

# Migration dosyası: database/migrations/tenant/2025_10_17_221722_add_embedding_to_shop_products.php
```

Migration şu kolonları ekler:
- `embedding` (JSON - 1536 boyutlu vektör)
- `embedding_generated_at` (timestamp)
- `embedding_model` (string - varsayılan: text-embedding-3-small)

### 3. Supervisor Kurulumu

**Supervisor'ı yükleyin:**
```bash
yum install -y supervisor
```

**Config dosyasını oluşturun:**
```bash
cat > /etc/supervisord.d/laravel-worker.ini << 'EOF'
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/vhosts/tuufi.com/httpdocs/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --timeout=120
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=root
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/vhosts/tuufi.com/httpdocs/storage/logs/worker.log
stopwaitsecs=3600
EOF
```

**Supervisor'ı başlatın:**
```bash
systemctl enable supervisord
systemctl start supervisord
supervisorctl reread
supervisorctl update
```

### 4. İlk Embedding Generation

Mevcut tüm ürünler için embedding oluşturun:

```bash
# Tenant 2 için (örnek: 1020 ürün)
php artisan products:generate-embeddings --tenant=2 --limit=1020 --force

# Tenant 3 için
php artisan products:generate-embeddings --tenant=3 --limit=1020 --force

# Progress takibi için (başka bir terminalde):
tail -f /tmp/tenant2_embeddings.log
```

---

## 🔧 Supervisor Yönetimi

### Komutlar

**Status kontrol:**
```bash
supervisorctl status
```

**Worker'ları restart et:**
```bash
supervisorctl restart laravel-worker:*
```

**Worker'ları durdur:**
```bash
supervisorctl stop laravel-worker:*
```

**Worker'ları başlat:**
```bash
supervisorctl start laravel-worker:*
```

**Config dosyasını reload et:**
```bash
supervisorctl reread
supervisorctl update
```

**Logları kontrol et:**
```bash
tail -f /var/www/vhosts/tuufi.com/httpdocs/storage/logs/worker.log
```

### Config Değiştirme

Config dosyasını düzenledikten sonra:

```bash
vi /etc/supervisord.d/laravel-worker.ini
supervisorctl reread
supervisorctl update
supervisorctl restart laravel-worker:*
```

---

## 💻 Kullanım

### Otomatik Embedding Generation

Model event listener'ları otomatik olarak çalışır. Aşağıdaki alanlar güncellendiğinde embedding yenilenir:

- `title`
- `short_description`
- `body`
- `features`
- `technical_specs`
- `use_cases`
- `competitive_advantages`
- `highlighted_features`

**Örnek:**
```php
use Modules\Shop\App\Models\ShopProduct;

$product = ShopProduct::find(1);
$product->title = ['tr' => 'Yeni Başlık'];
$product->save();

// 5 saniye sonra otomatik olarak embedding job kuyruğa eklenir
// Queue worker job'u işler ve embedding güncellenir
```

### Manuel Embedding Generation

Tek bir ürün için:
```bash
php artisan tinker
$tenant = \App\Models\Tenant::find(2);
tenancy()->initialize($tenant);
$service = app(\App\Services\AI\EmbeddingService::class);
$product = \Modules\Shop\App\Models\ShopProduct::find(1);
$embedding = $service->generateProductEmbedding($product);
$product->update([
    'embedding' => json_encode($embedding),
    'embedding_generated_at' => now(),
    'embedding_model' => 'text-embedding-3-small'
]);
```

### Toplu Embedding Generation

Belirli sayıda ürün için:
```bash
# İlk 100 ürün için (embedding olmayan)
php artisan products:generate-embeddings --tenant=2 --limit=100

# Tüm ürünler için (mevcut embedding'leri yeniden oluştur)
php artisan products:generate-embeddings --tenant=2 --limit=1000 --force
```

### Embedding Status Kontrolü

```bash
php artisan tinker --execute="
\$tenant = \App\Models\Tenant::find(2);
tenancy()->initialize(\$tenant);
\$total = \Modules\Shop\App\Models\ShopProduct::count();
\$withEmbedding = \Modules\Shop\App\Models\ShopProduct::whereNotNull('embedding')->count();
echo 'Total: ' . \$total . ', With Embedding: ' . \$withEmbedding . ', Without: ' . (\$total - \$withEmbedding);
"
```

---

## 🔍 Sorun Giderme

### 1. Worker çalışmıyor

**Kontrol:**
```bash
supervisorctl status
ps aux | grep "queue:work"
```

**Çözüm:**
```bash
supervisorctl restart laravel-worker:*
```

### 2. Job işlenmiyor

**Log kontrol:**
```bash
tail -f storage/logs/worker.log
tail -f storage/logs/laravel.log
```

**Redis kontrol:**
```bash
redis-cli
> LLEN queues:default
> LRANGE queues:default 0 -1
```

### 3. Embedding oluşmuyor

**Adım 1: Job çalışıyor mu?**
```bash
tail -f storage/logs/worker.log | grep GenerateProductEmbedding
```

**Adım 2: API key doğru mu?**
```bash
php artisan tinker --execute="echo config('openai.api_key');"
```

**Adım 3: Kolonlar var mı?**
```bash
php artisan tinker --execute="
\$tenant = \App\Models\Tenant::find(2);
tenancy()->initialize(\$tenant);
echo \DB::select('SHOW COLUMNS FROM shop_products LIKE \"embedding\"')[0]->Field ?? 'NOT FOUND';
"
```

### 4. Migration çalışmadı

**Tenant migration'ı tekrar çalıştır:**
```bash
php artisan tenants:migrate --path=database/migrations/tenant/2025_10_17_221722_add_embedding_to_shop_products.php
```

### 5. Kod değişikliklerinden sonra

**Worker'ları mutlaka restart edin:**
```bash
supervisorctl restart laravel-worker:*
```

---

## 📊 Performans

### Rate Limiting

- OpenAI API: 3000 request/dakika
- Command'da 20ms delay var (ürünler arası)
- 1000 ürün: ~20 saniye sürer

### Maliyet

- Model: `text-embedding-3-small`
- Fiyat: $0.02 / 1M token
- Örnek: 1000 ürün × 500 token ortalama = 500K token = $0.01

### Queue Performansı

- 2 worker paralel çalışıyor
- Her job ~800ms (OpenAI API call + DB update)
- Saniyede ~2-3 ürün işlenebilir

---

## 🔐 Güvenlik

### API Key

`.env` dosyasında saklanır:
```env
OPENAI_API_KEY=sk-proj-xxxxx
AI_ENABLED=true
```

### Tenant Isolation

Her tenant kendi database'inde embedding'lerini saklar. Job'lar tenant context'ini korur.

---

## 📝 Notlar

### Önemli!

1. **Kod değişikliği sonrası:** Mutlaka `supervisorctl restart laravel-worker:*`
2. **Migration:** Yeni tenant eklendiğinde `php artisan tenants:migrate` çalıştır
3. **Yeni ürün:** Event listener otomatik embedding oluşturur
4. **Güncelleme:** Önemli alanlar değiştiğinde embedding yenilenir (5s delay)

### Gelecek İyileştirmeler

- [ ] Batch processing (multiple products at once)
- [ ] Priority queue (critical products first)
- [ ] Failed job monitoring/alerting
- [ ] Embedding versioning (model değiştiğinde)
- [ ] Prometheus metrics

---

## 📚 İlgili Dökümanlar

- `readme/AI-SETUP-INSTRUCTIONS.md` - Hybrid Search kurulumu
- `app/Jobs/GenerateProductEmbedding.php` - Job implementasyonu
- `app/Services/AI/EmbeddingService.php` - Embedding service
- `Modules/Shop/app/Models/ShopProduct.php` - Model event listeners

---

**Son Güncelleme:** 2025-10-17
**Yazar:** Claude Code
**Versiyon:** 1.0
