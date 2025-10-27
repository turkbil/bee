# 🚨 GTM DASHBOARD UYARILARI - ÇÖZÜM REHBERİ

**Tarih:** 2025-10-26
**Container ID:** GTM-P8HKHCG9

---

## ⚠️ MEVCUT UYARILAR

GTM Dashboard'da görünen 2 ana sorun:

### 1. **"Sayfalarınızdan bazıları etiketlenmemiş"**
> Etiketinizin sitenizdeki tüm sayfalara eklenmesi kapsamlı ölçüm için önemlidir.

### 2. **"Yapılandırma için ek alan adları algılandı"**
> Etiketinizin algılandığı alanları yapılandırmanıza eklemeniz gerekebilir. Bu durum etiketinizin dayanıklılığını ve dönüşüm ölçümünü etkileyebilir.

---

## ✅ ÇÖZÜMLER

### 🔧 1. "Etiketlenmemiş Sayfalar" Sorunu

#### Neden Oluşuyor?
- GTM, tüm sayfaları henüz taramadı
- Admin panel sayfaları login gerektiği için GTM tarafından tespit edilemiyor
- Static HTML sayfalar yeni eklendi, GTM henüz keşfetmedi

#### ✅ Çözüm Adımları:

**A) GTM Preview Mode ile Test Et (Anında Çözüm)**

1. **GTM Dashboard → Preview Butonuna Tıkla**
   ```
   https://tagmanager.google.com/#/container/accounts/XXXXX/containers/XXXXXX/workspaces/X
   ```

2. **URL'leri Manuel Test Et:**
   ```
   https://ixtif.com
   https://ixtif.com/admin/shop (login gerekli)
   https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html
   ```

3. **Tag Assistant Açılır:**
   - ✅ "Tags Fired" bölümünde etiketlerin çalıştığını gör
   - ✅ Her sayfada GTM yükleniyor olmalı

**B) Bekle (24-48 Saat)**
- GTM otomatik olarak siteyi tarayacak
- Yeni sayfalar "Tag Coverage" raporunda görünecek

**C) Sitemaps Ekle (Google Search Console)**
```xml
https://ixtif.com/sitemap.xml
```
- Google Search Console → Sitemaps → Ekle
- GTM bu sitemap'i kullanarak sayfaları keşfeder

---

### 🌐 2. "Ek Alan Adları Algılandı" Sorunu

#### Neden Oluşuyor?
Multi-tenant sisteminiz var:
- `tuufi.com` (central)
- `ixtif.com` (tenant 2)
- `ixtif.com.tr` (tenant 2)

GTM bu 3 domain'i tespit etti ama cross-domain tracking yapılandırılmamış.

#### ✅ Çözüm: Cross-Domain Tracking Ayarı

**ADIM 1: Değişken Oluştur**

1. **GTM Dashboard → Variables (Değişkenler)**
2. **User-Defined Variables → New**
3. **Variable Configuration:**
   - Tip: **Constant**
   - Name: `Cross Domain List`
   - Value:
     ```
     tuufi.com,ixtif.com,ixtif.com.tr
     ```
4. **Save**

---

**ADIM 2: Google Analytics 4 Configuration Tag'ini Güncelle**

1. **GTM Dashboard → Tags (Etiketler)**
2. **Google Analytics 4 Configuration** tag'ini bul
3. **Edit (Düzenle)**
4. **Configuration Settings → Fields to Set:**

   | Field Name | Value |
   |------------|-------|
   | `linker` | `{"domains":["tuufi.com","ixtif.com","ixtif.com.tr"]}` |

5. **Advanced Settings → Cross-Domain Tracking:**
   - **Enable auto-link domains:** ✅
   - **Auto-link domains:** `tuufi.com,ixtif.com,ixtif.com.tr`

6. **Save**

---

**ADIM 3: Google Ads Conversion Linker'ı Kontrol Et**

1. **GTM Dashboard → Tags → Google Ads - Conversion Linker**
2. **Triggering (Tetikleyici):**
   - ✅ **All Pages** olmalı
   - ❌ Belirli sayfa sınırlaması OLMAMALI

3. **Advanced Settings:**
   - **Enable cross-domain tracking:** ✅
   - **Accept incoming linker parameters:** ✅
   - **Decorate forms:** ✅

4. **Save**

---

**ADIM 4: Container Settings'i Güncelle**

1. **GTM Dashboard → Admin (Sol üst) → Container Settings**
2. **Additional Settings → Domains:**
   - `tuufi.com`
   - `ixtif.com`
   - `ixtif.com.tr`
   - `www.tuufi.com` (varsa)
   - `www.ixtif.com` (varsa)
   - `www.ixtif.com.tr` (varsa)

