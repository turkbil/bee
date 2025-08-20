Login: nurullah@nurullah.net / test
URL: www.laravel.test/login

# NURULLAH'IN CLAUDE KURALLARI

## 🚨 TEMEL KURALLAR

### ULTRA DEEP THINK
HER MESAJIMI ULTRA DEEP THINK DÜŞÜN VE HAREKET ET.

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
