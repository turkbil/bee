# 🎯 FAZ BAZLI ÖNCELİK PLANI

**Proje Hedefi:** E-ticaret + Üyelik Satış Sistemi
**Birincil Öncelik:** ÜYELİK SATIŞI (Aylık/Yıllık Paket)
**İkincil Öncelik:** Ürün/Forklift Satışı

---

## 📊 TABLO ANALİZİ ÖZETİ

```
66 Tablo → ÇOK FAZLA
─────────────────────────────────
✅ CORE:          25 tablo (mutlaka)
🟡 İYİ OLUR:      16 tablo (projeye göre)
❌ GEREKSIZ:      25 tablo (universal/json)

ÖNERİ: 35-40 TABLO
```

---

## 🚀 FAZ 1: ÜYELİK SİSTEMİ (1. AY) - 15 Tablo

### Hedef
✅ Kullanıcı üyelik satın alabilmeli
✅ Aylık/yıllık paketler olmalı
✅ Otomatik yenileme çalışmalı
✅ Ödeme entegrasyonu (iyzico)

### Gerekli Tablolar (15)

#### 🎫 Üyelik Sistemi (3)
```
001. shop_subscription_plans        // Paketler (Aylık ₺99, Yıllık ₺990)
002. shop_subscriptions             // Aktif üyelikler
003. shop_membership_tiers          // Seviyeler (Bronze, Silver, Gold) - OPSİYONEL
```

#### 💳 Ödeme (3)
```
004. shop_payment_methods           // Kredi kartı, havale, vb
005. shop_payments                  // Ödeme kayıtları
006. shop_orders                    // Üyelik siparişleri için
```

#### 👤 Kullanıcı (3)
```
007. shop_customers                 // Müşteri profil (zaten users var, buna gerek var mı?)
008. shop_customer_addresses        // Fatura adresi
009. shop_customer_groups           // VIP, Kurumsal vb
```

#### 💰 Kupon & Kampanya (2)
```
010. shop_coupons                   // İndirim kuponları (YENI2025)
011. shop_coupon_usages             // Kullanım kayıtları
```

#### 📊 Sadakat (3) - OPSİYONEL
```
012. shop_loyalty_points            // Sadakat puanları
013. shop_loyalty_transactions      // Puan hareketleri
014. shop_gift_cards                // Hediye kartları (kupon'la birleştirilebilir)
```

#### ⚙️ Sistem (1)
```
015. shop_settings                  // Sistem ayarları
```

**FAZ 1 TOPLAM: 15 Tablo** (12 kesin + 3 opsiyonel)

---

### FAZ 1 - Kararlar

#### ✅ Kesinlikle Tutulacaklar
```
✅ shop_subscription_plans          // Paket tanımları
✅ shop_subscriptions               // Aktif üyelikler
✅ shop_payment_methods             // Ödeme yöntemleri
✅ shop_payments                    // Ödeme kayıtları
✅ shop_orders                      // Sipariş kayıtları
✅ shop_customer_addresses          // Fatura adresi
✅ shop_coupons                     // Kupon sistemi
✅ shop_coupon_usages               // Kullanım takibi
✅ shop_settings                    // Ayarlar
```

#### 🤔 Sorgulanması Gerekenler
```
🤔 shop_customers                   // users tablosu var, buna gerek var mı?
🤔 shop_customer_groups             // İlk fazda gerekli mi?
🤔 shop_membership_tiers            // Otomatik seviye sistemi gerekli mi?
🤔 shop_loyalty_points              // İlk fazda sadakat gerekli mi?
🤔 shop_gift_cards                  // shop_coupons'la birleştirilebilir
```

#### ❌ Çıkarılacaklar
```
❌ shop_gift_card_transactions      // shop_coupon_usages yeterli
```

---

## 🏗️ FAZ 2: ÜRÜN KATALOĞU (2. AY) - +15 Tablo

### Hedef
✅ Ürün/forklift listeleme
✅ Kategori, marka filtreleme
✅ Varyant yönetimi (mast, batarya)
✅ Stok takibi

### Eklenecek Tablolar (+15)

#### 📦 Katalog (6)
```
016. shop_categories                // Kategori ağacı
017. shop_brands                    // Markalar
018. shop_products                  // Ana ürünler
019. shop_product_variants          // Varyantlar
020. shop_attributes                // Özellikler (kapasite, menzil)
021. shop_product_attributes        // Ürün-özellik ilişkisi
```

#### 📊 Stok (4)
```
022. shop_warehouses                // Depolar
023. shop_inventory                 // Stok kayıtları
024. shop_stock_movements           // Stok hareketleri
025. shop_price_lists               // B2B fiyat listeleri
```

#### 🛒 Sepet (2)
```
026. shop_carts                     // Sepetler
027. shop_cart_items                // Sepet ürünleri
```

#### 💰 Vergi (2)
```
028. shop_taxes                     // Vergi tanımları
029. shop_tax_rates                 // Vergi oranları
```

#### 📝 Sipariş Ekleme (1)
```
030. shop_order_items               // Sipariş kalemleri (ürünler için)
```

