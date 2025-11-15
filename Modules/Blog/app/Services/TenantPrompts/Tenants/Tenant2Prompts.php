<?php

namespace Modules\Blog\App\Services\TenantPrompts\Tenants;

use Illuminate\Support\Facades\File;
use Modules\Blog\App\Services\TenantPrompts\DefaultPrompts;

/**
 * Tenant 2 (iXtif) Blog AI Prompts
 *
 * iXtif.com için özel AI prompt'ları
 * readme/blog-prompt/ klasöründeki dökümanları kullanır
 */
class Tenant2Prompts extends DefaultPrompts
{
    protected string $promptPath;

    public function __construct()
    {
        $this->promptPath = base_path('readme/blog-prompt');
    }

    /**
     * Draft (taslak) üretimi için AI prompt
     */
    public function getDraftPrompt(): string
    {
        $promptFile = $this->promptPath . '/1-blog-taslak-olusturma.md';

        if (File::exists($promptFile)) {
            return File::get($promptFile);
        }

        // Fallback prompt
        return $this->getDefaultDraftPrompt();
    }

    /**
     * Blog içeriği yazımı için AI prompt
     */
    public function getBlogContentPrompt(): string
    {
        $promptFile = $this->promptPath . '/2-blog-yazdirma.md';

        if (File::exists($promptFile)) {
            $basePrompt = File::get($promptFile);

            // Firma bilgisi kullanımı kuralını ekle
            $companyRules = $this->getCompanyUsageRules();

            return $basePrompt . "\n\n" . $companyRules;
        }

        // Fallback prompt
        return $this->getDefaultContentPrompt();
    }

