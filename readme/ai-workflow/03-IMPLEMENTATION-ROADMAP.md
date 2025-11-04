# 🚀 AI WORKFLOW IMPLEMENTATION ROADMAP

## 📋 MASTER TODO LIST

**⚠️ ÖNEMLİ:** Her görevi tamamladıktan sonra [ ] yerine [x] yaparak işaretleyin!

---

## PHASE 1: DATABASE & ALTYAPI (3-5 gün)

### 1.1 Migration Dosyaları

```sql
-- ⚠️ CLAUDE.md KURALI: Central + Tenant çift migration zorunlu!
```

**TODO:**
- [ ] Central migration: `database/migrations/2024_XX_XX_create_tenant_conversation_flows.php`
- [ ] Tenant migration: `database/migrations/tenant/2024_XX_XX_create_tenant_conversation_flows.php`
- [ ] Central migration: `database/migrations/2024_XX_XX_create_ai_tenant_directives.php`
- [ ] Tenant migration: `database/migrations/tenant/2024_XX_XX_create_ai_tenant_directives.php`
- [ ] Tenant only: `database/migrations/tenant/2024_XX_XX_create_ai_conversations.php`
- [ ] Migration'ları çalıştır: `php artisan migrate && php artisan tenants:migrate`
- [ ] Rollback test et: `php artisan migrate:rollback`

#### 📝 MIGRATION ÖRNEK DOSYA:

```php
<?php
// database/migrations/2024_11_04_120000_create_tenant_conversation_flows.php
// ⚠️ AYNI DOSYA database/migrations/tenant/ ALTINDA DA OLMALI!

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant_conversation_flows', function (Blueprint $table) {
            // Birincil anahtar
            $table->id()
                ->comment('Akış ID - Benzersiz tanımlayıcı');

            // Tenant ilişkisi
            $table->unsignedInteger('tenant_id')
                ->comment('Hangi tenant (örn: 2=ixtif.com, 3=diğer)');

            // Akış bilgileri
            $table->string('flow_name', 255)
                ->comment('Akış adı - Admin panelde görünen isim (örn: "E-Ticaret Satış Akışı")');

            $table->text('flow_description')->nullable()
                ->comment('Akış açıklaması - Admin için bilgi notu, kullanıcı görmez');

            $table->json('flow_data')
                ->comment('Tüm akış yapısı: nodes (kutucuklar), edges (bağlantılar), positions - Drawflow JSON');

            $table->string('start_node_id', 50)
                ->comment('İlk çalışacak node ID - Akış buradan başlar (örn: "node_greeting_1")');

            // Durum kontrol
            $table->boolean('is_active')->default(true)
                ->comment('Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar çalışır)');

            $table->integer('priority')->default(0)
                ->comment('Öncelik - Birden fazla aktif flow varsa en düşük sayı çalışır (0 en yüksek öncelik)');

            // Audit bilgileri
            $table->unsignedBigInteger('created_by')->nullable()
                ->comment('Akışı oluşturan admin user ID - users tablosundan');

            $table->unsignedBigInteger('updated_by')->nullable()
                ->comment('Son güncelleyen admin user ID - users tablosundan');

            // Zaman damgaları
            $table->timestamps();

            // İndeksler (performans)
            $table->index(['tenant_id', 'is_active'], 'idx_tenant_active')
                ->comment('Tenant aktif akış sorgusunu hızlandırır');

            $table->index(['tenant_id', 'priority'], 'idx_priority')
                ->comment('Öncelik sırasına göre seçim için - En düşük sayı önce');

            // Foreign key (opsiyonel, sadece central için)
            // $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Tablo seviyesinde açıklama
        DB::statement("ALTER TABLE tenant_conversation_flows COMMENT='Tenant AI sohbet akışları - Admin panelden çizilen akışlar burada saklanır'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_conversation_flows');
    }
};
```

