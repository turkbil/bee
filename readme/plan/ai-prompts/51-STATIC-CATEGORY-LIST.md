# AI FEATURE KATEGORİLERİ - STATİK KALICI LİSTE

⚠️ **KRİTİK UYARI: Bu kategoriler kalıcıdır ve değiştirilmeyecektir!**

Bu kategoriler static ID'leri ile birlikte tanımlanmış olup, **asla değiştirilmeyecek, silinmeyecek veya ID'leri değiştirilmeyecektir**. Yeni projeler bu kategori sistemini baz alacaktır.

## Kategori Listesi (Static ID ile)

### 1. SEO ve Optimizasyon
- **ID**: 1
- **Slug**: `seo-optimizasyon`
- **Icon**: `fas fa-search`
- **Açıklama**: Arama motoru optimizasyonu ve web site performansı

### 2. İçerik Yazıcılığı
- **ID**: 2
- **Slug**: `icerik-yazicilik`
- **Icon**: `fas fa-pen-fancy`
- **Açıklama**: Blog, makale, sosyal medya içerik üretimi

### 3. Çeviri ve Lokalizasyon
- **ID**: 3
- **Slug**: `ceviri-lokalizasyon`
- **Icon**: `fas fa-language`
- **Açıklama**: Çoklu dil çeviri ve yerelleştirme hizmetleri

### 4. Pazarlama & Reklam
- **ID**: 4
- **Slug**: `pazarlama-reklam`
- **Icon**: `fas fa-bullhorn`
- **Açıklama**: Reklam metinleri, kampanya içerikleri, landing page

### 5. E-ticaret ve Satış
- **ID**: 5
- **Slug**: `e-ticaret-satis`
- **Icon**: `fas fa-shopping-cart`
- **Açıklama**: Ürün açıklamaları, satış metinleri, e-ticaret içerikleri

### 6. Sosyal Medya
- **ID**: 6
- **Slug**: `sosyal-medya`
- **Icon**: `fas fa-share-alt`
- **Açıklama**: Sosyal medya paylaşımları, hashtag önerileri, engagement

### 7. Email & İletişim
- **ID**: 7
- **Slug**: `email-iletisim`
- **Icon**: `fas fa-envelope`
- **Açıklama**: Newsletter, email marketing, iş iletişimi

### 8. Analiz ve Raporlama
- **ID**: 8
- **Slug**: `analiz-raporlama`
- **Icon**: `fas fa-chart-line`
- **Açıklama**: Veri analizi, rapor yazımı, istatistiksel değerlendirmeler

### 9. Müşteri Hizmetleri
- **ID**: 9
- **Slug**: `musteri-hizmetleri`
- **Icon**: `fas fa-headset`
- **Açıklama**: Müşteri yanıtları, destek metinleri, FAQ'lar

### 10. İş Geliştirme
- **ID**: 10
- **Slug**: `is-gelistirme`
- **Icon**: `fas fa-briefcase`
- **Açıklama**: İş planları, sunum metinleri, kurumsal içerikler

### 11. Araştırma & Pazar
- **ID**: 11
- **Slug**: `arastirma-pazar`
- **Icon**: `fas fa-chart-pie`
- **Açıklama**: Pazar araştırması, competitor analizi, survey

### 12. Yaratıcı İçerik
- **ID**: 12
- **Slug**: `yaratici-icerik`
- **Icon**: `fas fa-palette`
- **Açıklama**: Hikaye yazımı, yaratıcı metinler, senaryolar

### 13. Teknik Dokümantasyon
- **ID**: 13
- **Slug**: `teknik-dokumantasyon`
- **Icon**: `fas fa-book`
- **Açıklama**: API dokümantasyonu, kullanıcı kılavuzları, teknik açıklamalar

### 14. Kod & Yazılım
- **ID**: 14
- **Slug**: `kod-yazilim`
- **Icon**: `fas fa-laptop-code`
- **Açıklama**: API dokümantasyonu, kod açıklamaları, tutorial

### 15. Tasarım & UI/UX
- **ID**: 15
- **Slug**: `tasarim-ui-ux`
- **Icon**: `fas fa-paint-brush`
- **Açıklama**: Microcopy, error messages, UI metinleri

### 16. Eğitim ve Öğretim
- **ID**: 16
- **Slug**: `egitim-ogretim`
- **Icon**: `fas fa-graduation-cap`
- **Açıklama**: Eğitim materyalleri, kurs içerikleri, sınav soruları

### 17. Finans & İş
- **ID**: 17
- **Slug**: `finans-is`
- **Icon**: `fas fa-calculator`
- **Açıklama**: İş planları, finansal analiz, ROI raporları

### 18. Hukuki ve Uyumluluk
- **ID**: 18
- **Slug**: `hukuki-uyumluluk`
- **Icon**: `fas fa-gavel`
- **Açıklama**: Sözleşmeler, kullanım şartları, gizlilik politikaları

## Sistem Kuralları

1. **Static ID Sistemi**: Her kategori ID'si kalıcıdır (1-18)
2. **Değiştirilemez**: Kategoriler, ID'ler, slug'lar değiştirilemez
3. **Silinemez**: Hiçbir kategori silinemez
4. **Ekleme Yasak**: Yeni kategori eklenemez (sistem 18 ile sınırlı)
5. **FontAwesome Icons**: Tüm iconlar FontAwesome standardında
6. **Priority Order**: 1=En yüksek öncelik, 18=En düşük öncelik

## Teknik Bilgiler

- **Seeder**: `AIFeatureCategoriesSeeder.php`
- **Model**: `AIFeatureCategory`
- **Primary Key**: `ai_feature_category_id`
- **Table**: `ai_feature_categories`
- **Migration**: Central database (tüm tenant'larda aynı)
- **Status**: Tüm kategoriler varsayılan olarak aktif

## Database Durumu (2025-08-07)

✅ **18 kategori başarıyla eklendi**
✅ **Seeder çalıştırıldı**
✅ **Admin panel kategori yönetimi aktif**
✅ **Sortable sistem çalışıyor**

**Access URL**: `http://laravel.test/admin/ai/features/categories`

---

**NOT**: Bu kategoriler AI sistem mimarisinin temelini oluşturur. Değiştirilmesi durumunda tüm AI feature sisteminin çökmesi riski vardır.