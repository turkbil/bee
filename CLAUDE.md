# 🤖 CLAUDE ÇALIŞMA TALİMATLARI

**Proje Giriş**: nurullah@nurullah.net / test  
**URL**: www.laravel.test/login

---

## 📋 ÇALIŞMA YÖNTEMİ

### 🧠 TEMEL YAKLAŞIM
- **Extended Think**: Her mesajı ultra deep analiz et, reasoning yap
- **Türkçe İletişim**: Daima Türkçe yanıt ver
- **Otomatik Devam**: Sorma, direkt hareket et
- **Asla manuel işlem yapma veritabanına**

### 📝 ÖNEMLİ NOT
Bu dosya **sadece çalışma yöntemi ve temel talimatları** içerir. 
**Detaylı teknik dökümanlar**: `readme/claude-docs/` klasöründe

---

## 🤖 KRİTİK AGENT PROTOKOLÜ

### AGENT TEST SİSTEMİ
1. **"test et", "test", "kontrol et", "bak"** → Agent KESIN devreye gir
2. **Agent**: laravel.test/login → Giriş → Test sayfası → Screenshot
3. **Laravel.log** kontrolü → Hata varsa Claude'a bildir
4. **Sayfaya özel butonların TAMAMINI test et**
5. **Sadece kullanıcı "test et" dediğinde çalışır**

---

## 🚨 KRİTİK KURALLAR

### 🚫 MANUEL QUEUE İŞLEMİ YASAK
- **ASLA** `php artisan queue:work` çalıştırma
- **SADECE** log temizleme: `truncate -s 0 laravel.log`
- Queue sistemleri tamamen otomatik

### 🎯 SAYFA STANDARTLARI
- Her admin sayfasının tepesinde **helper.blade.php**
- **Tablo yapısı**: Page modülü pattern'i ile aynı
- **Yeni route** = Agent test etsin

### 🧪 TEST PROTOKOLÜ
Migration/Seeder sonrası:
```bash
php artisan app:clear-all && php artisan migrate:fresh --seed && php artisan module:clear-cache && php artisan responsecache:clear && php artisan telescope:clear
```

### 🗑️ DOSYA TEMİZLEME
- **Log/Fotoğraf** gönderirsen: Oku → Analiz et → Boşalt → Sil
- **Otomatik temizlik** her işlem sonrası

---

## 🎨 TASARIM STANDARTLARI

- **Admin**: Tabler.io + Bootstrap + Livewire
- **Frontend**: Alpine.js + Tailwind CSS  
- **Framework renkleri kullan** (custom renk yok)

---

## 💾 SİSTEM HAFIZASI

### DİL SİSTEMİ
- **Admin**: `system_languages` + `admin_locale`
- **Site**: `site_languages` + `site_locale`

### PATTERN SİSTEMİ
- **Page Pattern = Master**: Yeni modüller Page pattern'i alır
- **JSON çoklu dil + SEO + Modern PHP**

---

## 📚 DETAYLI DÖKÜMANLAR

Teknik detaylar için: `readme/claude-docs/`
- `claude_ai.md` - AI sistemleri rehberi
- `claude_kurallari.md` - Eski çalışma kuralları
- `claude_modulpattern.md` - Modül geliştirme pattern'leri
- `claude_proje.md` - Proje mimarisi detayları