**FAZ 2 TOPLAM: +15 Tablo** (Toplam: 30)

---

## 🎨 FAZ 3: İLERİ ÖZELLİKLER (3. AY) - +10 Tablo

### Hedef
✅ İnceleme/yorum sistemi
✅ Favori listeleri
✅ B2B teklif sistemi
✅ İade yönetimi

### Eklenecek Tablolar (+10)

#### ⭐ Yorum & Favori (3)
```
031. shop_reviews                   // Ürün yorumları
032. shop_wishlists                 // Favori listeleri
033. shop_comparisons               // Karşılaştırma
```

#### 💼 B2B (4)
```
034. shop_quotes                    // Teklifler
035. shop_quote_items               // Teklif kalemleri
036. shop_vendors                   // Satıcılar/Bayiler
037. shop_vendor_products           // Satıcı-ürün ilişkisi
```

#### ↩️ İade (3)
```
038. shop_returns                   // İadeler
039. shop_return_items              // İade kalemleri
040. shop_refunds                   // Para iadeleri
```

**FAZ 3 TOPLAM: +10 Tablo** (Toplam: 40)

---

## 🚢 FAZ 4: KARGO & LOJİSTİK (4. AY) - +3 Tablo

### Eklenecek Tablolar (+3)

```
041. shop_shipping_methods          // Kargo yöntemleri
042. shop_shipments                 // Kargo takibi
043. shop_order_addresses           // Teslimat adresleri (snapshot)
```

**FAZ 4 TOPLAM: +3 Tablo** (Toplam: 43)

---

## ❌ ÇIKARILACAK TABLOLAR (23)

### Universal Sistemler (10) - Ayrı Modüller Kullan
```
❌ shop_notifications               → Universal Notification
❌ shop_email_templates             → Universal Email Template
❌ shop_activity_logs               → ActivityLoggable Trait
❌ shop_seo_redirects               → SeoManagement Modülü
❌ shop_analytics                   → Google Analytics
❌ shop_product_views               → HasViewCounter Trait
❌ shop_search_logs                 → Universal Search Log
❌ shop_banners                     → WidgetManagement
❌ shop_newsletters                 → Universal Newsletter
❌ shop_tags + shop_product_tags    → Universal Tags (zaten var)
```

### JSON'da Tutulabilecekler (6)
```
❌ shop_product_images              → media_gallery (JSON)
❌ shop_product_videos              → video_url (string)
❌ shop_product_documents           → manual_pdf_url (string)
❌ shop_product_bundles             → bundle_products (JSON)
❌ shop_product_cross_sells         → related_products (JSON)
❌ shop_campaigns                   → shop_coupons'la birleştir
```

### Soru-Cevap (2) - Universal Comment
```
❌ shop_product_questions
❌ shop_product_answers
```

### Sektöre Özel (5) - Şimdilik Gereksiz
```
❌ shop_service_requests            → Ticket/Support Modülü
❌ shop_rental_contracts            → Kiralama sektöre özel
❌ shop_gift_card_transactions      → shop_coupon_usages yeterli
❌ shop_subscription_plans (duplike) → Zaten var
```

**ÇIKARILAN TOPLAM: 23 Tablo**

---

## 📊 FAZ BAZLI ÖZET

| Faz | Açıklama | Tablo Sayısı | Kümülatif | Süre |
|-----|----------|--------------|-----------|------|
| **1** | Üyelik Satışı | 12-15 | 15 | 1 ay |
| **2** | Ürün Kataloğu | +15 | 30 | +1 ay |
| **3** | İleri Özellikler | +10 | 40 | +1 ay |
| **4** | Kargo & Lojistik | +3 | 43 | +1 ay |
| **Çıkarılan** | - | -23 | - | - |
| **Mevcut** | - | 66 | - | - |

**SONUÇ: 66 → 43 Tablo** (23 tablo çıkarıldı)

---

## 🎯 ÖNERİLEN İLK FAZ YAPISISI (15 Tablo)

```sql
-- FAZ 1: ÜYELİK SİSTEMİ
001_create_shop_subscription_plans_table.php        ✅ MUTLAKA
002_create_shop_subscriptions_table.php             ✅ MUTLAKA
003_create_shop_membership_tiers_table.php          🟡 OPSİYONEL

-- ÖDEME
004_create_shop_payment_methods_table.php           ✅ MUTLAKA
005_create_shop_payments_table.php                  ✅ MUTLAKA
006_create_shop_orders_table.php                    ✅ MUTLAKA

-- KULLANICI (users tablosu zaten var)
007_create_shop_customer_addresses_table.php        ✅ MUTLAKA
008_create_shop_customer_groups_table.php           🟡 OPSİYONEL
009_create_shop_customers_table.php                 🤔 SORGULANMALI

-- KUPON
010_create_shop_coupons_table.php                   ✅ MUTLAKA
011_create_shop_coupon_usages_table.php             ✅ MUTLAKA

-- SADAKAT (OPSİYONEL)
012_create_shop_loyalty_points_table.php            🟡 OPSİYONEL
013_create_shop_loyalty_transactions_table.php      🟡 OPSİYONEL

-- SİSTEM
014_create_shop_settings_table.php                  ✅ MUTLAKA
```

