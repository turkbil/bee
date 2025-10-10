# CLAUDE KURALLARI - NURULLAH'IN TEMEL ÇALIŞMA STANDARTLARI

## 🧠 **ULTRA DEEP THINK KURALI - EN ÖNEMLİ**
HER MESAJIMI HER ZAMAN ULTRA DEEP THINK DÜŞÜN VE ÖYLE HAREKET ET. ÖYLE KOD YAZ. ÖYLE YANIT VER. KAFANDAN UYDURMA. HER ZAMAN ÖRNEK VERİRSEM ONU DA ULTRA DEEP THINK İNCELE VE ANALİZ ET SONRA ONUN GİBİ KOD YAZ.

## 🔊 **KRİTİK: SES BİLDİRİM SİSTEMİ - EN ÖNCELİKLİ KURAL**
HER YANIT SONUNDA MUTLAKA SES ÇALIŞTIR:
```bash
say "tamamlandı"
```

## 🚨 **NURULLAH'IN ANA KURALLARI**

### **TEST KURALI - MUTLAKı**
- **ASLA test etmeden "çalışıyor" deme**
- **DAIMA "test et ve sonucu söyle" de**

### **OTOMATİK DEVAM PROTOKOLÜ**
- **Sorma, direk devam et**
- **Bash komutlarını çalıştır**

### **AI SEEDER TEST PROTOKOLÜ - ZORUNLU**
Seeder çalışırken bu komutla test yap:
```bash
php artisan app:clear-all && php artisan migrate:fresh --seed && php artisan module:clear-cache && php artisan responsecache:clear && php artisan telescope:clear
```
Bu hata vermiyorsa "tamamlandı" de ve CLAUDE.md'ye kaydet. 

### **SEEDER TEST DETAY PROTOKOLÜ**  
Bir seeder sayfası oluşturduğunda ya da güncellediğinde mutlaka test et. Testi aşağıdaki şekilde yapacaksın:

1. Yukarıdaki komutu çalıştıracaksın
2. Sonra laravel.log dosyasına bakacaksın  
3. Laravel.log dosyası hata vermeden çalışana kadar tekrarlayacaksın

### **OTOMATİK DOSYA TEMİZLEME**
- **Log dosyası** gönderildiğinde: Oku → Analiz et → Log'u boşalt → Dosyayı sil
- **Fotoğraf** gönderildiğinde: Oku → İşle → Dosyayı sil

### **İÇ KAYNAK KURALI - MUTLAK**
- **HİÇBİR DURUMDA** dış web sitesi/araç önerme
- **HER ŞEYİ** kendi sistemde çöz

## 🎯 **SİSTEM KURALLARI**

### **GENEL KURALLAR**
- **Türkçe yanıt ver**
- **HARDCODE kullanma** - sistem tamamen dinamik
- **"aferin", "bravo", "oldu"** gibi sonuclanma kelimesi kullandığımda → README.md'ye kaydet + Git'e kısa açıklamasıyla gönder ve yükle

### **ADMIN PANEL KURALLARI**
- **ÇOK ÖNEMLİ**: `bg-success`, `bg-danger`, `text-danger` gibi custom renkler **KULLANMA!** Dark modda sorun çıkarır
- Framework'ün kendi renk sistemini kullan

### **TASARIM KURALLARI**
- **Site Admin Panel**: Tabler.io + Bootstrap + jQuery + Livewire + FontAwesome
- **Site Frontend**: Alpine.js + Tailwind CSS  
- **Her ikisinde dark/light mod var!**
- **ÇOK ÖNEMLİ**: bg-success, bg-danger, text-danger gibi custom renkler **KULLANMA!** Dark modda sorun çıkarır
- Framework'ün kendi renk sistemini kullan

# NURULLAH'IN HAFıZASı - Otomatik Kayıt Sistemi

## KRİTİK SİSTEM BİLGİSİ

### Dil Sistemi (İKİ AYRI TABLO):
- **Admin**: `system_languages` + `admin_locale` session
- **Site**: `site_languages` + `site_locale` session  
- Karışık işlem yapma!

### Tenant Sistemi (ÇOK ÖNEMLİ):
- **Central Domain**: laravel.test (bu da bir tenant!)
- **Migrations**: 
  - `migrations/` → Central tablolar
  - `migrations/tenant/` → Tenant tablolar
- **Tenant tabloları central'da da var** (central da tenant olduğu için)
- **KRİTİK**: Add/remove migrate dosyası YAPMA! Create dosyasını düzenle
- **Neden**: Local çalışıyor, veriyi silebiliriz
- **UNUTMA**: Migration değiştiğinde → Seeder + Model + Controller + Blade + Component'ları da güncelle

# important-instruction-reminders
Do what has been asked; nothing more, nothing less.
NEVER create files unless they're absolutely necessary for achieving your goal.
ALWAYS prefer editing an existing file to creating a new one.
NEVER proactively create documentation files (*.md) or README files. Only create documentation files if explicitly requested by the User.