**⚠️ KRİTİK:** Bu dosyayı oluşturduktan sonra:
```bash
# Dosyayı tenant klasörüne kopyala
cp database/migrations/2024_11_04_120000_create_tenant_conversation_flows.php \
   database/migrations/tenant/2024_11_04_120000_create_tenant_conversation_flows.php

# Permission düzelt
sudo chown tuufi.com_:psaserv database/migrations/*.php
sudo chown tuufi.com_:psaserv database/migrations/tenant/*.php
sudo chmod 644 database/migrations/*.php
sudo chmod 644 database/migrations/tenant/*.php
```

### 1.2 Model Dosyaları

**TODO:**
- [ ] `app/Models/TenantConversationFlow.php` oluştur
- [ ] `app/Models/AITenantDirective.php` oluştur
- [ ] `app/Models/AIConversation.php` oluştur
- [ ] Model relationships tanımla
- [ ] Model casts ekle (JSON fields)
- [ ] Model scopes ekle (active, byTenant)

### 1.3 Database Seeder'lar

**TODO:**
- [ ] `database/seeders/AIWorkflowSeeder.php` oluştur
- [ ] İxtif.com için default flow seed'i
- [ ] İxtif.com için directive seed'leri (20+ ayar)
- [ ] Test tenant için örnek flow
- [ ] Seeder'ı çalıştır: `php artisan db:seed --class=AIWorkflowSeeder`

---

## PHASE 2: NODE SİSTEMİ (5-7 gün)

### 2.1 Base Node Yapısı

**TODO:**
- [ ] `app/Services/ConversationNodes/AbstractNode.php` oluştur
- [ ] `app/Services/ConversationNodes/NodeInterface.php` oluştur
- [ ] `app/Services/ConversationNodes/NodeExecutor.php` oluştur
- [ ] `app/Services/ConversationNodes/NodeRegistry.php` oluştur

### 2.2 Ortak Node'lar (Tüm Tenant'lar)

**TODO:**
- [ ] `app/Services/ConversationNodes/Common/AIResponseNode.php`
- [ ] `app/Services/ConversationNodes/Common/ConditionNode.php`
- [ ] `app/Services/ConversationNodes/Common/CollectDataNode.php`
- [ ] `app/Services/ConversationNodes/Common/ShareContactNode.php`
- [ ] `app/Services/ConversationNodes/Common/WebhookNode.php`
- [ ] `app/Services/ConversationNodes/Common/EndNode.php`

### 2.3 Tenant-Spesifik Node Sistemi (DİNAMİK)

```php
// ⚡ YENİ: Tenant'a özel node klasör yapısı
// NOT: İxtif.com şu anda TEK TENANT (ID: 2)
// İleride yeni tenant'lar eklenirse bu yapı genişleyecek

app/Services/ConversationNodes/
├── Common/              # Ortak node'lar (tüm tenant'lar kullanabilir)
├── TenantSpecific/      # Tenant özel node'lar
│   ├── Tenant_2/        # İXTİF.COM (tenant_id: 2) - ŞU AN TEK TENANT
│   │   ├── CategoryDetectionNode.php
│   │   ├── ProductRecommendNode.php
│   │   ├── PriceFilterNode.php
│   │   ├── CurrencyConvertNode.php
│   │   ├── StockCheckNode.php
│   │   ├── ComparisonNode.php
│   │   └── QuotationNode.php
│   │
│   └── loader.php       # Dinamik yükleyici
```

**TODO - İxtif.com Özel Node'lar:**
- [ ] `TenantSpecific/Tenant_2/CategoryDetectionNode.php` - Kategori tespit ve sınırlama
- [ ] `TenantSpecific/Tenant_2/ProductRecommendNode.php` - Anasayfa+stok öncelikli
- [ ] `TenantSpecific/Tenant_2/PriceFilterNode.php` - Ucuz/pahalı filtreleme
- [ ] `TenantSpecific/Tenant_2/CurrencyConvertNode.php` - Kur dönüşümü
- [ ] `TenantSpecific/Tenant_2/StockCheckNode.php` - Stok durumu
- [ ] `TenantSpecific/Tenant_2/ComparisonNode.php` - F4 vs F6 karşılaştırma
- [ ] `TenantSpecific/Tenant_2/QuotationNode.php` - Teklif hazırlama

