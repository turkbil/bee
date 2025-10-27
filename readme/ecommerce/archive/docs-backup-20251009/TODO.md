# ✅ SHOP MODULE - TODO CHECKLIST

## 📍 MEVCUT DURUM
- ✅ 66 Migration dosyası oluşturuldu (`readme/ecommerce/migrations/`)
- ✅ Portfolio pattern seçildi (kategori sistemi + repository + Livewire)
- ✅ Üyelik sistemi ödeme bazlı güncellendi (Spotify/Netflix modeli)
- ✅ 5 Ürün JSON dosyası hazır (CPD15TVL, CPD18TVL, CPD20TVL, EST122, F4)
- ✅ **TAMAMLANDI (10 Ocak 2025):**
  - 66 Migration dosyası standardize edildi (Portfolio pattern'e göre)
  - name → title, string slug → json slug dönüşümü yapıldı
  - SEO kolonları kaldırıldı (Universal SEO'ya geçiş)
  - Primary key'ler anlamlı isimler aldı
  - JSON slug indexes eklendi (MySQL 8.0+ / MariaDB 10.5+)
  - Sektörel comment'ler genel örneklere çevrildi
  - "vs." ile dinamik dil desteği eklendi
- ⏳ **ŞİMDİ:** Portfolio → Shop klonlama

---

## 🚀 PHASE 1: MODÜL OLUŞTURMA (30 dk) - **ŞİMDİ**

- [ ] **1.1:** `./module.sh` çalıştır (Portfolio → Shop)
- [ ] **1.2:** Migration dosyalarını taşı (`readme/ecommerce/migrations/` → `Modules/Shop/Database/migrations/`)
- [ ] **1.3:** Migration dosya isimlerini Laravel formatına çevir (`2025_01_10_000001_...`)
- [ ] **1.4:** `php artisan migrate` çalıştır
- [ ] **1.5:** Migration durumunu test et
- [ ] **1.6:** İlk commit at

---

## 🔧 PHASE 2: TEMEL MODELLER (3-4 saat)

- [ ] **2.1:** Category model (relations, casts, scopes, getFullPath)
- [ ] **2.2:** Brand model
- [ ] **2.3:** Tag model (polymorphic)
- [ ] **2.4:** Attribute model
- [ ] **2.5:** Warehouse model
- [ ] **2.6:** Product model + traits (HasReviews, HasViewCounter, HasTags, HasRatings, ActivityLoggable)
- [ ] **2.7:** ProductVariant model
- [ ] **2.8:** ProductImage model
- [ ] **2.9:** Customer model + subscription methods (hasActiveSubscription, isPremium)

---

## 📦 PHASE 3: REPOSITORY PATTERN (4-5 saat)

- [ ] **3.1:** ProductRepositoryInterface
- [ ] **3.2:** ProductRepository (all, find, findBySlug, search)
- [ ] **3.3:** CategoryRepositoryInterface
- [ ] **3.4:** CategoryRepository (tree, roots, reorder)
- [ ] **3.5:** CustomerRepositoryInterface
- [ ] **3.6:** CustomerRepository
- [ ] **3.7:** OrderRepositoryInterface
- [ ] **3.8:** OrderRepository
- [ ] **3.9:** ShopServiceProvider binding'leri ekle

---

## 🎭 PHASE 4: TRAITS (2-3 saat)

- [ ] **4.1:** HasReviews trait (reviews relation, averageRating, updateRatingCache)
- [ ] **4.2:** HasViewCounter trait (incrementViews, incrementCartAdds, incrementSales)
- [ ] **4.3:** HasRatings trait (overall + category ratings)
- [ ] **4.4:** HasTags trait (morphToMany tags)
- [ ] **4.5:** ActivityLoggable observer

**NOT: SEO Yönetimi**
- SEO ayarları tablo bazlı değil, Universal SEO sistemi ile yönetilir
- SeoManagement modülü üzerinden tüm entity'lere (Product, Category, Brand, vb.) SEO desteği sağlanır
- Model'larda SEO kolonları bulunmaz (seo_title, seo_description, seo_keywords)

---

## 🛣️ PHASE 5: ROUTES (2-3 saat)

- [ ] **5.1:** Admin routes (`routes/admin.php`)
  - Category CRUD
  - Brand CRUD
  - Product CRUD
  - Customer list/detail
  - Order list/detail
- [ ] **5.2:** Site routes (`routes/web.php`)
  - Product listing
  - Product detail
  - Cart
  - Subscription pages
- [ ] **5.3:** SubscriptionMiddleware oluştur
- [ ] **5.4:** Middleware'i `app/Http/Kernel.php`'ye kaydet

---

## 💳 PHASE 6: ÜYELİK SİSTEMİ (3-4 saat)

- [ ] **6.1:** Subscription model (isActive, renew, cancel, isExpired)
- [ ] **6.2:** SubscriptionPlan model (getFeatures, getPrice)
- [ ] **6.3:** Customer model subscription methods
- [ ] **6.4:** SubscriptionController (plans, subscribe, payment, callback, manage, cancel)
- [ ] **6.5:** Payment Gateway entegrasyonu (iyzico/PayTR/Stripe)
- [ ] **6.6:** SubscriptionSeeder (Bronze, Premium paketler)
- [ ] **6.7:** Cron Jobs (RenewSubscriptions, SubscriptionReminders)
- [ ] **6.8:** Üyelik view'ları (plans, payment, success, manage)
- [ ] **6.9:** E-posta şablonları (activated, renewed, failed, expiring)

---

## 🖥️ PHASE 7: LIVEWIRE ADMIN (1 hafta)

**Ürün Yönetimi:**
- [ ] **7.1:** ProductList component (table, filter, sort, pagination, bulk)
- [ ] **7.2:** ProductCreate component (8-tab form)
- [ ] **7.3:** ProductEdit component
- [ ] **7.4:** VariantManager component
- [ ] **7.5:** ImageManager component (upload, reorder, delete)
- [ ] **7.6:** CategoryTree component (drag-drop, nested)
- [ ] **7.7:** CategoryForm component

**Sipariş Yönetimi:**
- [ ] **7.8:** OrderList component
- [ ] **7.9:** OrderDetail component (timeline, status update, invoice)

---

## 🌐 PHASE 8: FRONTEND (2 hafta)

**Katalog:**
- [ ] **8.1:** Product Listing (grid/list, filter, sort, pagination)
- [ ] **8.2:** Product Detail (gallery, variant, cart, reviews, related)

**Sepet & Sipariş:**
- [ ] **8.3:** Cart & Checkout (mini cart, cart page, multi-step checkout)

**Hesabım:**
- [ ] **8.4:** My Account (dashboard, orders, addresses, wishlist, subscription)

**Diğer:**
- [ ] **8.5:** Search (autocomplete, results page)
- [ ] **8.6:** Subscription Pages (plans, payment, manage, content lock)
- [ ] **8.7:** Static Pages (homepage, about, contact, faq)

---

## 🧪 PHASE 9: TEST & OPTİMİZASYON (3-4 gün)

- [ ] **9.1:** Unit testler (model, repository, trait)
- [ ] **9.2:** Feature testler (route, controller, livewire)
- [ ] **9.3:** Browser testler (Dusk - sepet, checkout flow)
- [ ] **9.4:** Performance optimizasyon (eager loading, cache, index)
- [ ] **9.5:** Security audit

---

## 📖 PHASE 10: DOKÜMANTASYON (1-2 gün)

- [ ] **10.1:** README.md (module features, installation)
- [ ] **10.2:** API Documentation
- [ ] **10.3:** Admin user guide

---

## 🚀 PHASE 11: DEPLOYMENT (1 gün)

- [ ] **11.1:** Production deployment
- [ ] **11.2:** Database migration on production
- [ ] **11.3:** Performance testing
- [ ] **11.4:** Final security check

---

## 📊 İLERLEME ÖZET

**Tamamlanan:** 5/75 (%7)
**Aktif Fase:** PHASE 1 (Modül Oluşturma)
**Sonraki:** PHASE 2 (Temel Modeller)
**Tahmini Süre:** 4-5 hafta toplam

---

## 🎯 HANGİ ADIMDA?

✅ Migration dosyaları hazır
✅ Pattern seçildi (Portfolio)
✅ Üyelik sistemi planlandı
⏳ **ŞİMDİ:** module.sh ile Shop modülü oluşturuluyor
⏭️ **SONRA:** Migration'ları taşı ve Laravel formatına çevir
