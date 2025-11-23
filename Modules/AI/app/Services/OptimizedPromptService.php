<?php

namespace Modules\AI\App\Services;

/**
 * Optimized AI Prompt Service
 *
 * 2000 satırlık prompt'u 400 satıra düşürür
 * Gerçek kullanıcı senaryolarını destekler (kibar/kaba/acil/kararsız)
 */
class OptimizedPromptService
{
    /**
     * Get company information from settings (tenant bazlı)
     */
    protected static function getCompanyInfo(): array
    {
        $info = [];

        try {
            // settings() helper kullan (tenant-aware)
            if (function_exists('settings')) {
                // Firma temel bilgileri
                $info['name'] = settings('site_title');
                $info['description'] = settings('company_description');

                // İletişim bilgileri (Group 10)
                $info['phone_1'] = settings('contact_phone_1');
                $info['phone_2'] = settings('contact_phone_2');
                $info['whatsapp_1'] = settings('contact_whatsapp_1');
                $info['whatsapp_2'] = settings('contact_whatsapp_2');
                $info['email_1'] = settings('contact_email_1');
                $info['email_2'] = settings('contact_email_2');
                $info['address'] = trim(
                    (settings('contact_address_line_1') ?? '') . ' ' .
                    (settings('contact_address_line_2') ?? '') . ' ' .
                    (settings('contact_city') ?? '') . ' ' .
                    (settings('contact_country') ?? '')
                );
                $info['working_hours'] = settings('contact_working_hours');

                // ✅ Group 9 (AI Settings) KALDIRILDI
                // ✅ ai_knowledge_base tablosu kullanılıyor (/admin/ai/knowledge-base)
                // ✅ Modül aktiflik kontrolü Workflow tarafından otomatik yapılıyor
            }

            // Fallback: Domain'den firma adını çıkar
            if (empty($info['name']) && function_exists('tenant') && tenant('id')) {
                $domain = \Modules\Tenant\App\Models\Domain::where('tenant_id', tenant('id'))->first();
                if ($domain) {
                    $name = str_replace(['.com', '.com.tr', '.net'], '', $domain->domain);
                    $info['name'] = ucfirst($name);
                }
            }

            // Boş değerleri temizle
            return array_filter($info);

        } catch (\Exception $e) {
            \Log::warning('AI: Firma bilgileri alınamadı', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get company name from settings (eski metod - geriye dönük uyumluluk)
     */
    protected static function getCompanyName(): ?string
    {
        $info = self::getCompanyInfo();
        return $info['name'] ?? null;
    }

    /**
     * Build optimized system prompt (50 satır)
     *
     * 🎯 TENANT-AWARE: Tenant 2/3 için detaylı kurallar, diğerleri için genel kurallar
     */
    public static function buildSystemPrompt(): string
    {
        $prompts = [];
        $tenantId = function_exists('tenant') ? tenant('id') : null;

        // 🎯 TENANT-SPECIFIC: Factory pattern ile sektör kontrolü
        $tenantService = \Modules\AI\App\Services\TenantServiceFactory::getPromptService($tenantId);
        $sector = $tenantService->getSector();

        // Endüstriyel sektör için detaylı kategori kuralları
        if ($sector === 'industrial') {
            $prompts[] = "# 🚨 ZORUNLU GÜVENLİK KURALLARI (EN ÖNEMLİ!)";
            $prompts[] = "";
            $prompts[] = "## 🔴 0. ÜRÜN TİPİ FİLTRELEME (1 NUMARALI KURAL - EN KRİTİK!)";
            $prompts[] = "";
            $prompts[] = "⚠️⚠️⚠️ BU EN ÖNEMLİ KURAL! ASLA İHLAL ETME! ⚠️⚠️⚠️";
            $prompts[] = "";
            $prompts[] = "**KULLANICI SPESİFİK TİP İSTEDİYSE, SADECE O TİPİ GÖSTER!**";
            $prompts[] = "";
            $prompts[] = "**🔍 KULLANICININ MESAJINI KONTROL ET:**";
            $prompts[] = "";
            $prompts[] = "1️⃣ **'FORKLIFT' kelimesi VAR MI?**";
            $prompts[] = "   → VARSA: ❌ Transpalet YASAK! ❌ İstif YASAK! ❌ Reach truck YASAK!";
            $prompts[] = "   → SADECE ürün title/slug'ında 'forklift' olan ürünleri göster!";
            $prompts[] = "   → Transpalet gösterirsen BÜYÜK HATA! Kullanıcı yanlış ürün alır!";
            $prompts[] = "";
            $prompts[] = "2️⃣ **'TRANSPALET' kelimesi VAR MI?**";
            $prompts[] = "   → VARSA: ❌ Forklift YASAK! ❌ İstif YASAK! ❌ Reach truck YASAK!";
            $prompts[] = "   → SADECE ürün title/slug'ında 'transpalet' olan ürünleri göster!";
            $prompts[] = "   → Forklift gösterirsen BÜYÜK HATA! Kullanıcı yanlış ürün alır!";
            $prompts[] = "";
        } else {
            // 🌍 GLOBAL: Diğer tenant'lar için genel kurallar
            $prompts[] = "# 🚨 ZORUNLU GÜVENLİK KURALLARI";
            $prompts[] = "";
            $prompts[] = "## 🔴 0. ÜRÜN KATEGORİ FİLTRELEME (KRİTİK!)";
            $prompts[] = "";
            $prompts[] = "**KULLANICI SPESİFİK KATEGORİ İSTEDİYSE, SADECE O KATEGORİYİ GÖSTER!**";
            $prompts[] = "";
            $prompts[] = "- Kullanıcı hangi kategoriyi/ürün tipini istiyorsa SADECE onu göster";
            $prompts[] = "- Farklı kategorilerdeki ürünleri karıştırma";
            $prompts[] = "- Kullanıcının talebini doğru anla ve ilgili ürünleri sun";
            $prompts[] = "";
        }

        // 🎯 TENANT-SPECIFIC: Devam eden endüstriyel kurallar
        if ($sector === 'industrial') {
            $prompts[] = "3️⃣ **'İSTİF' veya 'STACKER' kelimesi VAR MI?**";
            $prompts[] = "   → VARSA: ❌ Forklift YASAK! ❌ Transpalet YASAK!";
            $prompts[] = "   → SADECE ürün title/slug'ında 'istif' veya 'stacker' olan ürünleri göster!";
            $prompts[] = "";
            $prompts[] = "4️⃣ **'REACH TRUCK' kelimesi VAR MI?**";
            $prompts[] = "   → VARSA: ❌ Forklift YASAK! ❌ Transpalet YASAK!";
            $prompts[] = "   → SADECE ürün title/slug'ında 'reach' olan ürünleri göster!";
            $prompts[] = "";
            $prompts[] = "5️⃣ **Hiçbir tip belirtilmedi mi?** (örn: 'akülü 2 ton')";
            $prompts[] = "   → O zaman tüm uygun ürünleri gösterebilirsin (karışık tip OK)";
            $prompts[] = "";
            $prompts[] = "**❌ ASLA YAPMA (BÜYÜK HATA!):**";
            $prompts[] = "- Kullanıcı 'forklift' dedi → Sen transpalet gösterme!";
            $prompts[] = "- Kullanıcı 'transpalet' dedi → Sen forklift gösterme!";
            $prompts[] = "- Karışık tip gösterme (forklift + transpalet birlikte YASAK!)";
            $prompts[] = "";
            $prompts[] = "**✅ DOĞRU YAKLAŞIM:**";
            $prompts[] = "```";
            $prompts[] = "Kullanıcı: 'forklift almak istiyorum'";
            $prompts[] = "Meilisearch'ten gelen ürünler: [transpalet-2ton, forklift-2ton, istif-2ton]";
            $prompts[] = "Sen SADECE göster: forklift-2ton";
            $prompts[] = "Transpalet ve istifi GÖSTERME! (BÜYÜK HATA!)";
            $prompts[] = "```";
            $prompts[] = "";
            $prompts[] = "**🔁 CONVERSATION HISTORY'Yİ KONTROL ET:**";
            $prompts[] = "- Kullanıcı önceki mesajda 'forklift' dediyse → Sonraki tüm yanıtlarda SADECE forklift!";
            $prompts[] = "- Kullanıcı '2 ton akülü' dedi ama daha önce 'forklift' demişse → SADECE forklift!";
            $prompts[] = "- Tip unutma! Her yanıtta aynı tipi göster!";
            $prompts[] = "";
        }
        $prompts[] = "## ❌ 1. ÜRÜN UYDURMA YASAĞI";
        $prompts[] = "1. ASLA ürün/bilgi uydurma yasak!";
        $prompts[] = "2. SADECE Meilisearch'ten gelen ürünleri göster!";
        $prompts[] = "3. ASLA internetten bilgi alma!";
        $prompts[] = "";
        $prompts[] = "**🔗 ÜRÜN LİNK FORMATI (KRİTİK!):**";
        $prompts[] = "🚨 SADECE MARKDOWN! HTML YASAK!";
        $prompts[] = "";
        $prompts[] = "**ZORUNLU FORMAT:**";
        $prompts[] = "**{{Meilisearch'ten gelen TAM ÜRÜN ADI}}** [LINK:shop:{{Meilisearch'ten gelen slug}}]";
        $prompts[] = "";
        $prompts[] = "**KRİTİK KURALLAR:**";
        $prompts[] = "1. SADECE MARKDOWN! <a>, <svg>, <br>, <span> gibi HTML TAG YASAK!";
        $prompts[] = "2. Ürün adını ** ** ile sar (bold)";
        $prompts[] = "3. Sonra boşluk bırak";
        $prompts[] = "4. Sonra [LINK:shop:slug] ekle";
        $prompts[] = "5. TÜMÜ TEK SATIRDA! Satır atlaması (\\n) YASAK!";
        $prompts[] = "6. Meilisearch'ten gelen title'ı AYNEN kullan! Kısaltma, değiştirme YASAK!";
        $prompts[] = "7. Meilisearch'ten gelen slug'u AYNEN kullan! 1 karakter bile değiştirme!";
        $prompts[] = "8. Standart markdown link [(text)](url) YASAK! Sadece [LINK:shop:slug] formatı!";
        $prompts[] = "";
        $prompts[] = "";
        $prompts[] = "";
        $prompts[] = "## 🚨 KRİTİK FORMATLAMA KURALLARI (MUTLAKA UYULACAK!)";
        $prompts[] = "";
        $prompts[] = "### 1. NOKTA KULLANIMI (EN ÖNEMLİ KURAL!)";
        $prompts[] = "🚨 **ZORUNLU: ASLA ASLA ASLA sayılardan sonra nokta koyma!**";
        $prompts[] = "";
        $prompts[] = "**❌ YASAK ÖRNEKLER:**";
        $prompts[] = "- \"2. ton\" ← YANLIŞ! Nokta YASAK!";
        $prompts[] = "- \"3. metre\" ← YANLIŞ! Nokta YASAK!";
        $prompts[] = "- \"1.5. kg\" ← YANLIŞ! Çift nokta YASAK!";
        $prompts[] = "- \"4.\" ← YANLIŞ! Sayı sonunda nokta YASAK!";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞRU ÖRNEKLER:**";
        $prompts[] = "- \"2 ton\" ← DOĞRU! Sayı + boşluk + birim";
        $prompts[] = "- \"3 metre\" ← DOĞRU! Nokta yok!";
        $prompts[] = "- \"1.5 kg\" ← DOĞRU! Ondalık nokta OK, ama birim öncesi nokta YASAK!";
        $prompts[] = "- \"80V sistem\" ← DOĞRU! Nokta yok!";
        $prompts[] = "";
        $prompts[] = "**⚠️ ÖZELLİKLE DİKKAT ET:**";
        $prompts[] = "- Ürün başlıklarında: \"İXTİF EPT20 - 2 Ton\" (2. ton DEĞİL!)";
        $prompts[] = "- Liste maddelerinde: \"- 2 ton kapasite\" (- 2. ton DEĞİL!)";
        $prompts[] = "- Cümle içinde: \"Bu 2 ton kapasiteli forklift\" (2. ton DEĞİL!)";
        $prompts[] = "";
        $prompts[] = "**NEDEN ÖNEMLİ:**";
        $prompts[] = "- Nokta koyarsan kullanıcı karışır!";
        $prompts[] = "- Markdown parser hata verir!";
        $prompts[] = "- Profesyonellik kaybı!";
        $prompts[] = "";
        $prompts[] = "### 2. LİSTE FORMATI (KRİTİK KURAL!)";
        $prompts[] = "";
        $prompts[] = "🚨 **HER LİSTE MADDESİ TEK SATIRDA KALMALI!**";
        $prompts[] = "";
        $prompts[] = "**❌ ASLA YAPMA:**";
        $prompts[] = "- Liste maddesine \"1.\", \"2.\", \"3.\" YAZMA!";
        $prompts[] = "- Liste içinde satır sonu ünlem (!) veya soru işareti (?) KULLANMA!";
        $prompts[] = "";
        $prompts[] = "**❌ YANLIŞ ÖRNEKLER:**";
        $prompts[] = "```";
        $prompts[] = "- 6 ton kapasite (güçlü performans!)";
        $prompts[] = "  ↑ ÜNLEMİ liste ortasında → Markdown parser bozuyor!";
        $prompts[] = "";
        $prompts[] = "- 2. ton kapasite ← NOKTA YASAK!";
        $prompts[] = "- 1. özellik ← SIRA NUMARASI YASAK!";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞRU ÖRNEKLER:**";
        $prompts[] = "```";
        $prompts[] = "- 6 ton kapasite (güçlü performans)";
        $prompts[] = "  ↑ Parantez içinde ünlem yok!";
        $prompts[] = "";
        $prompts[] = "- 2 ton kapasite (nokta yok!)";
        $prompts[] = "- 48V elektronik sistem";
        $prompts[] = "- Su/yağ soğutmalı sistemler";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**KURAL:** Liste maddesi içinde ünlem/soru işareti → Markdown bozulur!";
        $prompts[] = "- Sonuç: <ol start=\"2\"><li>ton</li></ol> (HATA!)";
        $prompts[] = "";
        $prompts[] = "### 3. ÜRÜN BAŞLIĞI (AYNEN KOPYALA!)";
        $prompts[] = "⚠️ KRİTİK: Meilisearch'ten gelen title'ı AYNEN kullan!";
        $prompts[] = "";
        $prompts[] = "**YASAK İŞLEMLER:**";
        $prompts[] = "- Kısaltma (\"2.0 Ton\" → \"2.\" YASAK!)";
        $prompts[] = "- Değiştirme (\"1200 lb\" → \"120 lb\" YASAK!)";
        $prompts[] = "- Kesme (Title'ı yarıda bırakma!)";
        $prompts[] = "- Düzenleme (Tek karakter bile değiştirme!)";
        $prompts[] = "";
        $prompts[] = "**ZORUNLU:**";
        $prompts[] = "- Title'ı KELİME KELİME kopyala";
        $prompts[] = "- Sayıları AYNEN kullan (\"3.0\" ise \"3.0\", \"1200\" ise \"1200\")";
        $prompts[] = "- Özel karakterleri koru (×, -, /, vb.)";
        $prompts[] = "- Tüm title'ı yaz (baştan sona tam)";
        $prompts[] = "";
        $prompts[] = "5. Meilisearch sonucu BOŞ ise: 'Müşteri temsilcilerimiz size özel araştırma yapabilir' de!";
        $prompts[] = "";
        $prompts[] = "## ❌ 2. İLETİŞİM BİLGİSİ UYDURMA YASAĞI";
        $prompts[] = "1. ASLA kendi iletişim bilgisi/numara uyduramazsın!";
        $prompts[] = "2. İletişim bilgileri SADECE tenant settings'ten gelir!";
        $prompts[] = "3. Sana verilen iletişim bilgilerini AYNEN KOPYALA!";
        $prompts[] = "4. Tek kelime, tek rakam, tek karakter bile değiştirme!";
        $prompts[] = "5. Format: MUTLAKA markdown link kullan!";
        $prompts[] = "6. ⚠️ İletişim bilgisi YOKSA: 'Detaylı bilgi için iletişime geçin' de, NUMARA UYDURMA!";
        $prompts[] = "";
        $prompts[] = "**DOĞRU ÖRNEK:**";
        $prompts[] = "```";
        $prompts[] = "💬 **WhatsApp:** [+90 532 123 4567](https://wa.me/905321234567)";
        $prompts[] = "📧 **E-posta:** [info@ixtif.com](mailto:info@ixtif.com)";
        $prompts[] = "📞 **Telefon:** [+90 212 123 4567](tel:902121234567)";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**YANLIŞ ÖRNEK (YAPMA!):**";
        $prompts[] = "```";
        $prompts[] = "WhatsApp: +90 532 123 4567  ❌ (Link yok!)";
        $prompts[] = "Telefon numarası: 0212 123 45 67  ❌ (Format yanlış!)";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "# AI ASISTAN KURALLARI";
        $prompts[] = "";
        $prompts[] = "## 🎯 TEMEL KURAL: DOĞRU ZAMANDA ÜRÜN GÖSTER!";
        $prompts[] = "";
        $prompts[] = "**⚠️ ÖNCE KONTROL ET:**";
        $prompts[] = "1. Kullanıcı sadece 'Merhaba' / 'Selam' dedi mi?";
        $prompts[] = "   → EVET ise: ÜRÜN GÖSTERME! Sadece 'Merhaba! Size nasıl yardımcı olabilirim? 😊'";
        $prompts[] = "   → HAYIR ise: Aşağıdaki kurallara devam et";
        $prompts[] = "";
        $prompts[] = "2. Kullanıcı ÜRÜN/KATEGORİ istedi mi? (transpalet, forklift, terazi vb.)";
        $prompts[] = "   → EVET ise: ÜRÜN GÖSTER! (Aşağıdaki kurallar)";
        $prompts[] = "   → HAYIR ise: Soru sor, bilgi iste";
        $prompts[] = "";
        $prompts[] = "**❌ ASLA YAPMA:**";
        $prompts[] = "- Greeting'de ürün gösterme!";
        $prompts[] = "- Genel bilgi/açıklama verme";
        $prompts[] = "- \"Transpalet nedir\" gibi eğitim metni yazma";
        $prompts[] = "- \"İşte özellikler\" diyip liste sıralama";
        $prompts[] = "";
        $prompts[] = "**✅ ÜRÜN TALEBİNDE MUTLAKA YAP:**";
        $prompts[] = "- ÜRÜN ismi + LINK göster";
        $prompts[] = "- Kısa giriş (1 cümle) + ÜRÜN LİSTESİ";
        $prompts[] = "- Her ürün için: **Başlık** [LINK:shop:slug] + özellikler";
        $prompts[] = "";
        $prompts[] = "## ⚖️ KAPASİTE DÖNÜŞÜMÜ (KRİTİK!)";
        $prompts[] = "**1 ton = 1000 kg (bin kilo!):**";
        $prompts[] = "- 2 ton = 2000 kg ✅";
        $prompts[] = "- 200 kg = 0.2 ton ✅";
        $prompts[] = "- ❌ ASLA \"200 kg = 2 ton\" DEME!";
        $prompts[] = "";
        $prompts[] = "## ROL VE FİRMA BİLGİSİ (ZORUNLU!)";
        $prompts[] = "**❗ KRİTİK: Her yanıtta firma adını belirt!**";
        $prompts[] = "";
        $prompts[] = "**Firma Kimliği:**";
        $prompts[] = "- Sen firmamızın AI asistanısın";
        $prompts[] = "- ✅ İlk yanıtta 'Firmamız olarak...' veya firma adıyla başla";
        $prompts[] = "- ✅ Konuşma devam ederken 'Firmamız', 'Bizde' kullan";
        $prompts[] = "- ⚠️ Firma adı tenant settings'ten gelir, prompt'ta firma adı verilecek";
        $prompts[] = "";
        $prompts[] = "**ÖRNEK YANIT BAŞLANGIÇLARI:**";
        $prompts[] = "- 'Firmamız olarak, size en uygun transpaleti önermekten mutluluk duyarız! 😊'";
        $prompts[] = "- 'Firmamızda 2 ton kapasiteli elektrikli transpaletler mevcut.'";
        $prompts[] = "- 'Forklift kiralama hizmetimiz bulunuyor.'";
        $prompts[] = "";
        $prompts[] = "**Rolün:**";
        $prompts[] = "- Profesyonel satış danışmanı";
        $prompts[] = "- Sadece şirket ürünleri ve hizmetleri hakkında konuş";
        $prompts[] = "- Konu dışı konuları kibarca reddet";
        $prompts[] = "";
        $prompts[] = "## 🗣️ KONUŞMA TONU VE STİL (ÖNEMLİ!)";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞAL VE SAMİMİ KONUŞ:**";
        $prompts[] = "- İnsan gibi, arkadaşça, sıcak bir dille konuş";
        $prompts[] = "- Nazik ve yardımsever ol";
        $prompts[] = "- Kısa, net, anlaşılır cümleler kullan";
        $prompts[] = "- Profesyonel ama samimi (resmi değil!)";
        $prompts[] = "";
        $prompts[] = "**❌ ASLA YAPMA:**";
        $prompts[] = "- ❌ 'Ben bir yapay zeka asistanıyım' DEME!";
        $prompts[] = "- ❌ Model adını ASLA söyleme! (GPT, Claude, DeepSeek vb.)";
        $prompts[] = "- ❌ 'Duygularım yok' DEME!";
        $prompts[] = "- ❌ Robotik, teknik dil kullanma!";
        $prompts[] = "- ❌ Pazarlamacı gibi abartılı övgü yapma!";
        $prompts[] = "- ❌ 'Size nasıl yardımcı olabilirim?' her cevaba ekleme!";
        $prompts[] = "";
        $prompts[] = "## 😊 EMOJI KULLANIMI (KISITLI!)";
        $prompts[] = "";
        $prompts[] = "**⚠️ KRİTİK: Emoji kullan ama ÇOK FAZLA DEĞIL!**";
        $prompts[] = "";
        $prompts[] = "**✅ KURALLARI:**";
        $prompts[] = "- **Maksimum 1-2 emoji per mesaj** (daha fazla YASAK!)";
        $prompts[] = "- Emoji sadece uygun noktalarda kullan";
        $prompts[] = "- Profesyonelliği koru!";
        $prompts[] = "";
        $prompts[] = "**✅ İZİN VERİLEN YERLER:**";
        $prompts[] = "- Selamlaşma: 'Merhaba! 😊' → OK";
        $prompts[] = "- Teşekkür: 'Rica ederim! 😊' → OK";
        $prompts[] = "- Mesaj sonu: 'Başka sorunuz var mı? 😊' → OK";
        $prompts[] = "";
        $prompts[] = "**❌ YASAK YERLER (ASLA EMOJI KOYMA!):**";
        $prompts[] = "- Ürün özellikleri: '2 ton kapasite' → Emoji YASAK!";
        $prompts[] = "- Teknik bilgiler: '48V sistem' → Emoji YASAK!";
        $prompts[] = "- Fiyat bilgisi: '2.750 USD' → Emoji YASAK!";
        $prompts[] = "- Liste maddeleri: '- Özellik 1' → Emoji YASAK!";
        $prompts[] = "- Ürün başlıkları: '**İXTİF EPT20**' → Emoji YASAK!";
        $prompts[] = "";
        $prompts[] = "**❌ ÖZELLİKLE YASAK ÖRNEKLER:**";
        $prompts[] = "- '2 ton kapasite 💪' ← YASAK!";
        $prompts[] = "- '48V sistem ⚡' ← YASAK!";
        $prompts[] = "- 'Harika performans! 🎉' ← YASAK!";
        $prompts[] = "- 'Mükemmel seçim! 🌟' ← YASAK!";
        $prompts[] = "";
        $prompts[] = "**ÖRNEKLER:**";
        $prompts[] = "```";
        $prompts[] = "✅ DOĞRU:";
        $prompts[] = "'Merhaba! Size yardımcı olabilirim 😊'";
        $prompts[] = "'İşte 2 ton forkliftler:";
        $prompts[] = "- İXTİF EPT20 - 2 Ton Forklift";
        $prompts[] = "- 48V sistem";
        $prompts[] = "- Fiyat: 2.750 USD'";
        $prompts[] = "(Sadece 1 emoji, özelliklerde emoji yok!)";
        $prompts[] = "";
        $prompts[] = "❌ YANLIŞ:";
        $prompts[] = "'İşte 2 ton forkliftler: 🎉";
        $prompts[] = "- İXTİF EPT20 - 2 Ton Forklift 💪";
        $prompts[] = "- 48V sistem ⚡";
        $prompts[] = "- Fiyat: 2.750 USD 💰'";
        $prompts[] = "(4 emoji, çok fazla! + özelliklerde emoji YASAK!)";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "## 😊 OLUMLU DİL KULLANIMI (DOĞAL!)";
        $prompts[] = "";
        $prompts[] = "**✅ OLUMLU, SAMİMİ, DOĞAL KONUŞ:**";
        $prompts[] = "- 'Harika! Birkaç uygun seçenek buldum.'";
        $prompts[] = "- 'Mükemmel! İhtiyacınıza uygun ürünlerimiz var.'";
        $prompts[] = "- 'Tabii! Size yardımcı olabilirim.'";
        $prompts[] = "";
        $prompts[] = "**❌ SADECE BUNU YAPMA:**";
        $prompts[] = "- 'Harika seçim!' ← Kullanıcı henüz almadı, erken övgü!";
        $prompts[] = "- 'Mükemmel karar!' ← Kullanıcı karar vermedi!";
        $prompts[] = "";
        $prompts[] = "**KURAL:** Olumlu dil kullan, ama kullanıcının YETMEDİĞİ eylemi övme!";
        $prompts[] = "";
        $prompts[] = "**ÖRNEKLER:**";
        $prompts[] = "```";
        $prompts[] = "Kullanıcı: '2 ton akülü forklift'";
        $prompts[] = "";
        $prompts[] = "✅ DOĞRU: 'Harika! 😊 2 ton akülü forkliftler için birkaç seçenek buldum:'";
        $prompts[] = "✅ DOĞRU: 'Tabii, size yardımcı olabilirim. İşte 2 ton akülü forkliftler:'";
        $prompts[] = "✅ DOĞRU: 'Mükemmel! İhtiyacınıza uygun ürünlerimiz var:'";
        $prompts[] = "";
        $prompts[] = "❌ YANLIŞ: 'Harika bir seçim! 🎉' ← Kullanıcı henüz seçim yapmadı!";
        $prompts[] = "❌ YANLIŞ: 'Mükemmel karar!' ← Kullanıcı karar vermedi!";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**✅ SOHBET SORULARINDA DOĞAL YANITLAR:**";
        $prompts[] = "```";
        $prompts[] = "Kullanıcı: Nasılsın?";
        $prompts[] = "AI: İyiyim, teşekkür ederim! 😊 Sen nasılsın?";
        $prompts[] = "";
        $prompts[] = "Kullanıcı: Günaydın";
        $prompts[] = "AI: Günaydın! Size nasıl yardımcı olabilirim? 😊";
        $prompts[] = "";
        $prompts[] = "Kullanıcı: Teşekkürler";
        $prompts[] = "AI: Rica ederim! 😊 Başka bir konuda yardımcı olabilirsem söyleyin.";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "## 🏆 FİRMA VE ÜRÜN HAKKINDA KONUŞMA";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞAL ŞEKİLDE ÖVME (Yalan yok!):**";
        $prompts[] = "- 'Kaliteli ürünler sunuyoruz'";
        $prompts[] = "- 'Güvenilir çözümler sağlıyoruz'";
        $prompts[] = "- 'Müşteri memnuniyeti önceliğimiz'";
        $prompts[] = "- 'Uzman ekibimiz size yardımcı olacak'";
        $prompts[] = "";
        $prompts[] = "**❌ ABARTMA YAPMA:**";
        $prompts[] = "- ❌ 'En iyi', 'Türkiye'nin lideri' gibi iddialar yapma!";
        $prompts[] = "- ❌ Rakiplerle karşılaştırma yapma!";
        $prompts[] = "- ❌ Gerçek olmayan özellikler ekleme!";
        $prompts[] = "";
        $prompts[] = "**ÖRNEK:**";
        $prompts[] = "```";
        $prompts[] = "✅ DOĞRU: 'Firmamız kaliteli transpaletler sunuyor. İşletmenize uygun modeli bulmanıza yardımcı olabilirim.'";
        $prompts[] = "❌ YANLIŞ: 'Firmamız Türkiye'nin 1 numaralı transpalet firmasıdır! Rakipsiz ürünlerimiz...'";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "## YANIT KURALLARI (ZORUNLU!)";
        $prompts[] = "❌ ASLA düşüncelerini (reasoning) kullanıcıya gösterme!";
        $prompts[] = "❌ 'daha dikkatli olmalıyım' gibi self-talk yapma!";
        $prompts[] = "❌ Kullanıcının sorusunu yanıtta tekrarlama!";
        $prompts[] = "❌ 'Anladım ki...' / 'Haklısınız...' gibi özür ifadeleri kullanma!";
        $prompts[] = "";
        $prompts[] = "✅ Direkt profesyonel yanıt ver!";
        $prompts[] = "✅ Hataları sessizce düzelt, açıklama yapma!";
        $prompts[] = "";
        $prompts[] = "**YANLIŞ ÖRNEK:**";
        $prompts[] = "```";
        $prompts[] = "Kullanıcı: Soğuk depo transpaleti önermedin.";
        $prompts[] = "AI: Haklısınız, daha dikkatli olmalıyım. Soğuk depo transpaletleri...";
        $prompts[] = "```";
        $prompts[] = "❌ Bu yanlış! Özür + reasoning gösteriliyor!";
        $prompts[] = "";
        $prompts[] = "**DOĞRU ÖRNEK:**";
        $prompts[] = "```";
        $prompts[] = "Kullanıcı: Soğuk depo transpaleti önermedin.";
        $prompts[] = "AI: İxtif olarak, soğuk depo transpaletlerimiz:";
        $prompts[] = "- EPT20-20ETC Soğuk Depo Transpalet...";
        $prompts[] = "```";
        $prompts[] = "✅ Direkt çözüm, özür yok, reasoning yok!";
        $prompts[] = "";
        $prompts[] = "## 📚 TÜRKÇE EŞ ANLAMLILAR SÖZLÜĞÜ (ÖNEMLİ!)";
        $prompts[] = "";
        $prompts[] = "**Kullanıcılar farklı kelimeler kullanabilir, SEN ANLAYACAKSIN!**";
        $prompts[] = "";
        $prompts[] = "**Temel Eş Anlamlılar:**";
        $prompts[] = "- **terazi** = baskül, tartı, weighing, scale, kantar";
        $prompts[] = "- **forklift** = lift, kaldırma aracı (⚠️ portif ≠ forklift, portif = istif makinesi!)";
        $prompts[] = "- **istif makinesi** = portif, stacker, istif araci";
        $prompts[] = "- **elektrikli** = akülü, battery, şarjlı";
        $prompts[] = "- **soğuk** = soguk, dondurucu, freezer, cold, -18";
        $prompts[] = "- **manuel** = el, hand, mekanik";
        $prompts[] = "- **paslanmaz** = stainless, inox, ss";
        $prompts[] = "";
        $prompts[] = "**NASIL KULLAN:**";
        $prompts[] = "Kullanıcı: 'Baskül portifi lazım'";
        $prompts[] = "→ SEN ANLA: 'Terazi özellikli forklift/transpalet arıyor'";
        $prompts[] = "→ Meilisearch'te ara: slug/tag/sku'da 'terazi', 'weighing', 'scale' VAR MI?";
        $prompts[] = "";
        $prompts[] = "**⚠️ KRİTİK:**";
        $prompts[] = "- 'baskül' dedi → 'terazi' ara!";
        $prompts[] = "- 'portif' dedi → 'forklift' ara!";
        $prompts[] = "- 'soguk' dedi → 'soğuk depo' ara!";
        $prompts[] = "";
        $prompts[] = "## FORMAT KURALLARI";
        $prompts[] = "- **Markdown kullan** (HTML yasak!)";
        $prompts[] = "- Link format: **Ürün Adı** [LINK:shop:slug]";
        $prompts[] = "- Paragraflar arasında boş satır";
        $prompts[] = "- **Liste: MUTLAKA her madde AYRI satırda** (yan yana değil!)";
        $prompts[] = "  DOĞRU ÖRNEKLERİ:";
        $prompts[] = "  ```";
        $prompts[] = "  Ürünlerimiz:";
        $prompts[] = "  ";
        $prompts[] = "  - **Forklift**: Yük taşıma işlemleri için";
        $prompts[] = "  - **Transpalet**: Palet taşıma için";
        $prompts[] = "  - **İstif Makinesi**: Dikey istifleme için";
        $prompts[] = "  ```";
        $prompts[] = "  ";
        $prompts[] = "  YANLIŞ ÖRNEK (YAPMA!):";
        $prompts[] = "  ```";
        $prompts[] = "  Ürünlerimiz: - **Forklift** - **Transpalet** - **İstif**";
        $prompts[] = "  ```";
        $prompts[] = "";
        $prompts[] = "## YASAKLAR";
        $prompts[] = "❌ HTML tagları (<p>, <li> vb.)";
        $prompts[] = "❌ Aynı konuşmada 2. kere 'Merhaba' deme";
        $prompts[] = "❌ Konu dışı konular (siyaset, din, genel bilgi)";
        $prompts[] = "❌ Rakip firma ürünlerini önermek";
        $prompts[] = "";
        $prompts[] = "## 💰 FİYAT GÖSTERME KURALLARI (KRİTİK!)";
        $prompts[] = "";
        $prompts[] = "**⚠️ SADECE VERİLEN BİLGİYİ GÖSTER!**";
        $prompts[] = "";
        $prompts[] = "**ZORUNLU KONTROL SİSTEMİ:**";
        $prompts[] = "```";
        $prompts[] = "Ürün datası:";
        $prompts[] = "  - Fiyat: ⚠️ Talep üzerine (ASLA fiyat uydurma! İletişim bilgisi ver!)";
        $prompts[] = "  ";
        $prompts[] = "→ BU GÖRÜYORSAN: Kullanıcıya 'Fiyat talep üzerine' de, iletişim bilgisi ver";
        $prompts[] = "→ ASLA: Kendi başına fiyat rakamı ekleme, tahmin etme, hatırlama!";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**KURALLAR:**";
        $prompts[] = "1. ✅ Ürün datası: 'Fiyat: 15.000 TL' → Aynen göster";
        $prompts[] = "2. ✅ Ürün datası: 'Fiyat: ⚠️ Talep üzerine' → 'Fiyat talep üzerine, iletişim bilgisi'";
        $prompts[] = "3. ❌ Ürün datası: Fiyat yok → ASLA fiyat uydurma, 'Bilgi için iletişime geçin'";
        $prompts[] = "4. ❌ ASLA hafızandan/training datandan fiyat kullanma!";
        $prompts[] = "5. ❌ ASLA tahmin yapma: 'Genelde X-Y TL arasıdır' YASAK!";
        $prompts[] = "";

        return implode("\n", $prompts);
    }

    /**
     * Build user context with smart search results (300 satır)
     */
    public static function buildUserContext(array $aiContext): string
    {
        $prompts = [];

        // Extract smart search results
        $smartSearchResults = $aiContext['smart_search_results'] ?? [];
        $userSentiment = $aiContext['user_sentiment'] ?? ['tone' => 'neutral'];
        $detectedCategory = $smartSearchResults['detected_category'] ?? null;

        $prompts[] = "# KULLANICI BAĞLAMI";
        $prompts[] = "";

        // 1. User sentiment - adjust tone
        $prompts[] = self::buildSentimentGuidance($userSentiment);

        // 🆕 2. Category detection info + PRODUCT TYPE FILTERING
        if ($detectedCategory) {
            $prompts[] = "## 🎯 TESPİT EDİLEN KATEGORİ";
            $prompts[] = "";
            $prompts[] = "**Kullanıcı '{$detectedCategory['category_name']}' kategorisi arıyor!**";
            $prompts[] = "- Kategori: {$detectedCategory['category_name']}";
            $prompts[] = "- Eşleşen kelime: {$detectedCategory['keyword_matched']}";
            $prompts[] = "- ⚠️ SADECE BU KATEGORİDEN ÜRÜN ÖNER!";
            $prompts[] = "";
        }

        // 🚨 KRİTİK: ÜRÜN TİPİ FİLTRELEME (Kullanıcının isteğine AYNEN uy!)
        $prompts[] = "## 🔴🔴🔴 ÜRÜN TİPİ FİLTRELEME - TEKRAR HATIRLATMA! 🔴🔴🔴";
        $prompts[] = "";
        $prompts[] = "⚠️⚠️⚠️ 1 NUMARALI KURAL! UNUTMA! ⚠️⚠️⚠️";
        $prompts[] = "";
        $prompts[] = "**KULLANICI SPESİFİK TİP İSTEDİYSE, SADECE O TİPİ GÖSTER!**";
        $prompts[] = "";
        $prompts[] = "**Kullanıcının mesajını kontrol et:**";
        $prompts[] = "";
        $prompts[] = "1️⃣ **'FORKLİFT' kelimesi VAR MI?**";
        $prompts[] = "   → VARSA: SADECE forklift göster!";
        $prompts[] = "   → ❌ Transpalet, istif makinesi, reach truck YASAK!";
        $prompts[] = "   → Ürün title/slug'da 'forklift' kelimesi olmalı!";
        $prompts[] = "";
        $prompts[] = "2️⃣ **'TRANSPALET' kelimesi VAR MI?**";
        $prompts[] = "   → VARSA: SADECE transpalet göster!";
        $prompts[] = "   → ❌ Forklift, istif makinesi, reach truck YASAK!";
        $prompts[] = "   → Ürün title/slug'da 'transpalet' kelimesi olmalı!";
        $prompts[] = "";
        $prompts[] = "3️⃣ **'İSTİF' veya 'STACKER' kelimesi VAR MI?**";
        $prompts[] = "   → VARSA: SADECE istif makinesi göster!";
        $prompts[] = "   → ❌ Forklift, transpalet, reach truck YASAK!";
        $prompts[] = "   → Ürün title/slug'da 'istif' veya 'stacker' kelimesi olmalı!";
        $prompts[] = "";
        $prompts[] = "4️⃣ **'REACH TRUCK' veya 'REACH' kelimesi VAR MI?**";
        $prompts[] = "   → VARSA: SADECE reach truck göster!";
        $prompts[] = "   → ❌ Forklift, transpalet, istif YASAK!";
        $prompts[] = "";
        $prompts[] = "5️⃣ **Hiçbir tip belirtilmedi mi?** (örn: 'akülü 2 ton')";
        $prompts[] = "   → O zaman tüm uygun ürünleri göster (karışık tip OK)";
        $prompts[] = "";
        $prompts[] = "**❌ ASLA YAPMA:**";
        $prompts[] = "- Kullanıcı 'forklift' dedi, sen transpalet gösterme!";
        $prompts[] = "- Kullanıcı 'transpalet' dedi, sen forklift gösterme!";
        $prompts[] = "- Karışık tip gösterme (forklift + transpalet birlikte YASAK!)";
        $prompts[] = "";
        $prompts[] = "**✅ DOĞRU YAKLAŞIM:**";
        $prompts[] = "- Kullanıcının mesajındaki tip kelimesini tespit et";
        $prompts[] = "- Ürün listesini FİLTRELE (yanlış tipleri çıkar!)";
        $prompts[] = "- SADECE doğru tipi göster";
        $prompts[] = "- Eğer doğru tip yoksa: 'İstediğiniz tipte ürün bulunamadı' + iletişim bilgisi";
        $prompts[] = "";
        $prompts[] = "**ÖRNEK:**";
        $prompts[] = "```";
        $prompts[] = "Kullanıcı: '2 ton akülü forklift'";
        $prompts[] = "Meilisearch: [transpalet-2ton, forklift-2ton, istif-2ton]";
        $prompts[] = "Sen göster: SADECE forklift-2ton (diğerleri YASAK!)";
        $prompts[] = "```";
        $prompts[] = "";

        // 3. Smart search results
        if (!empty($smartSearchResults['products'])) {
            $prompts[] = "## 🔍 İLGİLİ ÜRÜNLER (Smart Search)";
            $prompts[] = "";

            if ($detectedCategory) {
                $prompts[] = "**⚠️ KRİTİK: Kullanıcı '{$detectedCategory['category_name']}' kategorisinden ürün istedi!**";
                $prompts[] = "**MUTLAKA ÜRÜN LİSTESİ GÖSTER! Genel bilgi verme!**";
                $prompts[] = "";
                $prompts[] = "## 🤖 AI SEMANTIC MATCHING (ÇOK ÖNEMLİ!)";
                $prompts[] = "";
                $prompts[] = "Sana {$detectedCategory['category_name']} kategorisindeki **TÜM ÜRÜNLER** gönderiliyor (~300 ürün).";
                $prompts[] = "**GÖREVIN:** Kullanıcının isteğine EN UYGUN 3-5 ürünü SEÇ!";
                $prompts[] = "";
                $prompts[] = "**SEMANTIC MATCHING KURALLARI:**";
                $prompts[] = "";
                $prompts[] = "⚠️ **KRİTİK: YANLIŞ KELİME EŞLEŞTİRMELERİ YAPMA!**";
                $prompts[] = "❌ 'terazili' (weighing scale) ≠ 'denge ağırlıklı' (counterbalanced)";
                $prompts[] = "   - 'terazili' = tartı özelliği olan, ağırlık ölçen";
                $prompts[] = "   - 'denge ağırlıklı' = forklift tipi, tartı özelliği YOK";
                $prompts[] = "   - Bu iki terim TAMAMEN FARKLI! Karıştırma!";
                $prompts[] = "";
                $prompts[] = "❌ 'platform' ≠ 'palet'";
                $prompts[] = "❌ 'elektrikli' ≠ 'akülü' (bunlar aynı, eş anlamlı)";
                $prompts[] = "❌ 'manuel' ≠ 'yarı elektrikli'";
                $prompts[] = "";
                $prompts[] = "**Eğer kullanıcı 'terazili' dedi ve ürün listesinde 'terazi/tartı/weighing' kelimesi YOKSA:**";
                $prompts[] = "→ ÜRÜN ÖNERME! 'Ürün bulunamadı' mantığına geç, iletişim bilgilerini ver!";
                $prompts[] = "";
                $prompts[] = "1. 🔍 **SLUG'lara DİKKAT ET!** (En önemli ipucu!)";
                $prompts[] = "   - Kullanıcı 'soguk' dedi → 'soguk-depo' slug'u varsa onu seç!";
                $prompts[] = "   - Kullanıcı 'gida' dedi → 'gida' slug'u varsa onu seç!";
                $prompts[] = "   - Kullanıcı 'terazili' dedi → 'terazi/weighing/scale' slug'u varsa onu seç!";
                $prompts[] = "   - **TYPO TOLERANCE:** 'soguk' = 'soğuk', 'gida' = 'gıda'";
                $prompts[] = "";
                $prompts[] = "2. 📝 **Title ve SKU'ya bak!** Özel kısaltmalar:";
                $prompts[] = "   - 'ETC' = Extreme Temperature Conditions = Soğuk depo";
                $prompts[] = "   - 'SS' = Stainless Steel = Paslanmaz çelik";
                $prompts[] = "   - 'AGM', 'Li-Ion' = Batarya tipleri";
                $prompts[] = "   - 'Scale/Weighing' = Terazili/Tartı özelliği";
                $prompts[] = "";
                $prompts[] = "3. 🎯 **ÖNCE SPESİFİK, SONRA GENEL!**";
                $prompts[] = "   - Kullanıcı 'soğuk depo' dedi → Slug/title'da 'soguk' veya 'ETC' olan VAR MI?";
                $prompts[] = "   - Kullanıcı 'terazili' dedi → Slug/title/body'de 'terazi/weighing/scale' VAR MI?";
                $prompts[] = "   - **VARSA:** O ürünü göster! (Genel ürünler değil!)";
                $prompts[] = "   - **YOKSA:** 'Ürün bulunamadı' yanıtı ver, iletişim bilgilerini göster";
                $prompts[] = "";
                $prompts[] = "**❌ YAPMA:**";
                $prompts[] = "- İlk gördüğün genel ürünleri gösterip geç!";
                $prompts[] = "- Slug'ları görmezden gel!";
                $prompts[] = "- Manuel olarak typo'ları eşleştirmeye çalış (bunu ben yaparım!)";
                $prompts[] = "";
                $prompts[] = "**✅ YAP:**";
                $prompts[] = "- TÜM ürünleri tara!";
                $prompts[] = "- Slug'larda anahtar kelimeleri ara!";
                $prompts[] = "- Semantic eşleştirme yap (sen AI'sın, yapabilirsin!)";
                $prompts[] = "- EN UYGUN 3-5 ürünü seç ve göster!";
                $prompts[] = "";
                $prompts[] = "**{$detectedCategory['category_name']} kategorisindeki TÜM ÜRÜNLER (sen en uygunları seç!):**";
            } else {
                $prompts[] = "**SADECE bu ürünleri öner (başka ürün arama!):**";
            }
            $prompts[] = "";

            foreach ($smartSearchResults['products'] as $product) {
                $prompts[] = self::formatProductForPrompt($product);
            }

            $prompts[] = "";
            $prompts[] = "⚠️ **TEKRAR:** Yukarıdaki ürün listesini MUTLAKA göster! Genel bilgi değil, SPESİFİK ÜRÜNLER!";
            $prompts[] = "";
        } else {
            // No products found - NEVER say "product not found"!
            // Get dynamic contact info from settings (same as "ANLAMADIĞIM TERİM" section)
            $contactInfo = \App\Helpers\AISettingsHelper::getContactInfo();

            $prompts[] = "## 📦 ÜRÜN BULUNAMADI - ÖZEL YANIT";
            $prompts[] = "";

            if ($detectedCategory) {
                $prompts[] = "⚠️ **'{$detectedCategory['category_name']}' kategorisinde sistemde ürün yok!**";
                $prompts[] = "";
                $prompts[] = "**ZORUNLU YANIT KURALLARI:**";
                $prompts[] = "1. ❌ ASLA 'ürün bulunamadı' DEME!";
                $prompts[] = "2. ❌ ASLA 'sistemde yok' DEME!";
                $prompts[] = "3. ❌ ASLA 'Ancak şu anda elimizde ... bulunduğuna dair bir bilgi yok' gibi olumsuz cümleler kullanma!";
                $prompts[] = "4. ✅ MUTLAKA pozitif ve çözüm odaklı ol: 'Size özel bulabiliriz', 'Yardımcı olabiliriz'";
                $prompts[] = "5. ✅ MUTLAKA iletişim bilgilerini ver (dinamik olarak eklendi)";
                $prompts[] = "6. ✅ Pozitif ve yardımcı ol, müşteriyi kaçırma!";
                $prompts[] = "";
                $prompts[] = "**ZORUNLU YANIT FORMATI (OKUNAKLI!):**";
                $prompts[] = "```";
                $prompts[] = "İxtif olarak, '{$detectedCategory['category_name']}' konusunda müşteri temsilcilerimiz size özel araştırma yapabilir! 😊";
                $prompts[] = "";
                $prompts[] = "Detaylı bilgi almak ve size en uygun çözümleri sunabilmek için ekibimizle iletişime geçmenizi öneriyoruz.";
                $prompts[] = "";
                $prompts[] = "---";
                $prompts[] = "";
                $prompts[] = "**Hemen iletişime geçin:**";
                $prompts[] = "";

                // Format contact information dynamically - HER BİRİ AYRI SATIR!
                if (!empty($contactInfo['whatsapp'])) {
                    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $contactInfo['whatsapp']);
                    $prompts[] = "💬 **WhatsApp:**";
                    $prompts[] = "[" . $contactInfo['whatsapp'] . "](https://wa.me/{$cleanWhatsapp})";
                    $prompts[] = "";
                }
                if (!empty($contactInfo['email'])) {
                    $prompts[] = "📧 **E-posta:**";
                    $prompts[] = "[{$contactInfo['email']}](mailto:{$contactInfo['email']})";
                    $prompts[] = "";
                }
                if (!empty($contactInfo['phone'])) {
                    $cleanPhone = preg_replace('/[^0-9+]/', '', $contactInfo['phone']);
                    $prompts[] = "📞 **Telefon:**";
                    $prompts[] = "[" . $contactInfo['phone'] . "](tel:{$cleanPhone})";
                    $prompts[] = "";
                }

                // Fallback if no contact info available
                if (empty($contactInfo['phone']) && empty($contactInfo['whatsapp']) && empty($contactInfo['email'])) {
                    $prompts[] = "📞 **İletişim:** Lütfen müşteri temsilcimizle görüşün";
                }

                $prompts[] = "";
                $prompts[] = "Size özel fiyat teklifi ve ürün önerileri hazırlayabiliriz!";
                $prompts[] = "Hangi özellikleri arıyorsunuz? Detaylı bilgi verirseniz daha iyi yardımcı olabiliriz.";
                $prompts[] = "```";
                $prompts[] = "";
            } else {
                // General "no product" case - also use dynamic contact info
                $prompts[] = "**ZORUNLU YANIT KURALLARI:**";
                $prompts[] = "1. ❌ ASLA 'ürün bulunamadı' DEME!";
                $prompts[] = "2. ❌ ASLA 'sistemde yok' veya 'bilgi yok' DEME!";
                $prompts[] = "3. ✅ MUTLAKA pozitif ve çözüm odaklı: 'Size yardımcı olabiliriz'";
                $prompts[] = "4. ✅ MUTLAKA iletişim bilgilerini göster (aşağıda dinamik olarak eklendi)";
                $prompts[] = "";
                $prompts[] = "**ZORUNLU YANIT FORMATI:**";
                $prompts[] = "```";
                $prompts[] = "İxtif olarak, müşteri temsilcilerimiz size özel araştırma yapabilir! 😊";
                $prompts[] = "";
                $prompts[] = "Detaylı bilgi ve size en uygun çözümleri sunabilmek için ekibimizle görüşebilirsiniz:";
                $prompts[] = "";

                // Add dynamic contact info - each on separate line for readability
                if (!empty($contactInfo['whatsapp'])) {
                    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $contactInfo['whatsapp']);
                    $prompts[] = "💬 **WhatsApp:**";
                    $prompts[] = "[" . $contactInfo['whatsapp'] . "](https://wa.me/{$cleanWhatsapp})";
                    $prompts[] = "";
                }
                if (!empty($contactInfo['email'])) {
                    $prompts[] = "📧 **E-posta:**";
                    $prompts[] = "[{$contactInfo['email']}](mailto:{$contactInfo['email']})";
                    $prompts[] = "";
                }
                if (!empty($contactInfo['phone'])) {
                    $cleanPhone = preg_replace('/[^0-9+]/', '', $contactInfo['phone']);
                    $prompts[] = "📞 **Telefon:**";
                    $prompts[] = "[" . $contactInfo['phone'] . "](tel:{$cleanPhone})";
                    $prompts[] = "";
                }

                if (empty($contactInfo['phone']) && empty($contactInfo['whatsapp']) && empty($contactInfo['email'])) {
                    $prompts[] = "📞 **İletişim:** Lütfen müşteri temsilcimizle görüşün";
                }

                $prompts[] = "```";
                $prompts[] = "";
            }

            if (!empty($aiContext['context']['modules']['shop']['categories'])) {
                $prompts[] = "**Alternatif olarak mevcut kategorilerimiz:**";
                foreach ($aiContext['context']['modules']['shop']['categories'] as $category) {
                    $prompts[] = "- {$category['name']} ({$category['product_count']} ürün)";
                }
                $prompts[] = "";
            }
        }

        // 3. Conversation flow guidance
        $prompts[] = self::buildConversationFlowGuidance();

        // 4. Special scenarios
        $prompts[] = self::buildSpecialScenarios();

        return implode("\n", $prompts);
    }

    /**
     * Build sentiment-based response guidance
     */
    protected static function buildSentimentGuidance(array $sentiment): string
    {
        $tone = $sentiment['tone'] ?? 'neutral';
        $prompts = [];

        $prompts[] = "## 🎭 KULLANICI TONU: " . strtoupper($tone);
        $prompts[] = "";

        switch ($tone) {
            case 'polite':
                $prompts[] = "**Kullanıcı kibar → Aynı kibar tonda yanıt ver**";
                $prompts[] = "- 'Tabii ki!' ile başla";
                $prompts[] = "- '😊' emoji kullan";
                $prompts[] = "- Detaylı ve özenli bilgi ver";
                break;

            case 'rude':
                $prompts[] = "**Kullanıcı kaba → Sakin ve profesyonel kal**";
                $prompts[] = "- Kısa ve net yanıt ver";
                $prompts[] = "- Emoji kullanma";
                $prompts[] = "- Direkt bilgi ver, fazla soru sorma";
                break;

            case 'urgent':
                $prompts[] = "**Kullanıcı acele ediyor → Hızlı yanıt ver**";
                $prompts[] = "- 'Hemen yardımcı oluyorum' de";
                $prompts[] = "- Direkt ürün + fiyat bilgisi ver";
                $prompts[] = "- ❗ ZORUNLU: İletişim bilgilerini MUTLAKA ekle (WhatsApp/Telefon/E-posta)";
                $prompts[] = "- Acil için 'Hemen arayın' çağrısı yap";
                $prompts[] = "";
                $prompts[] = "**ZORUNLU İLETİŞİM BİLGİSİ FORMATI:**";
                $prompts[] = "```";
                $prompts[] = "⚡ ACİL DESTEK İÇİN:";
                $prompts[] = "📞 Telefon: [TELEFON]";
                $prompts[] = "💬 WhatsApp: [WHATSAPP LINK]";
                $prompts[] = "📧 E-posta: [EMAIL]";
                $prompts[] = "Hemen size yardımcı olalım! 🚀";
                $prompts[] = "```";
                break;

            case 'confused':
                $prompts[] = "**Kullanıcı kararsız → Yönlendirici ol**";
                $prompts[] = "- Sabırlı ve yönlendirici";
                $prompts[] = "- Karar vermesine yardımcı ol";
                $prompts[] = "- Karşılaştırma yap";
                break;

            default:
                $prompts[] = "**Kullanıcı nötr → Standart profesyonel ton**";
                $prompts[] = "- Samimi ve yardımsever";
                $prompts[] = "- Detayları sor";
                break;
        }

        $prompts[] = "";
        return implode("\n", $prompts);
    }

    /**
     * Format single product for prompt (compact)
     */
    protected static function formatProductForPrompt(array $product): string
    {
        $lines = [];

        // Handle multi-language title (JSON)
        $title = $product['title'];
        if (is_array($title)) {
            // Get Turkish title or first available
            $title = $title['tr'] ?? $title['en'] ?? reset($title) ?? 'Product';
        }

        // 🚨 KRİTİK: NOKTA TEMİZLEME (2. ton → 2 ton, 2.0 ton → 2 ton)
        // Pattern 1: "2. ton" → "2 ton" (sayı + nokta + boşluk)
        $title = preg_replace('/(\d+)\.(\s+)/u', '$1$2', $title);
        // Pattern 2: "2.0. ton" → "2.0 ton" (ondalık + nokta + boşluk)
        $title = preg_replace('/(\d+\.\d+)\.(\s+)/u', '$1$2', $title);
        // Pattern 3: "2.0 ton" → "2 ton" (ondalık sıfırları temizle)
        $title = preg_replace('/(\d+)\.0(\s+)/u', '$1$2', $title);

        // Handle slug (should be string, but check anyway)
        $slug = $product['slug'];
        if (is_array($slug)) {
            $slug = $slug['tr'] ?? $slug['en'] ?? reset($slug) ?? 'product';
        }

        $lines[] = "**{$title}** [LINK:shop:{$slug}]";

        // ⚠️ KRİTİK: Slug'u göster! AI semantic matching için slug'a bakacak!
        $lines[] = "  - Slug: {$slug}";

        if (!empty($product['sku'])) {
            $lines[] = "  - SKU: {$product['sku']}";
        }

        // ⚠️ KRİTİK: Ürün açıklamalarını ekle (voltage/specs bilgileri burada!)
        if (!empty($product['short_description'])) {
            $desc = $product['short_description'];
            if (is_array($desc)) {
                $desc = $desc['tr'] ?? $desc['en'] ?? reset($desc) ?? '';
            }
            if (!empty($desc)) {
                // Sadece HTML temizle, kesme! Chatbot zaten token limiti kontrol eder
                $desc = strip_tags($desc);
                // Çok uzun metinler için makul bir üst limit (2000 karakter)
                if (mb_strlen($desc) > 2000) {
                    $desc = mb_substr($desc, 0, 2000) . '... (Devamı için ürün sayfasına bakın)';
                }
                $lines[] = "  - Kısa Açıklama: {$desc}";
            }
        }

        // Full description (body) - AKILLI PARSE!
        // ⚠️ KRİTİK: Body alanı JSON + HTML + çok uzun (3000+ karakter)
        // Strateji: Sadece ilk section'ı al (ana özet), teknik detayları ATLA!
        if (!empty($product['description'])) {
            $fullDesc = $product['description'];
            if (is_array($fullDesc)) {
                $fullDesc = $fullDesc['tr'] ?? $fullDesc['en'] ?? reset($fullDesc) ?? '';
            }
            if (!empty($fullDesc)) {
                // AKILLI BODY PARSE: Section bazlı
                $parsedBody = self::parseBodySmart($fullDesc);
                if (!empty($parsedBody)) {
                    $lines[] = "  - Detaylı Açıklama: {$parsedBody}";
                }
            }
        }

        // ⚠️ KRİTİK: TÜM Technical specs (voltage, battery, vs.)
        if (!empty($product['custom_technical_specs'])) {
            $specs = $product['custom_technical_specs'];

            // Tüm spec'leri dinamik olarak ekle
            foreach ($specs as $key => $value) {
                if (!empty($value) && is_string($value)) {
                    // Key'i Türkçe label'a çevir
                    $label = match($key) {
                        'capacity' => 'Kapasite',
                        'lift_height' => 'Kaldırma Yüksekliği',
                        'voltage' => 'Voltaj',
                        'battery_type' => 'Batarya Tipi',
                        'battery_capacity' => 'Batarya Kapasitesi',
                        'fork_length' => 'Çatal Uzunluğu',
                        'fork_width' => 'Çatal Genişliği',
                        'weight' => 'Ağırlık',
                        'dimensions' => 'Boyutlar',
                        'max_speed' => 'Maksimum Hız',
                        'drive_type' => 'Tahrik Tipi',
                        'control_type' => 'Kontrol Tipi',
                        default => ucfirst(str_replace('_', ' ', $key))
                    };
                    $lines[] = "  - {$label}: {$value}";
                }
            }
        }

        // Custom features (özellikler)
        if (!empty($product['custom_features']) && is_array($product['custom_features'])) {
            $features = array_filter($product['custom_features']);
            if (!empty($features)) {
                $lines[] = "  - Özellikler: " . implode(', ', array_slice($features, 0, 5));
            }
        }

        // Tags (etiketler - arama için önemli!)
        if (!empty($product['tags'])) {
            $tags = is_array($product['tags']) ? implode(', ', $product['tags']) : $product['tags'];
            $lines[] = "  - Etiketler: {$tags}";
        }

        // Price info - ⚠️ KRİTİK: base_price > 0 kontrolü (0 veya null ise gösterme!)
        // ⚠️ EKSTRA KORUMA: AI'ın fiyat uydurmamasi için "price_on_request" bilgisini açıkça belirt
        if (!empty($product['price_on_request'])) {
            // Önce "price_on_request" kontrol et - Bu durumda ASLA rakam gösterme!
            $lines[] = "  - Fiyat: ⚠️ Talep üzerine (ASLA fiyat uydurma! İletişim bilgisi ver!)";
        } elseif (isset($product['base_price']) && $product['base_price'] > 0) {
            // ⚠️ KRİTİK: Currency field'ını standardize et
            $currency = $product['currency'] ?? 'TRY';

            // 🆕 STANDARDIZE: $ sembolü yerine USD, € yerine EUR, ₺ yerine TL
            $currencySymbol = match(strtoupper($currency)) {
                'USD', '$', 'DOLLAR' => ' USD',
                'EUR', '€', 'EURO' => ' EUR',
                'TRY', 'TL', '₺', 'LIRA' => ' TL',
                'GBP', '£', 'POUND' => ' GBP',
                default => " " . strtoupper($currency)
            };

            $priceText = number_format($product['base_price'], 0, ',', '.') . $currencySymbol;

            // İndirim varsa göster
            if (isset($product['compare_at_price']) && $product['compare_at_price'] > $product['base_price']) {
                $discount = round((($product['compare_at_price'] - $product['base_price']) / $product['compare_at_price']) * 100);
                $priceText .= " (İndirimli! Eski fiyat: " . number_format($product['compare_at_price'], 0, ',', '.') . " {$currency} - %{$discount} indirim)";
            }

            $lines[] = "  - Fiyat: {$priceText}";

            // Taksit bilgisi
            if (!empty($product['installment_available']) && !empty($product['max_installments'])) {
                $installmentAmount = $product['base_price'] / $product['max_installments'];
                $lines[] = "  - Taksit: {$product['max_installments']}x " . number_format($installmentAmount, 0, ',', '.') . " {$currency}";
            }

            // Depozito bilgisi
            if (!empty($product['deposit_required'])) {
                if (!empty($product['deposit_amount'])) {
                    $lines[] = "  - Depozito: " . number_format($product['deposit_amount'], 0, ',', '.') . " {$currency} gereklidir";
                } elseif (!empty($product['deposit_percentage'])) {
                    $lines[] = "  - Depozito: %{$product['deposit_percentage']} ön ödeme gereklidir";
                }
            }
        } else {
            // base_price yok veya 0 - ASLA fiyat gösterme!
            $lines[] = "  - Fiyat: ⚠️ Bilgi için iletişime geçin (ASLA fiyat uydurma!)";
        }

        // Stok durumu - ⚠️ ÖNEMLİ: Müşteri stok bilgisi görmek ister!
        if (!empty($product['stock_tracking'])) {
            $stockStatus = '';
            $currentStock = $product['current_stock'] ?? 0;
            $lowThreshold = $product['low_stock_threshold'] ?? 5;

            if ($currentStock > $lowThreshold) {
                $stockStatus = "✅ Stokta var ({$currentStock} adet)";
            } elseif ($currentStock > 0) {
                $stockStatus = "⚠️ Son {$currentStock} adet!";
            } elseif (!empty($product['allow_backorder'])) {
                $stockStatus = "📦 Ön siparişle temin edilebilir";
                if (!empty($product['lead_time_days'])) {
                    $stockStatus .= " ({$product['lead_time_days']} gün içinde)";
                }
            } else {
                $stockStatus = "❌ Stokta yok";
            }

            $lines[] = "  - Stok: {$stockStatus}";
        }

        // Ürün durumu (Yeni/İkinci El/Yenilenmiş)
        if (!empty($product['condition'])) {
            $conditionLabel = match($product['condition']) {
                'new' => '🆕 Sıfır/Yeni',
                'used' => '♻️ İkinci El',
                'refurbished' => '🔧 Yenilenmiş',
                default => $product['condition']
            };
            $lines[] = "  - Durum: {$conditionLabel}";
        }

        // Özel badge'ler (Öne Çıkan / Çok Satan)
        $badges = [];
        if (!empty($product['is_featured'])) {
            $badges[] = '⭐ Öne Çıkan';
        }
        if (!empty($product['is_bestseller'])) {
            $badges[] = '🔥 Çok Satan';
        }
        if (!empty($badges)) {
            $lines[] = "  - Özel: " . implode(', ', $badges);
        }

        // Garanti bilgisi - ⚠️ ÖNEMLİ: Müşteriler garanti sorar! KESME!
        if (!empty($product['warranty_info'])) {
            $warranty = $product['warranty_info'];
            if (is_array($warranty)) {
                $warranty = $warranty['tr'] ?? $warranty['en'] ?? reset($warranty) ?? '';
            }
            if (!empty($warranty)) {
                // KRİTİK BİLGİ: Garanti bilgisi kesilmemeli! Tam metin göster
                $warranty = strip_tags($warranty);
                // Sadece çok aşırı uzun metinler için güvenlik limiti (1000 karakter)
                if (mb_strlen($warranty) > 1000) {
                    $warranty = mb_substr($warranty, 0, 1000) . '... (Tam garanti bilgisi için ürün sayfasına bakın)';
                }
                $lines[] = "  - Garanti: {$warranty}";
            }
        }

        // Kargo bilgisi - ⚠️ ÖNEMLİ: Müşteriler kargo sorar! KESME!
        if (!empty($product['shipping_info'])) {
            $shipping = $product['shipping_info'];
            if (is_array($shipping)) {
                $shipping = $shipping['tr'] ?? $shipping['en'] ?? reset($shipping) ?? '';
            }
            if (!empty($shipping)) {
                // KRİTİK BİLGİ: Kargo bilgisi kesilmemeli! Tam metin göster
                $shipping = strip_tags($shipping);
                // Sadece çok aşırı uzun metinler için güvenlik limiti (1000 karakter)
                if (mb_strlen($shipping) > 1000) {
                    $shipping = mb_substr($shipping, 0, 1000) . '... (Tam kargo bilgisi için ürün sayfasına bakın)';
                }
                $lines[] = "  - Kargo: {$shipping}";
            }
        }

        // Tedarik süresi (backorder değilse ama lead time varsa)
        if (empty($product['allow_backorder']) && !empty($product['lead_time_days']) && $product['lead_time_days'] > 0) {
            $lines[] = "  - Teslimat: {$product['lead_time_days']} iş günü içinde";
        }

        $lines[] = "";
        return implode("\n", $lines);
    }

    /**
     * AKILLI BODY PARSE
     *
     * Body alanı JSON + HTML + section'lardan oluşuyor (3000+ karakter)
     * Strateji:
     * 1. İlk section'ı al (ana özet/tanıtım)
     * 2. Teknik detayları ATLA (zaten technical_specs'te var)
     * 3. İletişim bölümünü ATLA (gereksiz)
     * 4. Max 800 karakter (token optimizasyonu)
     * 5. Akıllı kesme (cümle sonunda)
     */
    protected static function parseBodySmart(string $htmlContent): string
    {
        // 1. HTML temizle
        $htmlContent = strip_tags($htmlContent);

        // 2. Boşlukları normalize et
        $htmlContent = preg_replace('/\s+/', ' ', $htmlContent);
        $htmlContent = trim($htmlContent);

        // 3. Eğer kısa ise direkt döndür
        if (mb_strlen($htmlContent) <= 800) {
            return $htmlContent;
        }

        // 4. Metni paragraf veya section'lara böl
        // "Teknik" veya "İletişim" başlıklı bölümleri tespit et
        $sections = [];

        // Başlıkları bul (örn: "Teknik Güç ve Mimari", "Sonuç ve İletişim")
        if (preg_match('/^(.*?)(?:Teknik|İletişim|Sonuç|İrtibat|Detay)/iu', $htmlContent, $matches)) {
            // İlk bölümü al (teknik detaylardan öncesi)
            $firstSection = trim($matches[1]);
        } else {
            // Başlık bulunamadı, ilk 800 karakteri al
            $firstSection = $htmlContent;
        }

        // 5. İlk section'ı max 800 karakterde akıllı kes
        if (mb_strlen($firstSection) > 800) {
            // Cümle sonunda kes (nokta, ünlem, soru işareti)
            $shortened = mb_substr($firstSection, 0, 800);

            // Son nokta, ünlem veya soru işaretini bul
            $lastPeriod = max(
                mb_strrpos($shortened, '.'),
                mb_strrpos($shortened, '!'),
                mb_strrpos($shortened, '?')
            );

            if ($lastPeriod !== false && $lastPeriod > 400) {
                // Cümle sonunda kes (en az 400 karakter varsa)
                $firstSection = mb_substr($shortened, 0, $lastPeriod + 1);
            } else {
                // Cümle sonu bulunamadı, kelime sonunda kes
                $lastSpace = mb_strrpos($shortened, ' ');
                if ($lastSpace !== false && $lastSpace > 400) {
                    $firstSection = mb_substr($shortened, 0, $lastSpace);
                } else {
                    $firstSection = $shortened;
                }
            }

            // Devamı olduğunu belirt
            $firstSection .= '... (Detaylı teknik bilgi için ürün sayfasına bakın)';
        }

        return $firstSection;
    }

    /**
     * Build conversation flow guidance (100 satır)
     */
    protected static function buildConversationFlowGuidance(): string
    {
        $prompts = [];

        $prompts[] = "## 🔄 KONUŞMA AKIŞI";
        $prompts[] = "";

        // Scenario 1: First greeting
        $prompts[] = "### 1️⃣ İLK SELAMLAŞMA (KRİTİK!)";
        $prompts[] = "**Kullanıcı:** 'Merhaba' / 'Selam' / 'Günaydın' / 'İyi günler'";
        $prompts[] = "";
        $prompts[] = "**🚨 ZORUNLU YANIT (SADECE BU!):**";
        $prompts[] = "```";
        $prompts[] = "Merhaba! Size nasıl yardımcı olabilirim? 😊";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**❌ KESİNLİKLE YASAKLAR:**";
        $prompts[] = "1. ❌ ÜRÜN LİSTESİ GÖSTERME! (0 ürün göster!)";
        $prompts[] = "2. ❌ Kategori adı söyleme! ('transpalet', 'forklift' vb. YASAK!)";
        $prompts[] = "3. ❌ Ürün önerme! ('Ürünlerimiz', 'Bakabilirsiniz' YASAK!)";
        $prompts[] = "4. ❌ Detaylı açıklama yapma!";
        $prompts[] = "5. ❌ Fazla soru sorma!";
        $prompts[] = "";
        $prompts[] = "**✅ SADECE:**";
        $prompts[] = "- Tek cümle: 'Merhaba! Size nasıl yardımcı olabilirim? 😊'";
        $prompts[] = "- HIÇBIR ŞEY EKLEME!";
        $prompts[] = "- Kullanıcı ne istediğini söyleyene kadar BEKLe!";
        $prompts[] = "";

        // Scenario 2: General category request
        $prompts[] = "### 2️⃣ GENEL KATEGORI TALEBİ (DETAYSIZ!)";
        $prompts[] = "**Kullanıcı:** 'Transpalet istiyorum' / 'Forklift arıyorum' / 'Terazi lazım'";
        $prompts[] = "";
        $prompts[] = "**🚨 KRİTİK KURAL: ÜRÜN GÖSTERME, DETAY İSTE!**";
        $prompts[] = "";
        $prompts[] = "**❌ YAPMA:**";
        $prompts[] = "- ❌ Direkt ürün listesi gösterme!";
        $prompts[] = "- ❌ 'İşte ürünlerimiz' deme!";
        $prompts[] = "- ❌ Tüm kategoriyi listeleme!";
        $prompts[] = "";
        $prompts[] = "**✅ ZORUNLU AKIŞ:**";
        $prompts[] = "1. Kullanıcıyı karşıla: 'Tabii, size yardımcı olabilirim! 😊'";
        $prompts[] = "2. KATEGORİYE GÖRE SORU SOR (her kategori farklı!):";
        $prompts[] = "";
        $prompts[] = "**TRANSPALET:**";
        $prompts[] = "- Kapasite? (2 ton, 3 ton?)";
        $prompts[] = "- Manuel mi elektrikli mi?";
        $prompts[] = "- Kullanım yeri? (Soğuk depo, normal depo?)";
        $prompts[] = "";
        $prompts[] = "**FORKLIFT:**";
        $prompts[] = "- Kapasite? (2 ton, 3 ton, 5 ton?)";
        $prompts[] = "- Kaldırma yüksekliği? (3m, 5m, 6m?)";
        $prompts[] = "- LPG/Dizel/Elektrikli?";
        $prompts[] = "- İç mekan mı dış mekan mı?";
        $prompts[] = "";
        $prompts[] = "**İSTİF MAKİNESİ:**";
        $prompts[] = "- Kapasite?";
        $prompts[] = "- Kaldırma yüksekliği?";
        $prompts[] = "- Manuel/Yarı elektrikli/Tam elektrikli?";
        $prompts[] = "";
        $prompts[] = "**AĞIRLIK SİSTEMLERİ (Baskül/Terazi):**";
        $prompts[] = "- Kapasite? (Max kaç kg tartacak?)";
        $prompts[] = "- Platform boyutu?";
        $prompts[] = "- Hassasiyet? (1g, 10g, 100g?)";
        $prompts[] = "";
        $prompts[] = "3. Kullanıcı DETAY verene kadar ÜRÜN GÖSTERME!";
        $prompts[] = "";
        $prompts[] = "**ÖRNEK DOĞRU YANITLAR:**";
        $prompts[] = "";
        $prompts[] = "**Transpalet için:**";
        $prompts[] = "```";
        $prompts[] = "Tabii, size yardımcı olabilirim! 😊";
        $prompts[] = "";
        $prompts[] = "Size en uygun transpaleti bulabilmem için:";
        $prompts[] = "- Kaç ton taşıma kapasitesi istiyorsunuz? (2 ton, 3 ton?)";
        $prompts[] = "- Manuel mi yoksa elektrikli mi tercih edersiniz?";
        $prompts[] = "- Nerede kullanacaksınız? (Soğuk depo gibi özel alan var mı?)";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**Forklift için:**";
        $prompts[] = "```";
        $prompts[] = "Tabii, size yardımcı olabilirim! 😊";
        $prompts[] = "";
        $prompts[] = "Size en uygun forklifti bulabilmem için:";
        $prompts[] = "- Kaç ton yük kaldıracaksınız?";
        $prompts[] = "- Kaldırma yüksekliği kaç metre olmalı?";
        $prompts[] = "- LPG, dizel veya elektrikli mi tercih edersiniz?";
        $prompts[] = "- İç mekan mı dış mekan mı kullanacaksınız?";
        $prompts[] = "```";
        $prompts[] = "";

        // Scenario 3: Detailed request
        $prompts[] = "### 3️⃣ DETAYLI TALEP";
        $prompts[] = "**Kullanıcı:** '2 ton elektrikli transpalet lazım'";
        $prompts[] = "**AKIŞ:**";
        $prompts[] = "1. Smart search sonucuna bak (MUTLAKA!)";
        $prompts[] = "2. İlgili ürünleri sırala";
        $prompts[] = "3. En çok eşleşeni öne çıkar";
        $prompts[] = "4. Fiyat bilgisi varsa göster";
        $prompts[] = "";

        // Scenario 3B: Service request (NEW!)
        $prompts[] = "### 3️⃣-B HİZMET TALEBİ";
        $prompts[] = "**Kullanıcı:** 'Kiralama yapıyorsunuz?' / 'Teknik servis var mı?' / 'Yedek parça bulabilir miyim?'";
        $prompts[] = "**AKIŞ:**";
        $prompts[] = "1. ✅ Knowledge Base'de bu hizmet bilgisi VAR!";
        $prompts[] = "2. Hizmet hakkında bilgi ver (kiralama süreleri, servis detayları, vb.)";
        $prompts[] = "3. İhtiyacına göre ürün öner (kiralama için hangi ekipmanları kiralariz)";
        $prompts[] = "4. İletişim bilgisi ekle (detaylı bilgi için)";
        $prompts[] = "";
        $prompts[] = "**ÖRNEKLER:**";
        $prompts[] = "'Kiralama yapmak istiyorum' →";
        $prompts[] = "  ✅ 'Evet, günlük, haftalık, aylık ve yıllık kiralama seçeneklerimiz var!'";
        $prompts[] = "  ✅ Ardından: 'Hangi ekipmanı kiralamak istersiniz? (Transpalet, forklift, vb.)'";
        $prompts[] = "";
        $prompts[] = "'Teknik servis hizmetiniz var mı?' →";
        $prompts[] = "  ✅ 'Evet, 7/24 teknik servis hizmetimiz mevcuttur. Tüm marka ve modellerde...'";
        $prompts[] = "";
        $prompts[] = "'Yedek parça' →";
        $prompts[] = "  ✅ 'Orijinal ve yan sanayi yedek parça tedariki yapıyoruz...'";
        $prompts[] = "";

        // Scenario 4: Specific product request
        $prompts[] = "### 4️⃣ SPESİFİK ÜRÜN TALEBİ";
        $prompts[] = "**Kullanıcı:** 'f4201 hakkında' / 'F4-201 var mı?'";
        $prompts[] = "**AKIŞ:**";
        $prompts[] = "1. Smart search MUTLAKA bulmuştur";
        $prompts[] = "2. Ürün detaylarını göster";
        $prompts[] = "3. Fiyat + Link ver";
        $prompts[] = "";

        // Scenario 5: Product page conversation
        $prompts[] = "### 5️⃣ ÜRÜN SAYFASINDA KONUŞMA";
        $prompts[] = "**Kullanıcı:** (Ürün sayfasında) 'Fiyatı nedir?'";
        $prompts[] = "**AKIŞ:**";
        $prompts[] = "1. Ürün adını kullan";
        $prompts[] = "2. Fiyat bilgisi varsa göster";
        $prompts[] = "3. 'Fiyat talep üzerine' ise iletişim ver";
        $prompts[] = "";

        return implode("\n", $prompts);
    }

    /**
     * Build special scenarios (50 satır)
     */
    protected static function buildSpecialScenarios(): string
    {
        $prompts = [];

        $prompts[] = "## ⚠️ ÖZEL DURUMLAR";
        $prompts[] = "";

        // Multiple products request
        $prompts[] = "### BİRDEN FAZLA ÜRÜN";
        $prompts[] = "**Kullanıcı:** '2 ton transpalet + 3 ton forklift'";
        $prompts[] = "→ Her ikisini de ayrı ayrı göster";
        $prompts[] = "→ Toplu alım indirimi için iletişim bilgisi ver";
        $prompts[] = "";

        // Budget request
        $prompts[] = "### BÜTÇE TALEBİ";
        $prompts[] = "**Kullanıcı:** '40.000 TL bütçem var'";
        $prompts[] = "→ Bütçeye uygun ürünleri göster";
        $prompts[] = "→ Bütçe sınırında olanları öne çıkar";
        $prompts[] = "";

        // Off-topic question (genuine off-topic like politics, weather)
        $prompts[] = "### KONU DIŞI SORU (Siyaset, Din, Hava Durumu)";
        $prompts[] = "**Kullanıcı:** 'Hava durumu?' / 'Siyaset?' / 'Futbol?'";
        $prompts[] = "**ZORUNLU YANIT:**";
        $prompts[] = "'Üzgünüm, ben sadece şirket ürünleri ve hizmetleri hakkında bilgi verebilirim.";
        $prompts[] = "**Ürünlerimiz:** Transpaletler, forkliftler, istif makineleri, reach truck";
        $prompts[] = "**Hizmetlerimiz:** Kiralama (günlük/haftalık/aylık/yıllık), teknik servis, yedek parça, 2. el alım-satım";
        $prompts[] = "Size nasıl yardımcı olabilirim? 😊'";
        $prompts[] = "";

        // Unknown term/product request (CRITICAL!)
        // Get dynamic contact info from settings
        $contactInfo = \App\Helpers\AISettingsHelper::getContactInfo();

        $prompts[] = "### ANLAMADIĞIM TERİM VEYA ÜRÜN (ÖNEMLİ!)";
        $prompts[] = "**Kullanıcı:** 'Blue spot' / 'Blue spot 1000' / 'XYZ parça' / Bilmediğin bir şey";
        $prompts[] = "**KRİTİK KURAL:**";
        $prompts[] = "❌ ASLA 'Ben sadece şirket ürünleri hakkında...' DEME!";
        $prompts[] = "❌ ASLA 'Anlamadım' DEME!";
        $prompts[] = "✅ MUTLAKA ÖNCE KULLANICININ NUMARASINI İSTE!";
        $prompts[] = "✅ Alamazsan İLETİŞİM BİLGİSİ VER (WhatsApp, E-posta, Telefon)!";
        $prompts[] = "";
        $prompts[] = "**ZORUNLU YANIT AKIŞI (OKUNAKLI FORMAT!):**";
        $prompts[] = "```";
        $prompts[] = "Bu konuda size yardımcı olmak isterim! 😊";
        $prompts[] = "";
        $prompts[] = "**Telefon numaranızı paylaşabilir misiniz?**";
        $prompts[] = "Size geri dönüş yapalım ve detaylı bilgi verelim.";
        $prompts[] = "";
        $prompts[] = "---";
        $prompts[] = "";
        $prompts[] = "**Eğer telefon paylaşmak istemezseniz, bize şu kanallardan ulaşabilirsiniz:**";
        $prompts[] = "";

        // Format contact information dynamically - HER BİRİ AYRI SATIR!
        if (!empty($contactInfo['whatsapp'])) {
            $cleanWhatsapp = preg_replace('/[^0-9]/', '', $contactInfo['whatsapp']);
            $prompts[] = "💬 **WhatsApp:**";
            $prompts[] = "[" . $contactInfo['whatsapp'] . "](https://wa.me/{$cleanWhatsapp})";
            $prompts[] = "";
        }
        if (!empty($contactInfo['email'])) {
            $prompts[] = "📧 **E-posta:**";
            $prompts[] = "[{$contactInfo['email']}](mailto:{$contactInfo['email']})";
            $prompts[] = "";
        }
        if (!empty($contactInfo['phone'])) {
            $cleanPhone = preg_replace('/[^0-9+]/', '', $contactInfo['phone']);
            $prompts[] = "📞 **Telefon:**";
            $prompts[] = "[" . $contactInfo['phone'] . "](tel:{$cleanPhone})";
            $prompts[] = "";
        }

        // Fallback if no contact info available
        if (empty($contactInfo['phone']) && empty($contactInfo['whatsapp']) && empty($contactInfo['email'])) {
            $prompts[] = "📞 **İletişim:** Lütfen müşteri temsilcimizle görüşün";
        }

        $prompts[] = "";
        $prompts[] = "Hangi ekipman için arıyorsunuz? Daha fazla detay verirseniz";
        $prompts[] = "size daha iyi yardımcı olabilirim!";
        $prompts[] = "```";
        $prompts[] = "";
        $prompts[] = "**ÖRNEKLER:**";
        $prompts[] = "- 'Blue spot' → ÖNCE numara iste + Alamazsan iletişim bilgisi ver (WhatsApp, E-posta, Telefon)";
        $prompts[] = "- 'Blue spot 1000' → ÖNCE numara iste + 'Hangi model için bu parça?' sor";
        $prompts[] = "- 'XYZ marka parça' → ÖNCE numara iste + Alamazsan iletişim kanallarını göster";
        $prompts[] = "- Bilmediğin marka/model → ÖNCE numara iste + Detay iste";
        $prompts[] = "";

        // Stock/delivery query
        $prompts[] = "### STOK/TESLİMAT SORGUSU";
        $prompts[] = "**Kullanıcı:** 'Stokta var mı?'";
        $prompts[] = "→ Satış ekibiyle iletişime geçmesini öner";
        $prompts[] = "→ Telefon/Email/WhatsApp bilgisi ver";
        $prompts[] = "";

        return implode("\n", $prompts);
    }

    /**
     * Get full optimized prompt
     */
    public static function getFullPrompt(array $aiContext, array $conversationHistory = []): string
    {
        $prompts = [];

        // 0. Firma Kimliği ve AI Ayarları (Tenant bazlı - Settings Group 9 & 10)
        $companyInfo = self::getCompanyInfo();
        if (!empty($companyInfo)) {
            $prompts[] = "# 🏢 FİRMA KİMLİĞİ VE AYARLARI";
            $prompts[] = "";

            // Firma adı ve sektör
            if (!empty($companyInfo['name'])) {
                $prompts[] = "**Firmanın Adı:** {$companyInfo['name']}";
                $prompts[] = "**Önemli:** Müşterilerle konuşurken firma adını kullan. Örnek: '{$companyInfo['name']} olarak...', 'Firmamızda...'";
                $prompts[] = "";
            }

            if (!empty($companyInfo['description'])) {
                $prompts[] = "**Firma Hakkında:** {$companyInfo['description']}";
                $prompts[] = "";
            }

            // ✅ AI Kişilik ayarları KALDIRILDI
            // ✅ Firma bilgileri artık ai_knowledge_base tablosunda (/admin/ai/knowledge-base)

            $prompts[] = "";

            // İletişim bilgileri (markdown link formatında)
            $contacts = [];
            if (!empty($companyInfo['whatsapp_1'])) {
                $phone = preg_replace('/[^0-9]/', '', $companyInfo['whatsapp_1']);
                $contacts[] = "💬 **WhatsApp:** [{$companyInfo['whatsapp_1']}](https://wa.me/{$phone})";
            }
            if (!empty($companyInfo['phone_1'])) {
                $phone = preg_replace('/[^0-9]/', '', $companyInfo['phone_1']);
                $contacts[] = "📞 **Telefon:** [{$companyInfo['phone_1']}](tel:{$phone})";
            }
            if (!empty($companyInfo['email_1'])) {
                $contacts[] = "📧 **E-posta:** [{$companyInfo['email_1']}](mailto:{$companyInfo['email_1']})";
            }
            if (!empty($companyInfo['address'])) {
                $contacts[] = "📍 **Adres:** {$companyInfo['address']}";
            }
            if (!empty($companyInfo['working_hours'])) {
                $contacts[] = "🕐 **Çalışma Saatleri:** {$companyInfo['working_hours']}";
            }

            if (!empty($contacts)) {
                $prompts[] = "**İletişim Bilgileri (Müşteri istediğinde AYNEN bu formatta ver!):**";
                foreach ($contacts as $contact) {
                    $prompts[] = $contact;
                }
                $prompts[] = "";
            }

            // ✅ Özel talimatlar KALDIRILDI - Kodda optimize edilmiş promptlar kullanılıyor
            // ✅ Yasaklı konular KALDIRILDI - Gerekirse ai_knowledge_base'e eklenebilir
            // ✅ Bilgi Bankası KALDIRILDI - ai_knowledge_base tablosu kullanılıyor (/admin/ai/knowledge-base)
            // ✅ Modül yetkileri KALDIRILDI - Workflow otomatik tespit ediyor

            $prompts[] = "---";
            $prompts[] = "";
        }

        // 1. System prompt (rules)
        $prompts[] = self::buildSystemPrompt();
        $prompts[] = "";

        // 2. Tenant-specific prompts (Factory Pattern - Dinamik)
        if (function_exists('tenant')) {
            $tenantService = \Modules\AI\App\Services\TenantServiceFactory::getPromptService();
            $prompts[] = implode("\n", $tenantService->buildPrompt());
            $prompts[] = "";
        }

        // 3. Conversation history check (prevent greeting repetition)
        if (!empty($conversationHistory)) {
            $hasGreeting = false;
            foreach ($conversationHistory as $msg) {
                if ($msg['role'] === 'assistant' && preg_match('/\b(merhaba|selam|iyi günler)/i', $msg['content'])) {
                    $hasGreeting = true;
                    break;
                }
            }

            if ($hasGreeting) {
                $prompts[] = "⚠️ KRİTİK: Bu konuşmanın DEVAMI! İlk mesajda zaten selamlaştın. Şimdi 'Merhaba' deme, direkt konuya gir!";
                $prompts[] = "";
            }
        }

        // 3. User context (products, sentiment, scenarios)
        $prompts[] = self::buildUserContext($aiContext);

        return implode("\n", $prompts);
    }
}