### 2.4 Dinamik Node Loader

```php
// app/Services/ConversationNodes/TenantNodeLoader.php
class TenantNodeLoader
{
    public static function loadTenantNodes($tenantId)
    {
        $tenantPath = "TenantSpecific/Tenant_{$tenantId}/";

        if (File::exists(app_path("Services/ConversationNodes/{$tenantPath}"))) {
            // Tenant'a özel node'ları yükle
            $files = File::files(app_path("Services/ConversationNodes/{$tenantPath}"));

            foreach ($files as $file) {
                $className = "App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_{$tenantId}\\"
                           . basename($file, '.php');

                NodeRegistry::register($className::getType(), $className);
            }
        }
    }
}
```

**TODO:**
- [ ] `TenantNodeLoader.php` oluştur
- [ ] Auto-discovery mekanizması ekle
- [ ] Node caching sistemi ekle
- [ ] Tenant değişiminde cache temizleme

---

## PHASE 3: FLOW ENGINE (3-4 gün)

### 3.1 Ana Engine

**TODO:**
- [ ] `app/Services/ConversationFlowEngine.php` oluştur
- [ ] `processMessage()` metodu implement et
- [ ] `getCurrentNode()` metodu implement et
- [ ] `executeNode()` metodu implement et
- [ ] `updateState()` metodu implement et
- [ ] Cache layer ekle (Redis/Cache facade)

### 3.2 Güvenlik & Kontroller

**TODO:**
- [ ] `app/Services/CircularDependencyDetector.php` - Döngü kontrolü
- [ ] `app/Services/TimeoutManager.php` - Max 30 saniye kontrolü
- [ ] `app/Services/NodeValidator.php` - Node config validation
- [ ] `app/Services/FlowValidator.php` - Flow integrity check
- [ ] Rate limiting middleware ekle

### 3.3 Controller Entegrasyonu

**TODO:**
- [ ] `PublicAIController.php` güncelle - yeni engine'i kullan
- [ ] Backward compatibility sağla (eski sistem çalışmalı)
- [ ] Feature flag ekle: `ai_workflow_enabled`
- [ ] Fallback mekanizması ekle

---

## PHASE 4: ADMIN PANEL (5-7 gün)

### 4.1 Livewire Components

**TODO:**
- [ ] `app/Http/Livewire/Admin/AI/FlowList.php` - Akış listesi
- [ ] `app/Http/Livewire/Admin/AI/FlowEditor.php` - Drawflow editör
- [ ] `app/Http/Livewire/Admin/AI/DirectiveManager.php` - Tenant ayarları
- [ ] `app/Http/Livewire/Admin/AI/NodeLibrary.php` - Node kütüphanesi
- [ ] `app/Http/Livewire/Admin/AI/FlowTester.php` - Test arayüzü

### 4.2 Blade Views

**TODO:**
- [ ] `resources/views/livewire/admin/ai/flow-list.blade.php`
- [ ] `resources/views/livewire/admin/ai/flow-editor.blade.php`
- [ ] `resources/views/livewire/admin/ai/directive-manager.blade.php`
- [ ] `resources/views/livewire/admin/ai/node-library.blade.php`
- [ ] `resources/views/livewire/admin/ai/flow-tester.blade.php`

### 4.3 Drawflow Integration

**TODO:**
- [ ] `npm install drawflow` - Kütüphane kurulumu
- [ ] `resources/js/ai-flow-editor.js` oluştur
- [ ] Drag & drop functionality implement et
- [ ] Node config modal'ları oluştur
- [ ] Save/Load flow functionality
- [ ] `npm run prod` - Assets compile
- [ ] Cache clear: `php artisan view:clear`

### 4.4 Routes & Menu

