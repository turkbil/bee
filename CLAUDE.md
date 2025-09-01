Login: nurullah@nurullah.net / test
URL: www.laravel.test/login


hep extended think ve turkce reasonable dusun her zaman. ve hiç bir zaman manuel işlem saglama veritabanına.


# 🤖 KRİTİK AGENT PROTOKOLÜ
HER GELİŞTİRME SONRASI AGENT TEST ETSİN:
1. **say "tamamlandı"** → Agent otomatik test başlatsın
2. **"test et", "test", "kontrol et", "bak"** kelimeleri → Agent KESIN devreye girsin
3. **Söylemezsem de** → Kendi kendine test etmeli
4. **Söylersem** → MUTLAKA test etmeli
5. **Agent**: laravel.test/login → Giriş → Test sayfası → Screenshot
6. **Laravel.log** kontrolü → Hata varsa prompt ile Claude'a bildir
7. **Eş zamanlı çalışma**: Agent hata bulursa direkt Claude'a söylesin
7. **Sayfaya özel butonlar**: Agent her zaman sayfaya ait butonları da test etmeli, denemeleri yapmalı, on off yenilemeler vs dahil. Hepsini tek tek test etmeli linklerin.
8. **Bu agent test sistemi HER ZAMAN aktif olsun ve agent sadece kod analizi yapmasın, mutlaka GERÇEK TEST yapsın**

## 🎯 ROUTE & SAYFA STANDARTLARI
**YENI ROUTE = AGENT TEST:**
1. Her yeni route oluşturduğumda → Agent test etsin
2. **helper.blade.php** her admin sayfasının en tepesinde olsun
3. **Tablo yapısı** Page modülü index pattern'i ile birebir aynı
4. **Satır/kolonlar** modülün ihtiyacına göre özelleştir
5. **Bu standartlar her sayfa için geçerli**

## 🧪 MODÜL & SAYFA DETAY TEST
**YENİ MODÜL/SAYFA EKLEYİNCE AGENT YAPMALI:**
1. **Tüm bağlı dosyaları** test et (Controller, Model, View, Route)
2. **helper.blade.php** butonlarının tamamını tıkla ve test et
3. **Her buton fonksiyonunu** tek tek dene ve çalıştığını kontrol et
4. **CRUD işlemleri** (Create, Read, Update, Delete) tam test
5. **Form validasyonları** çalışıyor mu test et
6. **Ajax istekleri** ve **Livewire** componentler çalışıyor mu
7. **Dil dosyaları** yükleniyor mu kontrol et
8. **Database bağlantısı** ve sorguları test et
9. **Bu detay test HER YENİ SAYFA için zorunlu**

## 🗑️ DOSYA TEMİZLEME PROTOKOLÜ
**LOG & FOTOĞRAF OTOMATIK TEMİZLEME:**
1. **Log dosyası** gönderildiğinde → Oku → Analiz et → Boşalt → Sil
2. **Fotoğraf** gönderildiğinde → Oku → İşle → Sil  
3. **Her işlem sonrası** dosyalar otomatik temizlenir
4. **Bu protokol HER ZAMAN aktif olsun**

# NURULLAH'IN CLAUDE KURALLARI

## 🚨 TEMEL KURALLAR

### 🚫 KRİTİK: MANUEL QUEUE İŞLEMİ YASAK
- **ASLA** manuel queue worker başlatma
- **ASLA** `php artisan queue:work` komutu çalıştırma
- **SADECE** log temizleme yapabilirsin: `truncate -s 0 laravel.log`
- Queue sistemleri otomatik çalışmalı
- Sistem deneme/test sırasında bu kurala sıkı uyulacak

### 🤖 OTOMATİK QUEUE YÖNETİM SİSTEMİ
**KESİN ÇÖZÜM - SUNUCU HAZIR:**
- ✅ **QueueHealthService**: Otomatik health check & repair
- ✅ **AutoQueueHealthCheck Middleware**: Admin sayfa yüklerken kontrol
- ✅ **php artisan queue:health-check**: Manuel komut mevcut
- ✅ **Syntax error detection**: Kritik dosya kontrolü
- ✅ **Failed jobs auto-clear**: Otomatik temizleme
- ✅ **Zero manual intervention**: Tamamen otomatik sistem