**KESİN GEREKLI: 9 Tablo**
**OPSİYONEL: 4 Tablo**
**SORGULANMALI: 2 Tablo**

---

## ❓ KARAR VERİLMESİ GEREKENLER

### 1. shop_customers Tablosu Gerekli mi?

**MEVCUT DURUM:** `users` tablosu zaten var

**SEÇENEK A:** shop_customers KULLAN
```
✅ Avantajları:
- E-ticaret verileri users tablosunu kirletmez
- B2B için şirket bilgileri ayrı tutulabilir
- Misafir sipariş desteklenebilir

❌ Dezavantajları:
- Ekstra tablo
- users ile senkron tutulması gerekir
```

**SEÇENEK B:** users KULLAN
```
✅ Avantajları:
- Tek kullanıcı sistemi
- Basit, karmaşık değil

❌ Dezavantajları:
- users tablosu şişer
- Shop'a özel alanlar users'a eklenmeli
```

**TAVSİYE:** İlk fazda `users` kullan, gerekirse Faz 2'de shop_customers ekle

---

### 2. shop_customer_groups Gerekli mi?

**KULLANIM:** VIP, Toptan, Kurumsal müşteri segmentasyonu

```
🟡 İLK FAZDA GEREKLİ DEĞİL
✅ FAZ 2'DE EKLENEBİLİR (B2B için)
```

---

### 3. shop_membership_tiers Gerekli mi?

**KULLANIM:** Otomatik seviye sistemi (Bronze, Silver, Gold)

**FARK:**
- `subscription_plans`: Kullanıcı satın alır (₺99/ay)
- `membership_tiers`: Harcamaya göre otomatik (₺50K harcadı → Silver)

```
🤔 KARAR: Sadece ücretli üyelik mi? → membership_tiers GEREKMEZİLK FAZDA GEREKLİ DEĞİL
Hem ücretli hem otomatik seviye mi? → membership_tiers GEREKLI
```

**TAVSİYE:** İlk fazda ÇIKAR, gerekirse Faz 3'te ekle

---

## 🏆 FİNAL KARAR: FAZ 1 İÇİN MİNİMAL YAPISAL (9 Tablo)

```
✅ 001_shop_subscription_plans      // Üyelik paketleri
✅ 002_shop_subscriptions           // Aktif üyelikler
✅ 003_shop_payment_methods         // Ödeme yöntemleri
✅ 004_shop_payments                // Ödeme kayıtları
✅ 005_shop_orders                  // Sipariş kayıtları
✅ 006_shop_customer_addresses      // Fatura/Teslimat adresi
✅ 007_shop_coupons                 // Kupon sistemi
✅ 008_shop_coupon_usages           // Kullanım takibi
✅ 009_shop_settings                // Sistem ayarları

TOPLAM: 9 TABLO (FAZ 1)
```

**NOT:** `users` tablosu mevcut, shop_customers kullanmıyoruz (şimdilik)

---

## 🚀 SONRAKI ADIM

**Senin kararın hangisi?**

### SEÇENEK 1: MİNİMAL BAŞLA (9 Tablo)
```
✅ Hızlı başlangıç
✅ Sadece gerekli olanlar
✅ İhtiyaç oldukça ekle

→ 9 tablo ile Faz 1'i başlat
```

### SEÇENEK 2: GENİŞ BAŞLA (15 Tablo)
```
✅ Daha kapsamlı
✅ İleride genişletme kolay
🟡 Daha fazla kod

→ 15 tablo ile Faz 1'i başlat
```

### SEÇENEK 3: TÜM 43 TABLOYU OLUŞTUR
```
✅ Tüm sistem hazır
❌ Gereksiz tablolar da var
❌ Bakım maliyeti yüksek

→ 43 tablo ile tüm sistemi oluştur
```

### SEÇENEK 4: TÜM 66 TABLOYU OLUŞTUR
```
❌ Aşırı karmaşık
❌ Bakım zor
❌ Gereksiz tablolar çok

→ 66 tablo ile devam et
```

---

## 💡 BENİM ÖNERİM

**SEÇENEK 1: MİNİMAL BAŞLA (9 Tablo)** ⭐

**Neden?**
1. ✅ Hızlı geliştirme
2. ✅ Basit, anlaşılır
3. ✅ Üyelik satışına odaklanma
4. ✅ İhtiyaç oldukça genişlet
5. ✅ Gereksiz kod yok

**İlk 1 ayda:**
- Üyelik satışı çalışır hale gelir
- Ödeme entegrasyonu tamamlanır
- Kullanıcı deneyimi test edilir

**Sonra:**
- Ürün kataloğu eklenir (Faz 2)
- B2B özellikler eklenir (Faz 3)
- Kargo sistemi eklenir (Faz 4)

---

**Hangi seçeneği tercih edersin?**