**TODO:**
- [ ] Admin route'ları ekle (`routes/admin.php`)
- [ ] Admin menüye "AI Workflow" ekle
- [ ] Permission check ekle (sadece super admin)
- [ ] Breadcrumb navigation ekle

---

## PHASE 5: İXTİF.COM ÖZEL YAPILANDIRMA (2-3 gün)

### 5.1 İxtif Flow Oluşturma

**TODO:**
- [ ] 10 adımlı e-ticaret flow'u tasarla
- [ ] Flow'u admin panel'den oluştur
- [ ] Node bağlantılarını yapılandır
- [ ] Her node için config ayarla
- [ ] Flow'u aktif et

### 5.2 İxtif Directives

**TODO:**
- [ ] 20+ directive kaydı oluştur
- [ ] Kategori sınırlama ayarları
- [ ] Ürün gösterim ayarları
- [ ] Fiyat politikası ayarları
- [ ] Lead toplama ayarları
- [ ] Teknik özellik ayarları

### 5.3 Test Data

**TODO:**
- [ ] Test ürünleri kontrol et (transpalet, forklift)
- [ ] Exchange rates tablosu kontrol et
- [ ] Settings values kontrol et (whatsapp, phone)
- [ ] Test conversation oluştur

---

## PHASE 6: TEST & DEBUG (3-4 gün)

### 6.1 Unit Tests

**TODO:**
- [ ] `tests/Unit/Services/ConversationNodes/AbstractNodeTest.php`
- [ ] `tests/Unit/Services/ConversationNodes/Common/*Test.php` (6 dosya)
- [ ] `tests/Unit/Services/ConversationNodes/TenantSpecific/Tenant_2/*Test.php` (7 dosya)
- [ ] `tests/Unit/Services/ConversationFlowEngineTest.php`
- [ ] Coverage raporu: `php artisan test --coverage`

### 6.2 Integration Tests

**TODO:**
- [ ] `tests/Integration/AIWorkflowTest.php` - Full flow test
- [ ] `tests/Integration/TenantIsolationTest.php` - Multi-tenant test
- [ ] `tests/Integration/CategoryDetectionTest.php` - Kategori testi
- [ ] `tests/Integration/CurrencyConversionTest.php` - Kur dönüşüm testi

### 6.3 Manual Testing

**TODO:**
- [ ] Admin panel'den flow oluştur
- [ ] Chat widget'tan test et
- [ ] "Transpalet arıyorum" senaryosu
- [ ] "Fiyat TL olarak" senaryosu
- [ ] "F4 vs F6 karşılaştır" senaryosu
- [ ] Telefon toplama senaryosu
- [ ] WhatsApp paylaşma senaryosu

---

## PHASE 7: DEPLOYMENT (2 gün)

### 7.1 Pre-Deployment

**TODO:**
- [ ] Git checkpoint: `git add . && git commit -m "🔧 CHECKPOINT: Before AI Workflow"`
- [ ] Database backup: `php artisan backup:run --only-db`
- [ ] Test tenant'ta son test

### 7.2 Production Deploy

**TODO:**
- [ ] Migration çalıştır: `php artisan migrate`
- [ ] Tenant migration: `php artisan tenants:migrate`
- [ ] Seeder çalıştır: `php artisan db:seed --class=AIWorkflowSeeder`
- [ ] Assets compile: `npm run prod`
- [ ] Cache temizle: `php artisan cache:clear && php artisan view:clear`
- [ ] OPcache reset: `curl -s -k https://ixtif.com/opcache-reset.php`

### 7.3 Permission Fix

**TODO:**
- [ ] `sudo chown -R tuufi.com_:psaserv app/Services/ConversationNodes/`
- [ ] `sudo find app/Services/ConversationNodes/ -type f -exec chmod 644 {} \;`
- [ ] `sudo find app/Services/ConversationNodes/ -type d -exec chmod 755 {} \;`

### 7.4 Post-Deployment

**TODO:**
- [ ] Smoke test: `curl -I https://ixtif.com/`
- [ ] Admin panel erişim testi
- [ ] Chat widget çalışma testi
- [ ] Error log kontrolü: `tail -f storage/logs/laravel.log`
- [ ] Final commit: `git add . && git commit -m "✅ AI Workflow implemented"`

