# 🧠 F4 201 Transpalet İçin AI Prompt Taslağı

Bu taslak, PDF → JSON/SQ L dönüşümü yapan yapay zekaya verilerek F4 201 ürününü Phase 1 standartlarında çıkarması için kullanılabilir. Tüm metinler Türkçe olmalı, `en` alanları Türkçe içeriğin birebir kopyası olacak.

---

## 🎯 Görev

```
GöREV: /Users/nurullah/Desktop/cms/EP PDF/2-Transpalet/F4 201/02_F4-201-brochure-CE.pdf
PDF'ini analiz et ve aşağıdaki kurallara göre Phase 1 formatında ürün JSON'u ve SQL INSERT taslağı üret.
```

### Zorunlu Alanlar
- `product_info`, `basic_data`, `category_brand`, `pricing`, `inventory`
- `technical_specs`, `features`, `highlighted_features`
- Yeni alanlar: `use_cases`, `competitive_advantages`, `target_industries`, `primary_specs`, `faq_data`

### Dil ve Pazarlama Kuralları
1. Tüm içerikler %100 Türkçe olmalı. `en` alanları Türkçe metnin aynısı.
2. `long_description` iki bloktan oluşmalı:
   - `<section class="marketing-intro">` → abartılı, duygusal satış açılışı
   - `<section class="marketing-body">` → teknik faydalar, garanti, iletişim, **SEO anahtar kelimeleri** listesi
3. SEO anahtar kelimelerini mutlaka geçir:
   - `F4 201 transpalet`
   - `48V Li-Ion transpalet`
   - `2 ton akülü transpalet`
   - `İXTİF transpalet`
   - `dar koridor transpalet`
4. İXTİF’in **ikinci el, kiralık, yedek parça ve teknik servis** programlarına mutlaka değin (özellikle marketing body, features, competitive advantages ve FAQ’da).
5. İletişim satırında telefon `0216 755 3 555`, e-posta `info@ixtif.com` kullanılacak.
6. `short_description` ve `features` içinde de bu anahtar kelimelerden mümkün olduğunca kullan.
7. Üretilen içerik hem ürün detay sayfası hem de bağımsız landing page olarak kullanılabilir olmalı; CTA, bölümlere ayrılmış storytelling ve satış odaklı akış üret.
8. Tüm anlatımı son kullanıcıyı hedefleyerek yap; konteyner dizilimi, toplu sevkiyat, wholesale packaging gibi B2B detaylara yer verme.
9. `primary_specs` alanında ürün tipine göre dört kart üret (transpaletler: Denge Tekeri, Li-Ion Akü, Şarj Cihazı, Standart Çatal. Forkliftler: Asansör, Li-Ion Akü, Şarj Cihazı, Raf Aralığı. İstif makineleri: Asansör, Akü, Şarj Cihazı, Çatal. Değerleri ürün datasından doldur).
10. `features` alanını `{ list: [...], branding: { slogan, motto, technical_summary } }` yapısında tut.
11. `target_industries` en az 20 sektör barındırmalı.
12. `use_cases` en az 6 sektör bazlı senaryo, `competitive_advantages` en az 5 ölçülebilir avantaj ve `faq_data` en az 10 soru-cevap içermeli; tüm cevaplar detaylı ve ikna edici olmalı.

### Teknik Spesifikasyonlar
- Tablo verileri PDF'deki rakamlara göre doldurulmalı (mm, kg, kW vb. birimler korunur).
- `charger_options`, `battery_system`, `tyres` vb. alanlar Türkçe anlatım içermeli.
- JSON içindeki tüm `note` alanları da Türkçe yazılacak.

### SQL Üretimi
- `shop_products`, `shop_brands`, `shop_categories`, `shop_product_variants`, `shop_settings` sıralamasına uyan tek bir SQL dosyası üret.
- `JSON_OBJECT` içindeki tüm metinler Türkçe. `en` değerleri `tr` ile aynı.
- `long_description` HTML olarak JSON içine gömülecek.
- `faq_data`, `use_cases`, `competitive_advantages`, `target_industries` gibi alanlar JSON olarak `shop_products` tablosuna eklenmeli.

---

## 🧾 İPUÇLARI
- Pazarlama tonunda emoji kullanımı serbest (özellikle `long_description` ve `features` için).
- Duygusal tetikleyiciler: “prestij”, “şampiyon”, “hız rekoru”, “yatırımınızın vitrini”.
- AI’nın senaryoyu daha iyi anlaması için PDF’den çektiği teknik tabloları bullet listesinde tekrar kullan.
- Çıktıyı üretmeden önce, tüm İngilizce kelimelerin Türkçe karşılıklarına çevrildiğinden emin ol.

---

## ✅ Kontrol Listesi
- [ ] `long_description` iki HTML section ile başlıyor mu?
- [ ] SEO anahtar kelimeleri hem kısa hem uzun açıklamada geçiyor mu?
- [ ] `use_cases` ≥ 6, `competitive_advantages` ≥ 5, `target_industries` ≥ 20, `faq_data` ≥ 10 mı?
- [ ] Tüm `en` alanları Türkçe metni aynen taşıyor mu?
- [ ] Teknik spesifikasyon değerleri PDF ile uyumlu mu?
- [ ] SQL dosyası; marka, kategori, ürün, varyant, ayar sıralamasında mı?

Bu prompt `.md` dosyası AI operatörleri / otomasyonlar tarafından direkt kullanılabilir. Savunma hattı: “Tüm metinler Türkçe, `en` alanı Türkçe kopya, SEO kelimeleri unutulmayacak.” 

Hazırsan F4 201 transpaletiyle depoda yeni bir vitrin açıyoruz! 🚀
