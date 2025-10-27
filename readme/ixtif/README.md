# 📘 İXTİF DOKÜMANTASYON

> **Tenant ID:** 2
> **Domain:** ixtif.com, ixtif.com.tr
> **Database:** tenant_ixtif
> **Son Güncelleme:** 2025-10-23

---

## 📚 Dökümanlar

### 1. **Marka Kimlik Dokümanlığı** 📄
**Dosya:** [`marka-kimlik.md`](./marka-kimlik.md)

**İçerik:**
- ✅ Kurumsal Kimlik (Şirket bilgileri, logo, slogan)
- ✅ Ürün & Hizmet Kataloğu (1,020 ürün, 106 kategori)
- ✅ Veritabanı Yapısı (Tablolar, modeller, örnekler)
- ✅ Marka Değerleri & Vizyon
- ✅ Teknik Altyapı (Frontend/Backend stack)
- ✅ İçerik Stratejisi (8 kategori)
- ✅ Sayfa Önerileri (Hakkımızda, Kariyer, İletişim, SSS, vb.)
- ✅ SEO & Pazarlama (Keyword, blog fikirleri, sosyal medya)
- ✅ Dinamik İçerik Sistemi (Settings helper)

**Kullanım:**
```bash
# Tüm marka bilgilerini görmek için:
cat readme/ixtif/marka-kimlik.md
```

---

### 2. **Settings Dinamik Kullanım Kılavuzu** ⚙️
**Dosya:** [`settings-kullanim.md`](./settings-kullanim.md)

**İçerik:**
- ✅ Settings Helper Kullanımı
- ✅ Blade Örnekleri
- ✅ WhatsApp Link Helper
- ✅ Component Örnekleri
- ✅ Full İletişim Sayfası Örneği
- ✅ Mevcut Settings Keys

**Kullanım:**
```blade
{{-- Blade dosyalarında --}}
{{ settings('contact_phone_1', '0216 755 3 555') }}
{{ settings('contact_whatsapp_1', '0532 216 07 54') }}
{{ settings('contact_email_1', 'info@ixtif.com') }}
```

---

## 🎯 Hızlı Başlangıç

### İletişim Bilgileri (Gerçek Veriler)

```
☎️ Telefon (Sabit): 0216 755 3 555
📱 Telefon (Mobil): 0501 005 67 58
💬 WhatsApp: 0532 216 07 54
📧 E-posta: info@ixtif.com
🌐 Web: www.ixtif.com
📸 Instagram: instagram.com/ixtifcom
👥 Facebook: facebook.com/ixtif
```

### Veritabanı Bilgileri

```
Database: tenant_ixtif
Tenant ID: 2
Domains: ixtif.com, ixtif.com.tr

Ürünler: 1,020 adet
Kategoriler: 106 adet (7 ana, 99 alt)
Marka: iXtif
Depo: İXTİF Tuzla Ana Depo
```

### Settings Keys

```php
// İletişim
contact_phone_1       // 0216 755 3 555
contact_phone_2       // 0501 005 67 58
contact_whatsapp_1    // 0532 216 07 54
contact_email_1       // info@ixtif.com
site_email            // info@ixtif.com
```

---

## 📁 Klasör Yapısı

```
readme/ixtif/
├── README.md                 # Bu dosya (index)
├── marka-kimlik.md          # Kapsamlı marka kimlik dokümanı
└── settings-kullanim.md     # Settings helper kullanım kılavuzu
```

---

## 🚀 Sonraki Adımlar

### Acil Öncelikler (1-2 Hafta)

- [ ] Hakkımızda sayfası oluştur
- [ ] İletişim sayfası oluştur (dinamik settings ile)
- [ ] SSS sayfası oluştur
- [ ] Hizmetler sayfası oluştur
- [ ] Kariyer sayfası oluştur
- [ ] Google My Business kayıt
- [ ] Sosyal medya hesapları aktif et

### Orta Vadeli (1-2 Ay)

- [ ] Blog modülü aktif et
- [ ] İlk 10 blog yazısı yayınla
- [ ] Referanslar sayfası ekle
- [ ] Sektörel çözümler sayfası
- [ ] Video içerikler üret
- [ ] E-mail marketing başlat
- [ ] WhatsApp Business entegre et

---

## 💡 Önemli Notlar

### ⚠️ VERİTABANI KORUMA

**BU GERÇEK CANLI SİSTEMDİR!**

❌ **ASLA YAPMA:**
- `php artisan migrate:fresh`
- `php artisan db:wipe`
- Manuel DELETE/DROP komutları
- Tenant database silme

✅ **ÖNCE KULLANICIYA SOR:**
- Veritabanına INSERT/UPDATE
- Mevcut kayıtları değiştirme
- Migration dosyası oluşturma

### 🎨 Cache & Build

**Tailwind/View değişikliğinden SONRA otomatik:**
```bash
php artisan view:clear
php artisan cache:clear
php artisan responsecache:clear
npm run prod
```

---

## 📞 Destek

**Sorularınız için:**
- Claude Code ile çalışıyorsanız → `CLAUDE.md` dosyasını okuyun
- Tenant yönetimi için → `readme/tenant-olusturma.md`
- Thumbmaker kullanımı için → `readme/thumbmaker/README.md`
- Settings kullanımı için → `readme/ixtif/settings-kullanim.md`

---

**📝 Not:** Bu dökümanlar sürekli güncellenmektedir. Yeni özellik ve içerikler eklendikçe güncellenecektir.

**Versiyon:** 1.0
**Son Güncelleme:** 2025-10-23
**Hazırlayan:** Claude Code (AI Assistant)
