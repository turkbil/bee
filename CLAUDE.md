# 🤖 CLAUDE ÇALIŞMA TALİMATLARI

**Proje Giriş**: nurullah@nurullah.net / test  
**URL**: www.laravel.test/login


işlemler bittikten sonra tamamlandığına dair siri ile seslendir.




---
Standard Workflow

First think through the problem, read the codebase for relevant files, and write a plan to claudeguncel.md

The plan should have a list of todo items that you can check off as you complete them

Before you begin working, check in with me and I will verify the plan

Then, begin working on the todo items, marking them as complete as you go

Please every step of the way just give me a high level explanation of what changes you made

Make every task and code change you do as simple as possible. We want to avoid making any massive or complex changes. Every change should impact as little code as possible. Everything is about simplicity

Finally, add a review section to the projectplan.md file with a summary of the changes you made and any other relevant information


- Önce sorunları iyice düşünün
- Planları claudeguncel.md 'e yaz
- Başlamadan önce giriş yapın
- Yapılacaklar'ı tamamlanmış olarak işaretle
- Değişiklikleri basit tutun


## 📋 ÇALIŞMA YÖNTEMİ

### 🧠 TEMEL YAKLAŞIM
- **Extended Think**: Her mesajı ultra deep analiz et, reasoning yap
- **Türkçe İletişim**: Daima Türkçe yanıt ver
- **Otomatik Devam**: Sorma, direkt hareket et
- **Asla manuel işlem yapma veritabanına**

### 📝 ÖNEMLİ NOT
Bu dosya **sadece çalışma yöntemi ve temel talimatları** içerir. 
**Detaylı teknik dökümanlar**: `readme/claude-docs/` klasöründe

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

