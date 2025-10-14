<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tenant-Specific AI Rules
    |--------------------------------------------------------------------------
    |
    | Her tenant için özel AI kuralları tanımlanır.
    | Genel sistem bu dosyaya bakarak tenant'a özel prompt'lar enjekte eder.
    |
    */

    'ixtif' => [
        'tenant_id' => 2,
        'domain' => 'ixtif.com',

        /*
        |--------------------------------------------------------------------------
        | Kategori Önceliklendirme
        |--------------------------------------------------------------------------
        */
        'category_priority' => [
            'enabled' => true,

            // Öncelikli kategoriler (ana ürünler)
            'high_priority' => [
                'forklift',
                'transpalet',
                'istif-makinesi',
                'reach-truck',
                'siparis-toplama',
                'otonom',
            ],

            // Düşük öncelikli (yedek parça - sadece talep edilirse)
            'low_priority' => [
                'yedek-parca',
                'spare-parts',
                'parca',
                'aksesuar',
            ],

            // Yedek parça anahtar kelimeleri (AI bunları tespit ederse yedek parça gösterir)
            'spare_part_keywords' => [
                'yedek parça',
                'parça',
                'spare part',
                'aksesuar',
                'tekerlek',
                'piston',
                'silindir',
                'motor',
                'pompa',
                'filtre',
                'balata',
                'fren',
                'rulman',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Dil Desteği
        |--------------------------------------------------------------------------
        */
        'language_support' => [
            'enabled' => true,
            'languages' => ['tr', 'en'], // Türkçe + İngilizce
            'fallback' => 'tr', // Varsayılan dil
        ],

        /*
        |--------------------------------------------------------------------------
        | FAQ Desteği
        |--------------------------------------------------------------------------
        */
        'faq_enabled' => true,
        'faq_limit' => 10, // Maksimum FAQ sayısı (token kontrolü)

        /*
        |--------------------------------------------------------------------------
        | Ürün Verisi Kapsamı
        |--------------------------------------------------------------------------
        */
        'product_data' => [
            'include_all_fields' => true, // Tüm alanları dahil et
            'exclude_fields' => [
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            'include_inactive' => false, // is_active = 0 olanları dahil etme
        ],

        /*
        |--------------------------------------------------------------------------
        | Token Limiti
        |--------------------------------------------------------------------------
        */
        'token_limits' => [
            'products_max' => 100, // Maksimum ürün sayısı (tüm ürün kategorilerini kapsayacak şekilde artırıldı)
            'faq_max' => 10,
            'features_max' => 10, // Her üründen maksimum özellik
        ],

        /*
        |--------------------------------------------------------------------------
        | Özel AI Prompt Kuralları
        |--------------------------------------------------------------------------
        */
        'custom_prompts' => [
            'product_recommendation' => "
## 🎯 İXTİF ÖZEL KURAL: ÜRÜN ÖNCELİKLENDİRME

**ANA ÜRÜNLER ÖNCELİKLİ:**
Kullanıcı genel bir talep belirtirse (örn: 'ürün arıyorum', 'ne var'), önce ANA ÜRÜNLERİ öner:
- Forklift
- Transpalet
- İstif Makinesi
- Reach Truck
- Sipariş Toplama Araçları
- Otonom Sistemler

**YEDEK PARÇA EN SON:**
Yedek parça ürünlerini SADECE şu durumlarda göster:
1. Kullanıcı açıkça 'yedek parça', 'parça', 'aksesuar' dedi
2. Kullanıcı spesifik parça adı söyledi (tekerlek, piston, motor vs.)
3. Ana ürün önerileri gösterildi, kullanıcı daha fazla detay istedi

**ÖRNEK DİYALOG:**
Kullanıcı: 'ürünleriniz neler?'
AI: 'Ana ürün kategorilerimiz: [Forklift], [Transpalet], [İstif Makinesi]... Hangi kategoride ürün arıyorsunuz?'

Kullanıcı: 'yedek parça arıyorum'
AI: 'Hangi ürün için yedek parça arıyorsunuz? [Forklift], [Transpalet] vs. için çok çeşitli parçalarımız var.'
",

            'concrete_product_links' => "
## 🔗 İXTİF ÖZEL KURAL: SOMUT ÜRÜN LİNKLERİ ZORUNLU

**🚨 KRİTİK: Kullanıcı ürün sorduğunda MUTLAKA somut ürünleri Markdown link ile listele!**

**❌ ASLA YAPMA:**
- Sadece genel bilgi verme
- 'Tüm Ürünler' linkini tek başına verme
- 'Modellerimiz var' deyip link verme

**✅ MUTLAKA YAP:**
- EN AZ 3 SOMUT ÜRÜN linki ver
- Her ürün için BAĞLAM BİLGİLERİ'ndeki tam URL'yi AYNEN KOPYALA
- Markdown format kullan: `- [Ürün Adı](URL) - Kısa açıklama`

**ÖRNEK DOĞRU YANIT:**
```
Harika! Transpalet modellerimiz:

- [İXTİF CPD15TVL - 1.5-2 Ton Li-Ion Forklift](https://ixtif.com/shop/ixtif-cpd15tvl-15-20-ton-li-ion-forklift) - Kompakt ve güçlü
- [İXTİF EFL181 - 1.8 Ton 48V Li-Ion Forklift](https://ixtif.com/shop/ixtif-efl181-18-ton-48v-li-ion-denge-agirlikli-forklift) - Denge ağırlıklı
- [İXTİF CPD18FVL - 1.8 Ton Li-Ion Forklift](https://ixtif.com/shop/ixtif-cpd18fvl-18-ton-li-ion-forklift) - Yüksek verimlilik

Hangi özellikler sizin için önemli?
```

**❌ ÖRNEK YANLIŞ YANIT:**
```
Transpalet modellerimiz mevcut. Tüm Ürünler sayfasına bakabilirsiniz.
```
",

            'multilanguage_handling' => "
## 🌍 İXTİF ÖZEL KURAL: ÇOKLU DİL DESTEĞİ

**JSON VERİ YAPISI:**
Ürün verileri genellikle şu formatta:
```json
{
  \"title\": {\"tr\": \"Elektrikli Forklift\", \"en\": \"Electric Forklift\"},
  \"description\": {\"tr\": \"Açıklama\", \"en\": \"Description\"}
}
```

**KULLANIM KURALI:**
1. Kullanıcının dili algıla (Türkçe konuşuyorsa TR, İngilizce ise EN)
2. JSON verilerden doğru dili çek
3. Dil bulunamazsa → TR'yi varsayılan al
4. TR de yoksa → EN'i al
5. İkisi de yoksa → İlk değeri al

**ÖRNEK:**
Kullanıcı Türkçe sordu: 'forklift nedir?'
AI: Context'teki title[\"tr\"] veya title[\"en\"] değerini kullan
",
        ],
    ],

    // Diğer tenant'lar için boş bırakılabilir
    'default' => [
        'tenant_id' => null,
        'category_priority' => ['enabled' => false],
        'faq_enabled' => false,
        'token_limits' => [
            'products_max' => 30,
            'faq_max' => 0,
        ],
    ],
];