---

## PHASE 8: MONITORING & OPTIMIZATION (Ongoing)

### 8.1 Performance Monitoring

**TODO:**
- [ ] Node execution süreleri logla
- [ ] Flow completion rate takip et
- [ ] Cache hit ratio kontrol et
- [ ] Database query optimization

### 8.2 Error Tracking

**TODO:**
- [ ] Sentry integration (opsiyonel)
- [ ] Custom error logging
- [ ] Failed node execution alerts
- [ ] Timeout alerts

### 8.3 Analytics

**TODO:**
- [ ] Conversion rate (lead/chat oranı)
- [ ] Popular flow paths analizi
- [ ] Node success rates
- [ ] User satisfaction metrics

---

## 📊 İLERLEME TAKİBİ

```
Toplam Görev: 150+
Tamamlanan: 0
Kalan: 150+
İlerleme: 0%

Phase 1: [ ] Database (0/15)
Phase 2: [ ] Nodes (0/20)
Phase 3: [ ] Engine (0/15)
Phase 4: [ ] Admin (0/20)
Phase 5: [ ] İxtif (0/10)
Phase 6: [ ] Test (0/15)
Phase 7: [ ] Deploy (0/15)
Phase 8: [ ] Monitor (0/10)
```

---

## 🚨 KRİTİK UYARILAR

1. **Migration Çiftliliği:** Her migration hem central hem tenant'ta olmalı!
2. **Permission:** Her yeni dosya sonrası chown/chmod yap!
3. **Cache:** View/config değişikliği sonrası cache temizle!
4. **Test:** Deploy öncesi mutlaka test tenant'ta dene!
5. **Backup:** Production deploy öncesi backup al!

---

## 📝 NOTLAR

- Her TODO'yu tamamladıktan sonra [x] ile işaretle
- Problemler çıkarsa bu dosyaya not ekle
- Phase'ler sıralı gitmeli, atlama yapma
- İxtif.com öncelikli, diğer tenant'lar sonra

---

**SON GÜNCELLEME:** 2024-11-04
**HAZIRLAYANLAR:** Claude AI + {{ kullanıcı }}

---

## İXTİF.COM TENANT BİLGİSİ

**⚠️ ÖNEMLİ NOT:**
- İxtif.com şu anda **TEK TENANT** olarak çalışıyor (Tenant ID: 2)
- Tüm geliştirme öncelikle İxtif.com için yapılacak
- İleride yeni tenant eklenirse, bu sistemin dinamik yapısı sayesinde kolayca genişleyebilecek

## TENANT-SPESİFİK NODE OLUŞTURMA KILAVUZU

### İleride Yeni Tenant Eklenirse:

1. **Klasör oluştur:**
```bash
# Örnek: Tenant ID 5 için
mkdir -p app/Services/ConversationNodes/TenantSpecific/Tenant_5/
```

2. **Node dosyası oluştur:**
```php
// app/Services/ConversationNodes/TenantSpecific/Tenant_5/CustomNode.php
namespace App\Services\ConversationNodes\TenantSpecific\Tenant_5;

class CustomNode extends \App\Services\ConversationNodes\AbstractNode
{
    // Yeni tenant'a özel logic
}
```

3. **Auto-discovery:** Sistem otomatik yükleyecek!

4. **Permission fix:**
```bash
sudo chown -R tuufi.com_:psaserv app/Services/ConversationNodes/TenantSpecific/Tenant_5/
sudo chmod 755 app/Services/ConversationNodes/TenantSpecific/Tenant_5/
sudo chmod 644 app/Services/ConversationNodes/TenantSpecific/Tenant_5/*.php
```

**Şu an sadece İxtif.com (Tenant_2) klasörü oluşturulacak!**

---

**BU ROADMAP'İ TAKİP EDİN VE HER ADIMI İŞARETLEYİN!**