<?php

namespace App\Services\AI\Tenants;

use App\Helpers\AISettingsHelper;

/**
 * Tenant 2 Blog Enhancement - ixtif.com
 *
 * Sektör: Endüstriyel Ekipman (Forklift, Transpalet, İstifleyici)
 * İçerik Tipi: Ürün teknik özellikleri, karşılaştırma, seçim rehberleri
 * Hedef Kitle: B2B (Satın alma müdürleri, depo yöneticileri, lojistik sorumlular)
 *
 * İçerik Kaynakları:
 * - Shop kategorileri (forklift, transpalet, istifleyici vb.)
 * - Shop ürünleri (spesifik modeller ve özellikleri)
 *
 * Hizmetler:
 * - Kiralama
 * - İkinci el satış
 * - Teknik servis
 * - Garanti (süre belirtmeden)
 * - Orijinal yedek parça
 */
class Tenant2BlogEnhancement
{
    /**
     * ixtif.com (Tenant 2) - Endüstriyel Ekipman Shop
     *
     * @return array Enhancement configuration
     */
    public function getEnhancement(): array
    {
        // AISettingsHelper'dan dinamik bilgileri al
        $companyInfo = AISettingsHelper::getCompanyContext();
        $targetInfo = AISettingsHelper::getTargetAudience();

        return [
            // ========================================
            // SEKTÖR & FİRMA BİLGİLERİ
            // ========================================
            'sector' => $companyInfo['sector'] ?? 'Endüstriyel ekipman ve iş makineleri',
            'company_name' => $companyInfo['name'] ?? 'iXtif',
            'expertise' => $companyInfo['expertise'] ?? 'Forklift, transpalet, istifleyici satış ve kiralama',
            'main_services' => $companyInfo['main_services'] ?? 'Endüstriyel ekipman satışı, kiralama ve teknik servis',

            // ========================================
            // İÇERİK KAYNAKLARI (Blog konuları için)
            // ========================================
            'content_sources' => [
                'shop_categories' => true,  // Forklift, transpalet, istifleyici kategorilerinden konu alabilir
                'shop_products' => true,    // Spesifik ürün modellerinden örnek verebilir
                'blog_categories' => true,  // Mevcut blog kategorilerinden ilham alabilir
            ],

            // ========================================
            // HİZMETLER (Blog'da bahsedilebilir)
            // ========================================
            'services' => [
                'rental' => 'Kiralama hizmeti (kısa/uzun dönem)',
                'second_hand' => 'İkinci el ekipman satışı',
                'technical_service' => 'Teknik servis ve periyodik bakım',
                'warranty' => 'Garanti kapsamı (süre belirtilmeden)',
                'original_parts' => 'Orijinal yedek parça tedariki',
            ],

            // ========================================
            // İÇERİK ODAĞI
            // ========================================
            'content_focus' => [
                'Ürün teknik özellikleri ve spesifikasyonlar',
                'Karşılaştırma rehberleri (manuel vs elektrikli, marka karşılaştırması)',
                'Seçim kriterleri ve satın alma rehberleri',
                'Kullanım alanları ve uygulama örnekleri',
                'Güvenlik standartları ve sertifikalar (CE, ISO)',
                'Bakım ve servis gereksinimleri',
                'Maliyet-fayda analizi (kiralama vs satın alma)',
                'İkinci el alım ipuçları ve dikkat edilmesi gerekenler',
                'Orijinal yedek parça önemi ve faydaları',
                'Verimlilik artırma stratejileri',
            ],

            // ========================================
            // HEDEF KİTLE
            // ========================================
            'target_audience' => $targetInfo['customer_profile'] ?? 'B2B profesyoneller (satın alma müdürleri, depo yöneticileri, lojistik sorumlular, teknik ekipler)',
            'target_industries' => $targetInfo['industries'] ?? 'Depo yönetimi, lojistik, üretim, inşaat, perakende',

            // ========================================
            // TON & STİL
            // ========================================
            'tone' => 'Profesyonel, teknik, güvenilir, objektif',
            'writing_style' => [
                'Profesyonel ve teknik dil kullan',
                'Kanıta dayalı iddıalar sun (teknik dökümanlar, standartlar)',
                'Objektif karşılaştırmalar yap (marka adı kullanmadan)',
                'Sade ve net cümleler tercih et (≤20 kelime)',
                'Jargon kullanma, teknik terimleri açıkla',
                'Görüş belirtme, analoji kurma',
                'Güvenilirlik ve uzmanlık hissi ver',
            ],

            // ========================================
            // REFERANS KAYNAKLARI
            // ========================================
            'reference_sources' => [
                'ISO standartları (ISO 3691, ISO 5053)',
                'CE sertifikasyon gereksinimleri',
                'TSE standartları',
                'Üretici teknik spesifikasyon dökümanları',
                'Endüstri güvenlik raporları',
                'İş güvenliği yönetmelikleri',
                'Sektörel ticaret odası yayınları',
                'Lojistik dernek raporları',
            ],

            // ========================================
            // ANAHTAR KELİME ODAĞI
            // ========================================
            'keyword_focus' => [
                'Teknik özellikler (kapasite, yük yüksekliği, çatal uzunluğu)',
                'Güvenlik (CE, ISO, iş güvenliği)',
                'Karşılaştırma (manuel vs elektrikli, dizel vs LPG)',
                'Maliyet (fiyat, kiralama bedeli, işletme maliyeti)',
                'Bakım (periyodik bakım, yedek parça, servis)',
                'Verimlilik (iş akışı, zaman tasarrufu, kapasite)',
                'Seçim kriterleri (nasıl seçilir, nelere dikkat edilmeli)',
                'Uzun kuyruk keywords (en iyi forklift markası, elektrikli transpalet fiyatları)',
            ],

            // ========================================
            // BLOG KONU ÖNERİLERİ (Örnekler)
            // ========================================
            'blog_topic_ideas' => [
                'product_comparison' => 'Ürün karşılaştırmaları (Manuel vs Elektrikli Transpalet, Dizel vs LPG Forklift)',
                'buying_guides' => 'Satın alma rehberleri (Forklift Seçim Kriterleri, Transpalet Nasıl Seçilir)',
                'rental_vs_purchase' => 'Kiralama vs Satın alma (Forklift Kiralamak mı Almak mı Daha Avantajlı)',
                'second_hand_tips' => 'İkinci el alım ipuçları (İkinci El Forklift Alırken Nelere Dikkat Edilmeli)',
                'maintenance_guides' => 'Bakım ve servis rehberleri (Forklift Bakım Periyotları, Transpalet Bakımı)',
                'original_parts' => 'Orijinal parça önemi (Orijinal Yedek Parça Neden Önemlidir)',
                'warranty_info' => 'Garanti bilgilendirmesi (Forklift Garanti Kapsamı ve Koşulları)',
                'safety_standards' => 'Güvenlik standartları (CE Belgeli Forklift Nedir, ISO 3691 Standartları)',
                'application_examples' => 'Kullanım alanları (Hangi Sektörlerde Forklift Kullanılır)',
                'cost_analysis' => 'Maliyet analizi (Forklift İşletme Maliyetleri, Kiralama vs Satın Alma Maliyeti)',
                'efficiency_tips' => 'Verimlilik ipuçları (Depo Verimliliğini Artırma Yolları)',
                'technical_specs' => 'Teknik özellikler (Forklift Kapasite Hesaplama, Yük Yüksekliği Seçimi)',
            ],

            // ========================================
            // SEO HEDEF & STRATEJİ
            // ========================================
            'seo_goals' => [
                'target_position' => 'İlk sayfa (Top 5) - 90 gün hedef',
                'keyword_type' => 'Uzun kuyruk teknik keywords',
                'search_intent' => 'Bilgi arama (informational) + Ticari araştırma (commercial investigation)',
                'content_depth' => '2000+ kelime, kapsamlı rehberler',
                'internal_linking' => 'Shop kategorileri ve ürünlere bağlantı',
            ],

            // ========================================
            // ÖNEMLİ NOTLAR
            // ========================================
            'important_notes' => [
                'Marka adı kullanma (context gerektirmedikçe)',
                'Fiyat belirtme (genel aralık verebilirsin)',
                'Süre verme (garanti, kiralama vb. için)',
                'Rakip firma isimleri kullanma',
                'Subjektif görüş belirtme',
                'Kanıtsız iddia sunma',
            ],
        ];
    }
}
