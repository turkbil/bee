-- ============================================================================
-- MANUEL AI KREDİ EKLEME SQL SCRIPT
-- ============================================================================
-- Tarih: 27 Ekim 2025
-- Sistem: Tuufi Multi-Tenant AI Credit System
--
-- UYARI: Bu script'i çalıştırmadan önce:
-- 1. Tenant ID'yi doğrulayın
-- 2. Eklenecek kredi miktarını belirleyin
-- 3. Mevcut bakiyeyi yedekleyin
-- ============================================================================

-- ============================================================================
-- ADIM 0: MEVCUT DURUMU KONTROL ET
-- ============================================================================

-- Tenant bilgilerini görüntüle
SELECT
    id,
    title,
    ai_credits_balance as mevcut_kolon_kredisi,
    JSON_EXTRACT(data, '$.ai_credits_balance') as mevcut_json_kredisi,
    is_premium
FROM tenants
WHERE id = 2; -- TENANT_ID'yi değiştirin

-- Mevcut purchase kayıtlarını görüntüle
SELECT
    tenant_id,
    COUNT(*) as kayit_sayisi,
    SUM(credit_amount) as toplam_purchase
FROM ai_credit_purchases
WHERE tenant_id = 2 AND status = 'completed' -- TENANT_ID'yi değiştirin
GROUP BY tenant_id;

-- ============================================================================
-- ADIM 1: PURCHASE KAYDI OLUŞTUR
-- ============================================================================

INSERT INTO ai_credit_purchases (
    tenant_id,
    user_id,
    package_id,
    credit_amount,
    price_paid,
    amount,
    currency,
    status,
    payment_method,
    payment_transaction_id,
    payment_data,
    notes,
    purchased_at,
    created_at,
    updated_at
) VALUES (
    2,                              -- tenant_id: BURAYA TENANT ID'yi yazın
    NULL,                           -- user_id: NULL = admin grant
    1,                              -- package_id: Referans için (1=Başlangıç)
    100000,                         -- credit_amount: EKLENECEK KREDİ MİKTARI
    0.00,                           -- price_paid: 0 = ücretsiz ekleme
    0.00,                           -- amount: 0 = ücretsiz
    'TRY',                          -- currency: Para birimi
    'completed',                    -- status: completed (direkt aktif)
    'admin_grant',                  -- payment_method: Admin tarafından verildi
    NULL,                           -- payment_transaction_id: Ödeme yok
    NULL,                           -- payment_data: Ödeme detayı yok
    'Manuel kredi ekleme - Claude tarafından eklendi (100k kredi)', -- notes: AÇIKLAMA YAZIN
    NOW(),                          -- purchased_at: Şu an
    NOW(),                          -- created_at
    NOW()                           -- updated_at
);

-- Son eklenen purchase ID'yi göster
SELECT LAST_INSERT_ID() as purchase_id;

-- ============================================================================
-- ADIM 2: TENANT KOLONUNU GÜNCELLE
-- ============================================================================

-- Tenant kredisine ekle (mevcut + yeni)
UPDATE tenants
SET ai_credits_balance = ai_credits_balance + 100000  -- EKLENECEK MİKTAR
WHERE id = 2; -- TENANT_ID

-- Güncelleme kontrolü
SELECT
    id,
    title,
    ai_credits_balance as yeni_kolon_kredisi
FROM tenants
WHERE id = 2; -- TENANT_ID

-- ============================================================================
-- ADIM 3: DATA JSON KOLONUNU GÜNCELLE (KRİTİK!)
-- ============================================================================

-- JSON kolondaki ai_credits_balance'ı tenant kolonuyla senkronize et
UPDATE tenants
SET data = JSON_SET(
    data,
    '$.ai_credits_balance',
    ai_credits_balance  -- Tenant kolonundan al
)
WHERE id = 2; -- TENANT_ID

-- JSON güncelleme kontrolü
SELECT
    id,
    title,
    ai_credits_balance as kolon_kredisi,
    JSON_EXTRACT(data, '$.ai_credits_balance') as json_kredisi,
    CASE
        WHEN ai_credits_balance = CAST(JSON_EXTRACT(data, '$.ai_credits_balance') AS UNSIGNED)
        THEN '✅ SENKRON'
        ELSE '❌ FARKLI'
    END as durum
FROM tenants
WHERE id = 2; -- TENANT_ID

-- ============================================================================
-- ADIM 4: FINAL DOĞRULAMA
-- ============================================================================

-- Tüm kredi kaynaklarını karşılaştır
SELECT 'Purchases Toplamı' as kaynak, SUM(credit_amount) as toplam
FROM ai_credit_purchases
WHERE tenant_id = 2 AND status = 'completed'

UNION ALL

SELECT 'Tenant Kolonu', ai_credits_balance
FROM tenants
WHERE id = 2

