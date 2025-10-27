# 🎯 DİJİTAL PAZARLAMA PLATFORMLARI - TİNKER KOMUTLARI

## 📋 EKLENECEK PLATFORM ID'LERİ

Admin paneline şu adrese gir: https://ixtif.com/admin/settingmanagement/values/8

---

## 🔧 TİNKER KOMUTLARI

### ✅ 1. GOOGLE TAG MANAGER (GTM)

```php
php artisan tinker
```

```php
// GTM Container ID Ekle
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_google_tag_manager_id')->value('id'), 'tenant_id' => null],
    ['value' => 'GTM-XXXXXXX', 'updated_at' => now()]
);

// Kontrol et
setting('seo_google_tag_manager_id');
```

---

### ✅ 2. GOOGLE ADS CONVERSION TRACKING

```php
// Google Ads Conversion ID
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_google_ads_conversion_id')->value('id'), 'tenant_id' => null],
    ['value' => 'AW-XXXXXXXXXX', 'updated_at' => now()]
);

// Form Gönderme Conversion Label
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_google_ads_form_conversion_label')->value('id'), 'tenant_id' => null],
    ['value' => 'AbC-123xyz', 'updated_at' => now()]
);

// Telefon Tıklama Conversion Label
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_google_ads_phone_conversion_label')->value('id'), 'tenant_id' => null],
    ['value' => 'XyZ-456abc', 'updated_at' => now()]
);

// Kontrol et
setting('seo_google_ads_conversion_id');
setting('seo_google_ads_form_conversion_label');
setting('seo_google_ads_phone_conversion_label');
```

---

### ✅ 3. FACEBOOK PIXEL (META)

```php
// Facebook Pixel ID
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_facebook_pixel_id')->value('id'), 'tenant_id' => null],
    ['value' => '123456789012345', 'updated_at' => now()]
);

// Kontrol et
setting('seo_facebook_pixel_id');
```

---

### ✅ 4. LINKEDIN INSIGHT TAG

```php
// LinkedIn Partner ID
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_linkedin_partner_id')->value('id'), 'tenant_id' => null],
    ['value' => '123456', 'updated_at' => now()]
);

// Kontrol et
setting('seo_linkedin_partner_id');
```

---

### ✅ 5. MICROSOFT CLARITY

```php
// Microsoft Clarity Project ID
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_microsoft_clarity_id')->value('id'), 'tenant_id' => null],
    ['value' => 'abcd1234', 'updated_at' => now()]
);

// Kontrol et
setting('seo_microsoft_clarity_id');
```

---

### ✅ 6. TWITTER (X) PIXEL (OPSİYONEL)

```php
// Twitter Pixel ID
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_twitter_pixel_id')->value('id'), 'tenant_id' => null],
    ['value' => 'o1234', 'updated_at' => now()]
);

// Kontrol et
setting('seo_twitter_pixel_id');
```

---

### ✅ 7. TIKTOK PIXEL (OPSİYONEL)

```php
// TikTok Pixel ID
DB::table('setting_values')->updateOrInsert(
    ['setting_id' => DB::table('settings')->where('key', 'seo_tiktok_pixel_id')->value('id'), 'tenant_id' => null],
    ['value' => 'C1234567890ABCDEF', 'updated_at' => now()]
);

// Kontrol et
setting('seo_tiktok_pixel_id');
```

---

## 🔍 TÜM AYARLARI KONTROL ET

```php
// Tüm pazarlama platform ayarlarını listele
DB::table('settings')
    ->where('group_id', 8)
    ->whereIn('key', [
        'seo_google_tag_manager_id',
        'seo_google_analytics_code',
        'seo_google_ads_conversion_id',
        'seo_google_ads_form_conversion_label',
        'seo_google_ads_phone_conversion_label',
        'seo_facebook_pixel_id',
        'seo_linkedin_partner_id',
        'seo_microsoft_clarity_id',
        'seo_yandex_metrica',
        'seo_twitter_pixel_id',
        'seo_tiktok_pixel_id',
    ])
    ->get(['id', 'key', 'label'])
    ->each(function($s) {
        echo "{$s->label}: " . setting($s->key) . "\n";
    });
```

---

## 🗑️ TEMİZLEME (GEREKİRSE)

```php
// Sadece boş değerleri temizle
DB::table('setting_values')
    ->whereIn('setting_id', DB::table('settings')->whereIn('key', [
        'seo_google_tag_manager_id',
        'seo_google_ads_conversion_id',
        'seo_facebook_pixel_id',
        'seo_linkedin_partner_id',
        'seo_microsoft_clarity_id',
    ])->pluck('id'))
    ->where('value', '')
    ->delete();
```

---

## 📊 PLATFORM ID'LERİ NEREDEN ALINIR?

### Google Tag Manager
1. https://tagmanager.google.com
2. Hesap oluştur → Container oluştur
3. GTM-XXXXXXX formatında ID kopyala

### Google Ads
1. https://ads.google.com
2. Araçlar → Dönüşümler → Yeni dönüşüm
3. AW-XXXXXXXXXX (Conversion ID) ve label'ı kopyala

### Facebook Pixel
1. https://business.facebook.com/events_manager
2. Veri Kaynakları → Pixel → Oluştur
3. 15 haneli Pixel ID kopyala

### LinkedIn Insight Tag
1. https://www.linkedin.com/campaignmanager
2. Hesap Varlıkları → Insight Tag
3. Partner ID kopyala

### Microsoft Clarity
1. https://clarity.microsoft.com
2. Yeni Proje Oluştur
3. Project ID kopyala

---

## ✅ HIZLI KURULUM (HEPSİNİ BİRDEN)

```php
// Placeholder ID'lerle hepsini ekle (sonra admin panelden değiştir)
$platforms = [
    'seo_google_tag_manager_id' => 'GTM-XXXXXXX',
    'seo_google_ads_conversion_id' => 'AW-XXXXXXXXXX',
    'seo_google_ads_form_conversion_label' => 'form_label_here',
    'seo_google_ads_phone_conversion_label' => 'phone_label_here',
    'seo_facebook_pixel_id' => '123456789012345',
    'seo_linkedin_partner_id' => '123456',
    'seo_microsoft_clarity_id' => 'abcd1234',
];

foreach ($platforms as $key => $value) {
    $settingId = DB::table('settings')->where('key', $key)->value('id');
    if ($settingId) {
        DB::table('setting_values')->updateOrInsert(
            ['setting_id' => $settingId, 'tenant_id' => null],
            ['value' => $value, 'updated_at' => now()]
        );
        echo "✅ {$key} => {$value}\n";
    }
}

echo "\n🎯 Admin panelden gerçek ID'leri gir:\n";
echo "   https://ixtif.com/admin/settingmanagement/values/8\n";
```

---

## 🔥 NOT

**ÖNEMLİ:** Yukarıdaki komutlar **placeholder** değerlerle çalışır.
**Gerçek ID'leri** admin panelden gir: https://ixtif.com/admin/settingmanagement/values/8