3. **Save**

---

**ADIM 5: Publish**

1. **GTM Dashboard → Submit (Sağ üst)**
2. **Version Name:**
   ```
   Cross-domain tracking + Admin panel integration
   ```
3. **Version Description:**
   ```
   - Added cross-domain tracking for tuufi.com, ixtif.com, ixtif.com.tr
   - Updated Google Ads Conversion Linker trigger
   - Fixed tag coverage issues
   ```
4. **Publish**

---

## 🧪 DOĞRULAMA

### Test 1: Cross-Domain Tracking
1. **GTM Preview Mode Aç**
2. **ixtif.com → ixtif.com.tr arası geçiş yap**
3. **URL'de `_gl=` parametresi görmeli** (linker parameter)
   ```
   https://ixtif.com.tr/?_gl=1*abc123...
   ```
4. ✅ Başarılı: Aynı session ID korunur

### Test 2: Tag Coverage
1. **GTM Dashboard → Workspace → Tag Coverage**
2. **URL'leri Test Et:**
   - `ixtif.com/admin/shop`
   - `ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html`
3. ✅ Artık "tagged" olarak görünmeli

### Test 3: Google Analytics
1. **GA4 → Realtime**
2. **Multi-domain ziyaret yap** (ixtif.com → ixtif.com.tr)
3. ✅ Tek session olarak izlenmeli

---

## 📊 SONUÇ BEKLENTİLERİ

### Hemen Sonra:
- ✅ Preview Mode'da tüm sayfalar etiketli görünür
- ✅ Cross-domain parametreleri URL'de görünür

### 24-48 Saat Sonra:
- ✅ Tag Coverage raporu güncellenecek
- ✅ "Etiketlenmemiş sayfalar" uyarısı azalacak/kaybolacak
- ✅ "Ek alan adları" uyarısı kaybolacak

### 1 Hafta Sonra:
- ✅ GA4 raporlarında doğru cross-domain tracking
- ✅ Conversion tracking düzgün çalışır
- ✅ Multi-domain user journey görünür

---

## ⚠️ ÖNEMLİ NOTLAR

### Admin Panel Sayfaları
- Admin panel login gerektirir
- GTM otomatik tarayamaz
- **Çözüm:** Preview Mode ile manuel test et

### Static HTML Dosyaları
- Dinamik sistem sayesinde GTM eklendi
- GTM'in bu sayfaları keşfetmesi 24-48 saat alabilir
- **Hızlandırmak için:** Sitemap'e ekle

### Multi-Tenant Yapısı
- Her tenant kendi GTM ID'sine sahip olabilir
- Tenant 2: GTM-P8HKHCG9
- Tenant 3: Farklı ID kullanabilir (opsiyonel)

---

## 🔍 SORUN GİDERME

### "Uyarılar hala görünüyor"
**Çözüm:** 24-48 saat bekle, GTM cache'i temizlenir

### "Cross-domain tracking çalışmıyor"
**Kontrol:**
```bash
# URL'de _gl parametresi var mı?
curl -I https://ixtif.com | grep -i "location"
```

### "Tag Coverage boş"
**Çözüm:**
1. Preview Mode ile manuel test et
2. Sitemap ekle (Google Search Console)
3. 48 saat bekle

---

## 📚 EK KAYNAKLAR

**Google Dökümanları:**
- [Cross-Domain Tracking](https://support.google.com/tagmanager/answer/7549390)
- [Tag Coverage](https://support.google.com/tagmanager/answer/9708549)
- [Google Ads Linker](https://support.google.com/tagmanager/answer/7549390?hl=tr#zippy=%2Cweb)

**Bizim Dökümanlar:**
- [GTM Yapılandırma Rehberi](./GTM-YAPILANDIRMA-REHBERI.md)
- [GTM Tam Kontrol Listesi](./GTM-TAM-KONTROL-LISTESI.md)

---

## ✅ KONTROL LİSTESİ

**GTM Dashboard'da yapılacaklar:**
- [ ] Cross Domain List değişkeni oluşturuldu
- [ ] GA4 Configuration tag'ine linker eklendi
- [ ] Google Ads Conversion Linker tetikleyicisi "All Pages"
- [ ] Container Settings'e tüm domain'ler eklendi
- [ ] Container publish edildi
- [ ] Preview Mode ile test edildi
- [ ] Tag Coverage kontrol edildi (48 saat sonra)

**Tamamlandığında:** 🎉 GTM sistemi %100 çalışır durumda!

---

**Hazırlayan:** Claude AI
**Son Güncelleme:** 2025-10-26