UNION ALL

SELECT 'Data JSON', CAST(JSON_EXTRACT(data, '$.ai_credits_balance') AS UNSIGNED)
FROM tenants
WHERE id = 2;

-- ============================================================================
-- BONUS: FARKLI TENANT'LAR İÇİN TOPLU GÜNCELLEME
-- ============================================================================

-- Tüm tenant'ların kredi durumunu kontrol et
SELECT
    t.id,
    t.title,
    t.ai_credits_balance as kolon,
    CAST(JSON_EXTRACT(t.data, '$.ai_credits_balance') AS UNSIGNED) as json_data,
    COALESCE(SUM(p.credit_amount), 0) as purchases_toplam,
    CASE
        WHEN t.ai_credits_balance = CAST(JSON_EXTRACT(t.data, '$.ai_credits_balance') AS UNSIGNED)
        THEN '✅'
        ELSE '❌'
    END as senkron
FROM tenants t
LEFT JOIN ai_credit_purchases p ON p.tenant_id = t.id AND p.status = 'completed'
GROUP BY t.id
ORDER BY t.id;

-- Senkronizasyon bozuk olan tenant'ları düzelt (HEPSİ İÇİN)
-- UYARI: Bu tüm tenant'ları etkiler!
UPDATE tenants
SET data = JSON_SET(
    data,
    '$.ai_credits_balance',
    ai_credits_balance
)
WHERE CAST(JSON_EXTRACT(data, '$.ai_credits_balance') AS UNSIGNED) != ai_credits_balance;

-- ============================================================================
-- YARDIMCI QUERY'LER
-- ============================================================================

-- Belirli bir tenant'ın tüm purchase geçmişi
SELECT
    id,
    tenant_id,
    credit_amount,
    price_paid,
    currency,
    status,
    payment_method,
    notes,
    purchased_at,
    created_at
FROM ai_credit_purchases
WHERE tenant_id = 2 -- TENANT_ID
ORDER BY created_at DESC;

-- Tenant'ın AI kullanım geçmişi
SELECT
    tenant_id,
    COUNT(*) as kullanim_sayisi,
    SUM(credits_used) as toplam_harcanan,
    AVG(credits_used) as ortalama_harcanan,
    MIN(used_at) as ilk_kullanim,
    MAX(used_at) as son_kullanim
FROM ai_credit_usage
WHERE tenant_id = 2 -- TENANT_ID
GROUP BY tenant_id;

-- Premium tenant'ları listele (sınırsız kredi)
SELECT
    id,
    title,
    is_premium,
    ai_credits_balance,
    'Sınırsız' as premium_durumu
FROM tenants
WHERE is_premium = 1;

-- Düşük kredili tenant'ları listele
SELECT
    id,
    title,
    ai_credits_balance,
    CASE
        WHEN ai_credits_balance < 10 THEN '🔴 Kritik'
        WHEN ai_credits_balance < 100 THEN '🟡 Düşük'
        ELSE '🟢 Yeterli'
    END as durum
FROM tenants
WHERE is_premium = 0
ORDER BY ai_credits_balance ASC
LIMIT 10;

-- ============================================================================
-- ROLLBACK (Hatalı ekleme durumunda)
-- ============================================================================

-- Son eklenen purchase kaydını sil
DELETE FROM ai_credit_purchases
WHERE id = (SELECT MAX(id) FROM ai_credit_purchases WHERE tenant_id = 2);

-- Tenant kredisinden düş
UPDATE tenants
SET ai_credits_balance = ai_credits_balance - 100000  -- DÜŞÜLECEKktar
WHERE id = 2; -- TENANT_ID

-- JSON'u güncelle
UPDATE tenants
SET data = JSON_SET(data, '$.ai_credits_balance', ai_credits_balance)
WHERE id = 2; -- TENANT_ID

-- ============================================================================
-- CACHE TEMİZLİĞİ (Laravel tarafında)
-- ============================================================================

-- Bu komutlar SQL değil, Laravel Artisan komutları:
-- php artisan cache:clear
-- php artisan tinker --execute="Cache::forget('tenant_credits_2');"

-- ============================================================================
-- NOTLAR
-- ============================================================================

/*
1. TENANT_ID'yi mutlaka değiştirin!
2. EKLENECEK KREDİ miktarını doğrulayın!
3. Her adımdan sonra KONTROL sorgusu çalıştırın!
4. Data JSON güncellemesini ASLA ATLAMAYIN!
5. İşlem bitince Laravel cache'ini temizleyin!

ÖNEMLI: Bu script sadece manuel acil durumlar içindir.
Normal kullanımda Laravel Tinker veya Artisan Command kullanın!

Referans: /readme/ai_kredi_ekleme/MANUEL-KREDI-EKLEME-KILAVUZU.md
*/
