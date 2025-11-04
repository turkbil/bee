# 🤖 Claude Agent Skills Kurulum ve Kullanım Kılavuzu

**Tarih:** 2025-10-17
**Versiyon:** 1.0
**Durum:** ✅ Aktif

---

## 📋 İçindekiler

1. [Agent Skills Nedir?](#agent-skills-nedir)
2. [Kurulum](#kurulum)
3. [Yüklü Skills](#yüklü-skills)
4. [Kullanım Örnekleri](#kullanım-örnekleri)
5. [Yeni Skill Ekleme](#yeni-skill-ekleme)
6. [Sorun Giderme](#sorun-giderme)

---

## 🎯 Agent Skills Nedir?

Agent Skills, Claude'un özel görevlerde uzmanlaşmasını sağlayan bir sistemdir. Her skill:

- **Otomatik aktif olur**: Claude ihtiyaç duyduğunda otomatik yükler
- **Bağımsız çalışır**: Her skill kendi uzmanlık alanında çalışır
- **Birleştirilebilir**: Birden fazla skill aynı anda kullanılabilir
- **Portable**: Tüm Claude ürünlerinde çalışır (API, Claude.ai, Claude Code)

---

## 🚀 Kurulum

### Kurulum Özeti

```bash
# Skills klasörü oluşturuldu
mkdir -p ~/.claude/skills

# Anthropic Skills repository clone edildi
git clone https://github.com/anthropics/skills.git

# Laravel projesi için faydalı skills kopyalandı
# xlsx, pdf, docx, pptx, webapp-testing, skill-creator, template-skill
```

### Kurulum Tarihi
**17 Ekim 2025 - 15:43**

### Kurulum Konumu
```
~/.claude/skills/
├── docx/           # Word belge işlemleri
├── pdf/            # PDF işlemleri
├── pptx/           # PowerPoint işlemleri
├── xlsx/           # Excel işlemleri
├── webapp-testing/ # Web uygulama testleri
├── skill-creator/  # Yeni skill oluşturma
└── template-skill/ # Skill template'i
```

---

## 📦 Yüklü Skills

### 1. 📊 XLSX Skill
**Amaç:** Excel dosyaları ile çalışma

**Yetenekler:**
- ✅ Yeni Excel dosyaları oluşturma
- ✅ Formül yazma ve hesaplama
- ✅ Veri analizi
- ✅ Grafik ve görselleştirme
- ✅ Mevcut Excel dosyalarını düzenleme
- ✅ CSV/TSV dönüşümleri

**Kullanım Örneği:**
```
"Shop ürünlerinin bir Excel raporu oluştur.
Kategori bazında toplam satış ve stok durumunu göster."
```

---

### 2. 📄 PDF Skill
**Amaç:** PDF belgeleri ile çalışma

**Yetenekler:**
- ✅ PDF oluşturma
- ✅ Metin ve tablo çıkarma
- ✅ PDF birleştirme/bölme
- ✅ Form doldurma
- ✅ PDF analizi

**Kullanım Örneği:**
```
"Bu ürün kataloğunu profesyonel bir PDF'e dönüştür.
Kapak sayfası ve içindekiler ekle."
```

**Not:** Projede zaten PDF export var (Shop ürünleri için). Bu skill daha gelişmiş PDF işlemleri için kullanılabilir.

---

### 3. 📝 DOCX Skill
**Amaç:** Word belgeleri ile çalışma

**Yetenekler:**
- ✅ Word belgesi oluşturma
- ✅ Belge düzenleme
- ✅ Değişiklikleri izleme (tracked changes)
- ✅ Yorum ekleme
- ✅ Formatlama koruma
- ✅ Metin çıkarma

**Kullanım Örneği:**
```
"Tenant kurulum dökümanını Word formatında oluştur.
Başlıklar, numaralandırma ve görseller ile profesyonel bir doküman hazırla."
```

---

### 4. 📊 PPTX Skill
**Amaç:** PowerPoint sunumları ile çalışma

**Yetenekler:**
- ✅ Sunum oluşturma
- ✅ Slayt düzenleme
- ✅ Layout yönetimi
- ✅ Yorum ve konuşmacı notları ekleme
- ✅ Sunum analizi

**Kullanım Örneği:**
```
"Yeni özellikleri tanıtan bir sunum hazırla.
Her özellik için bir slayt, görseller ve açıklayıcı notlar ekle."
```

---

### 5. 🌐 Webapp Testing Skill
**Amaç:** Web uygulamalarını test etme

**Yetenekler:**
- ✅ Playwright ile otomatik test
- ✅ Frontend fonksiyonalite kontrolü
- ✅ UI davranış debug
- ✅ Ekran görüntüsü alma
- ✅ Browser log görüntüleme

**Kullanım Örneği:**
```
"Shop sayfasını test et. Ürün filtreleme, sepete ekleme ve checkout
işlemlerini kontrol et. Sorun varsa ekran görüntüsü al."
```

**Not:** Laravel.test ortamında test yapabilir.

---

### 6. 🛠️ Skill Creator Skill
**Amaç:** Yeni özel skill'ler oluşturma

**Yetenekler:**
- ✅ Skill template oluşturma
- ✅ SKILL.md formatı hazırlama
- ✅ Kaynak dosyaları organize etme
- ✅ İnteraktif skill geliştirme rehberi

**Kullanım Örneği:**
```
"Laravel Livewire component'leri için özel bir skill oluştur.
Bu skill, Livewire best practice'lerini ve projemizin pattern'lerini içersin."
```

---

### 7. 📋 Template Skill
**Amaç:** Yeni skill geliştirme için temel template

**Kullanım:**
- Yeni skill oluştururken başlangıç noktası
- SKILL.md yapısını gösterir
- Klasör organizasyonu örneği

---

## 💡 Kullanım Örnekleri

### Örnek 1: Excel Rapor Oluşturma
```
Kullanıcı: "Shop ürünlerinin kategori bazında satış raporunu Excel'e aktar"

Claude: [xlsx skill otomatik aktif olur]
- Veritabanından shop products verilerini alır
- Kategori bazında gruplar
- Excel dosyası oluşturur
- Formüller ve grafikler ekler
- İndirilmesi için dosya hazırlar
```

### Örnek 2: Ürün Kataloğu PDF
```
Kullanıcı: "Tüm shop ürünlerini içeren bir katalog PDF'i hazırla"

Claude: [pdf skill otomatik aktif olur]
- Ürün verilerini organize eder
- Profesyonel layout uygular
- Görselleri optimize eder
- PDF oluşturur ve optimize eder
```

### Örnek 3: Web Test Senaryosu
```
Kullanıcı: "Shop checkout işlemini test et ve sorun varsa raporla"

Claude: [webapp-testing skill otomatik aktif olur]
- Playwright script oluşturur
- Checkout flow'unu test eder
- Hataları tespit eder
- Ekran görüntüleri ile raporlar
```

### Örnek 4: Tenant Kurulum Dökümanı
```
Kullanıcı: "Tenant kurulum sürecini detaylı bir Word dökümanı olarak hazırla"

Claude: [docx skill otomatik aktif olur]
- Mevcut tenant-olusturma.md'yi okur
- Word formatına dönüştürür
- Başlıklar, numaralandırma ekler
- Kod blokları formatlar
- Profesyonel Word belgesi oluşturur
```

---

## 🆕 Yeni Skill Ekleme

### Yöntem 1: Anthropic Skills Repository'den

```bash
# Skills repository'yi clone et (zaten yapıldı)
cd /tmp/anthropic-skills

# İstediğin skill'i kopyala
cp -r [skill-adi] ~/.claude/skills/

# Örnek: algorithmic-art skill'ini ekle
cp -r algorithmic-art ~/.claude/skills/
```

### Yöntem 2: Özel Skill Oluşturma

```bash
# Template'i kopyala
cp -r ~/.claude/skills/template-skill ~/.claude/skills/my-laravel-skill

# SKILL.md dosyasını düzenle
nano ~/.claude/skills/my-laravel-skill/SKILL.md
```

**Özel Skill Önerisi:** `skill-creator` skill'ini kullanarak Claude'a interaktif şekilde skill oluşturttırabilirsin:

```
"Laravel modül geliştirme için özel bir skill oluştur.
Bu skill, Page pattern'ini, Livewire component'lerini ve
projemizin standartlarını içersin."
```

---

## 🐛 Sorun Giderme

### Skill Çalışmıyor?

**Kontrol 1: Klasör yapısı**
```bash
ls -la ~/.claude/skills/xlsx/
# Çıktı: SKILL.md ve diğer dosyalar görünmeli
```

**Kontrol 2: SKILL.md formatı**
```bash
head -15 ~/.claude/skills/xlsx/SKILL.md
# Çıktı: YAML header (---) ile başlamalı
```

**Kontrol 3: Yeniden başlat**
```bash
# Claude Code'u yeniden başlat
# Terminali kapat ve tekrar aç
```

### Skill'i Manuel Tetikleme

Claude otomatik olarak skill'leri yükler ama açıkça belirtebilirsin:

```
"xlsx skill'ini kullanarak bir Excel raporu oluştur"
```

### Claude Skill'i Görmüyor?

1. **Skill klasörünü kontrol et:**
   ```bash
   ls ~/.claude/skills/
   ```

2. **SKILL.md içeriğini kontrol et:**
   ```bash
   cat ~/.claude/skills/xlsx/SKILL.md | head -20
   ```

3. **İzinleri kontrol et:**
   ```bash
   chmod -R 755 ~/.claude/skills/
   ```

---

## 📊 Mevcut Kurulum Özeti

```
📦 ~/.claude/skills/
│
├── 📊 xlsx/           [Excel işlemleri]
├── 📄 pdf/            [PDF işlemleri]
├── 📝 docx/           [Word işlemleri]
├── 📊 pptx/           [PowerPoint işlemleri]
├── 🌐 webapp-testing/ [Web test]
├── 🛠️ skill-creator/  [Skill oluşturma]
└── 📋 template-skill/ [Template]

✅ 7 skill başarıyla kuruldu
✅ Otomatik aktif
✅ Kullanıma hazır
```

---

## 🔗 Faydalı Linkler

- **Anthropic Skills GitHub:** https://github.com/anthropics/skills
- **Agent Skills Blog:** https://www.anthropic.com/news/agent-skills
- **Claude Documentation:** https://docs.claude.com/

---

## 📝 Notlar

### Laravel Projesi için Özel Kullanım

1. **Excel Export:**
   - Shop ürünleri
   - Tenant raporları
   - Sipariş özetleri

2. **PDF İyileştirmesi:**
   - Mevcut PDF export sistemini güçlendir
   - Katalog ve raporlar

3. **Web Testing:**
   - Tenant sayfalarını otomatik test
   - Shop checkout flow test
   - Form validasyon test

4. **Döküman Oluşturma:**
   - Teknik dökümanlar (Word)
   - Sunumlar (PowerPoint)
   - Raporlar (Excel, PDF)

### Gelecek Planlar

- [ ] Laravel modül geliştirme için özel skill
- [ ] Livewire component generator skill
- [ ] Database seeder generator skill
- [ ] AI prompt optimizer skill (projede mevcut AI sistemi için)

---

## 🎉 Sonuç

Agent Skills sistemi başarıyla kuruldu! Artık Claude:
- **Excel/Word/PowerPoint/PDF** dosyaları oluşturabilir
- **Web testleri** yapabilir
- **Özel skill'ler** geliştirebilir

Skills otomatik çalışır, manuel aktivasyon gerekmez. Claude ihtiyaç duyduğunda ilgili skill'i yükler ve kullanır.

---

**Kurulum:** ✅ Tamamlandı
**Test:** ✅ Başarılı
**Durum:** 🟢 Aktif

**Son Güncelleme:** 2025-10-17 15:43