### ULTRA DEEP THINK
HER MESAJIMI ULTRA DEEP THINK DÜŞÜN VE HAREKET ET.
REASONING DE YAP Kİ NE DÜŞÜNDÜĞÜNÜM BİLEYİM VE İLHAM ALAYIM SENİN ENGİN TECRÜBELERİNDEN.

### SES BİLDİRİM
```bash
say "tamamlandı"
```

### TEST PROTOKOLÜ
```bash
php artisan app:clear-all && php artisan migrate:fresh --seed && php artisan module:clear-cache && php artisan responsecache:clear && php artisan telescope:clear
```



Seeder ve migrate sayfalarında asla manuel işlem yapmayacaksın.
Onlar için migrate ve seder dosyalarını düzenleyeceksin. 
Bir seeder veya migration sayfası olusturdugunda ya da güncellediğinde mutlaka test et. Testi asagıdaki şekilde yapacaksın: 

php artisan app:clear-all && php artisan migrate:fresh --seed && php artisan module:clear-cache && php artisan responsecache:clear && php artisan telescope:clear

bu komutu calıstıracak. sonra laravel.log dosyasına bakacaksın. laravel.log dosyası hata vermeden calısana kadar tekrarlayacaksın.



### OTOMATİK DEVAM
- Sorma, direk devam et
- Bash komutlarını çalıştır

### DOSYA TEMİZLEME
- Log gönderirsen: Oku → Analiz et → Boşalt → Sil
- Fotoğraf gönderirsen: Oku → İşle → Sil

### SİSTEM KURALLARI
- Türkçe yanıt ver
- HARDCODE yok - dinamik sistem
- "tamamlandı" dediğimde → README.md'ye kaydet + Git'e gönder
- Custom renkler kullanma (dark mod hatası)

### TASARIM KURALLARI
- **Admin**: Tabler.io + Bootstrap + Livewire
- **Frontend**: Alpine.js + Tailwind CSS
- Framework renk sistemi kullan

---

## 💾 HAFIZA

### DİL SİSTEMİ (2 TABLO)
- **Admin**: `system_languages` + `admin_locale`
- **Site**: `site_languages` + `site_locale`

### TENANT SİSTEMİ
- **Central**: laravel.test (bu da tenant!)
- **Migrations**: central/tenant ayrımı
- **KRİTİK**: Create dosyasını düzenle, add/remove yapma

### PAGE PATTERN = MASTER
- Page modülü standart şablon
- Yeni modüller Page pattern alır
- JSON çoklu dil + SEO + Modern PHP

---

## 📋 BAŞARI KAYITLARI

### JSON DİL TEMİZLEME (16.08) ✅
43 kayıt temizlendi - otomatik sistem

### 🚀 ENTERPRISE AI QUEUE SYSTEM v2.0 (26.08) ✅
**KRİTİK SORUNLAR TAMAMEN ÇÖZÜLDü:**
- ✅ 75% Progress Takılma Sorunu → TranslatePageJob + Queue Worker
- ✅ Modal Backdrop Temizleme → Enhanced cleanup system
- ✅ Manual İşlem Bağımlılığı → Docker Supervisor + Auto scripts
- ✅ Rate Limiting Eksikliği → Laravel Queue Middleware stack

**ENTERPRISE ÖZELLİKLER:**
- 🛡️ WithoutOverlapping, RateLimited, ThrottlesExceptions
- 🎯 Tenant isolated queue system
- 📊 Real-time progress tracking
- 🚨 Critical error detection + admin notifications
- ⚡ Multi-queue support (tenant_isolated, critical)

**PERMANENT SOLUTION:** Zero manual intervention required

### AI ÇEVİRİ TAMİRATI (14.08) ✅  
OpenAI response parsing fix - 3/3 test başarılı

### UNIVERSAL INPUT V3 (10.08) ✅
8 Service + 246 route + 8 tablo aktif

### AI SEEDER (09.08) ✅
74 feature + 3 phase yapısı

---

## 📚 DETAYLAR
- 📖 `claude_modulpattern.md` - Pattern rehberi
- 🤖 `claude_ai.md` - AI sistem detayları