    /**
     * Firma bilgisi kullanım kuralları
     */
    protected function getCompanyUsageRules(): string
    {
        return <<<'RULES'

---

## 🏢 FİRMA BİLGİSİ KULLANIMI (ZORUNLU - UYMADIĞIN TAKDİRDE İÇERİK REDDEDİLİR!)

### ⚠️ KRİTİK: Bu kurallar ZORUNLUDUR ve MUTLAKA uyulmalıdır!

### Firma Bilgileri Context'ten Alınacak:
- **Firma Adı**: {company_info.name}
- **Site Başlığı**: {company_info.title}
- **Slogan**: {company_info.slogan}
- **Website**: {company_info.website}
- **Email**: {contact_info.email}
- **Telefon**: {contact_info.phone}
- **Adres**: {contact_info.address}

---

## ⭐ ZORUNLU KURALLAR - MUTLAKA UYULACAK!

### 1️⃣ FİRMA ADI KULLANIMI (ZORUNLU - MİNİMUM 3 KEZ!)

**🔴 UYARI:** Firma adını EN AZ 3 KEZ kullanmak ZORUNLUDUR!

**Kullanım Yerleri:**
1. **İlk 200 kelime içinde** (giriş paragrafı) - ZORUNLU
2. **Orta bölümde** (teknik detay/açıklama kısmı) - ZORUNLU
3. **Sonuç/CTA bölümünde** (kapanış) - ZORUNLU

**✅ DOĞRU KULLANIM ÖRNEKLERİ:**

**Giriş Paragrafı:**
```
"{company_info.name} olarak, endüstriyel ekipman sektöründe 15 yıllık tecrübemizle..."
```

**Orta Bölüm:**
```
"{company_info.name} uzman ekibi, forklift bakım süreçlerinde size profesyonel destek sağlar."
"{company_info.name}'in sunduğu teknik servis hizmetleri ile..."
```

**Sonuç/CTA:**
```
"Daha fazla bilgi için {company_info.name}'i arayabilirsiniz."
"{company_info.name} olarak, ihtiyaçlarınıza özel çözümler sunuyoruz."
```

**❌ YANLIŞ KULLANIM:**
```
❌ "Bizim firma..." (GENERİK!)
❌ "Firmamız..." (FİRMA ADI YOK!)
❌ "İşletmeniz için..." (FİRMA ADI YOK!)
❌ "Profesyonel destek için bize ulaşın" (FİRMA ADI YOK!)
```

---

### 2️⃣ İLETİŞİM BİLGİLERİ (ZORUNLU - TELEFON + EMAIL!)

**🔴 UYARI:** CTA bölümünde MUTLAKA telefon VE email olmalı!

**✅ DOĞRU CTA FORMATI:**
```html
<h2>İletişim ve Destek</h2>
<p>{company_info.name} olarak, forklift bakım süreçlerinde profesyonel destek sağlıyoruz. Detaylı bilgi almak ve ihtiyaçlarınıza özel çözümler için bizimle iletişime geçin:</p>

<ul>
  <li><strong>Telefon:</strong> {contact_info.phone}</li>
  <li><strong>Email:</strong> {contact_info.email}</li>
  <li><strong>Adres:</strong> {contact_info.address}</li>
</ul>

<p>Uzman ekibimiz, sorularınızı yanıtlamak ve size en uygun çözümü sunmak için hazır!</p>
```

**❌ YANLIŞ CTA:**
```html
❌ "Bizimle iletişime geçin." (İLETİŞİM BİLGİSİ YOK!)
❌ "Daha fazla bilgi için..." (TELEFON/EMAIL YOK!)
❌ "İhtiyaçlarınız için..." (KİMLİK YOK!)
```

---

### 3️⃣ OTORİTE & GÜVENİLİRLİK (FİRMA İLE VURGU)

Firma adını kullanarak uzmanlık ve otorite göster:

**✅ DOĞRU KULLANIM:**
```
"{company_info.name} olarak, endüstriyel ekipman tedarikinde 15 yıllık deneyimimizle..."
"{company_info.name} uzman ekibi, CE ve ISO standartlarına uygun..."
"{company_info.name}'in geniş ürün yelpazesi sayesinde..."
```

**❌ YANLIŞ KULLANIM:**
```
❌ "Profesyonel ekipler..." (FİRMA ADI YOK!)
❌ "Sektörde deneyimli firmalar..." (SPESIFIK DEĞİL!)
```

---

### 4️⃣ YASAKLAR (BUNLARI ASLA YAPMA!)

❌ **Firma adı OLMADAN bitirme** → REDDEDİLİR!
❌ **Genel "bizim firma" ifadeleri** → FİRMA ADI kullan!
❌ **İletişim bilgisi OLMADAN CTA** → Telefon + Email ZORUNLU!
❌ **Rakip firma adı kullanma** → Sadece {company_info.name}
❌ **"Tedarikçiler", "firmalar" gibi genel terimler** → {company_info.name} kullan!

---

## 📊 KONTROL LİSTESİ (Blog yazmadan önce kontrol et!)

Yazını göndermeden önce MUTLAKA şunları kontrol et:

✅ **Firma adı minimum 3 kez kullanıldı mı?**
   - [ ] İlk 200 kelimede 1 kez
   - [ ] Orta bölümde 1 kez
   - [ ] Sonuç/CTA'da 1 kez

✅ **CTA bölümünde iletişim bilgileri var mı?**
   - [ ] Telefon numarası
   - [ ] Email adresi
   - [ ] Firma adı

✅ **Firma adıyla otorite gösterildi mi?**
   - [ ] "... olarak" yapısı kullanıldı
   - [ ] Uzmanlık vurgusu yapıldı

---

## 🎯 ÖZET: MUTLAKA HATIRLA!

1. **Firma adı EN AZ 3 KEZ** kullanılacak!
2. **CTA'da TELEFON + EMAIL** olacak!
3. **İlk 200 kelimede firma adı** geçecek!
4. **Sonuç bölümünde firma adı + iletişim** olacak!

**Bu kurallara uyulmadığı takdirde içerik REDDEDILIR ve yeniden yazılması istenir!**

RULES;
    }

    /**
     * Tenant context override - iXtif için özel company name
     *
     * Parent DefaultPrompts::getContext()'i override eder
     */
    public function getContext(): array
    {
        $context = parent::getContext();

        // 🔧 FIX: company_info.name'i kısa title ile override et
        // Çünkü uzun "İxtif İç ve Dış Ticaret A.Ş." yerine "iXtif" kullanılmalı
        if (!empty($context['company_info']['title'])) {
            $context['company_info']['name'] = $context['company_info']['title'];
        }

        // Tenant 2 özel ek bilgiler
        $context['focus'] = 'industrial_equipment';
        $context['industry'] = 'B2B Endüstriyel Ekipman';
        $context['target_audience'] = 'Satın alma müdürleri, depo yöneticileri, lojistik sorumlular';

        return $context;
    }

    /**
     * Fallback draft prompt
     */
    protected function getDefaultDraftPrompt(): string
    {
        return 'Sen profesyonel bir SEO ve içerik stratejistisin. Endüstriyel ekipman konularında blog taslakları oluştur.';
    }

    /**
     * Fallback content prompt
     */
    protected function getDefaultContentPrompt(): string
    {
        return 'Sen profesyonel bir endüstriyel ekipman içerik yazarısın. 2000 kelimelik teknik blog yazıları yazıyorsun.';
    }
}
