<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute kabul edilmelidir.',
    'accepted_if' => ':other :value olduğunda :attribute kabul edilmelidir.',
    'active_url' => ':attribute geçerli bir URL olmalıdır.',
    'after' => ':attribute :date tarihinden sonra olmalıdır.',
    'after_or_equal' => ':attribute :date tarihinden sonra veya aynı tarihte olmalıdır.',
    'alpha' => ':attribute sadece harfler içerebilir.',
    'alpha_dash' => ':attribute sadece harfler, sayılar, tire ve alt çizgi içerebilir.',
    'alpha_num' => ':attribute sadece harfler ve sayılar içerebilir.',
    'array' => ':attribute bir dizi olmalıdır.',
    'ascii' => ':attribute sadece tek baytlık alfanümerik karakterler ve semboller içerebilir.',
    'before' => ':attribute :date tarihinden önce olmalıdır.',
    'before_or_equal' => ':attribute :date tarihinden önce veya aynı tarihte olmalıdır.',
    'between' => [
        'array' => ':attribute :min ve :max öğe arasında olmalıdır.',
        'file' => ':attribute :min ve :max kilobyte arasında olmalıdır.',
        'numeric' => ':attribute :min ve :max arasında olmalıdır.',
        'string' => ':attribute :min ve :max karakter arasında olmalıdır.',
    ],
    'boolean' => ':attribute doğru veya yanlış olmalıdır.',
    'can' => ':attribute yetkisiz bir değer içeriyor.',
    'confirmed' => ':attribute onayı eşleşmiyor.',
    'contains' => ':attribute gerekli bir değer eksik.',
    'current_password' => 'Şifre yanlış.',
    'date' => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals' => ':attribute :date tarihine eşit olmalıdır.',
    'date_format' => ':attribute :format formatına uymalıdır.',
    'decimal' => ':attribute :decimal ondalık basamak içermelidir.',
    'declined' => ':attribute reddedilmelidir.',
    'declined_if' => ':other :value olduğunda :attribute reddedilmelidir.',
    'different' => ':attribute ve :other farklı olmalıdır.',
    'digits' => ':attribute :digits basamak olmalıdır.',
    'digits_between' => ':attribute :min ve :max basamak arasında olmalıdır.',
    'dimensions' => ':attribute geçersiz resim boyutlarına sahip.',
    'distinct' => ':attribute tekrarlanan bir değere sahip.',
    'doesnt_end_with' => ':attribute şu değerlerden biriyle bitemez: :values.',
    'doesnt_start_with' => ':attribute şu değerlerden biriyle başlayamaz: :values.',
    'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'ends_with' => ':attribute şu değerlerden biriyle bitmelidir: :values.',
    'enum' => 'Seçilen :attribute geçersiz.',
    'exists' => 'Seçilen :attribute geçersiz.',
    'extensions' => ':attribute aşağıdaki uzantılardan birine sahip olmalıdır: :values.',
    'file' => ':attribute bir dosya olmalıdır.',
    'filled' => ':attribute bir değer içermelidir.',
    'gt' => [
        'array' => ':attribute :value öğeden fazla olmalıdır.',
        'file' => ':attribute :value kilobyte\'tan büyük olmalıdır.',
        'numeric' => ':attribute :value\'dan büyük olmalıdır.',
        'string' => ':attribute :value karakterden uzun olmalıdır.',
    ],
    'gte' => [
        'array' => ':attribute :value öğe veya daha fazla olmalıdır.',
        'file' => ':attribute :value kilobyte veya daha büyük olmalıdır.',
        'numeric' => ':attribute :value veya daha büyük olmalıdır.',
        'string' => ':attribute :value karakter veya daha uzun olmalıdır.',
    ],
    'hex_color' => ':attribute geçerli bir onaltılık renk olmalıdır.',
    'image' => ':attribute bir resim olmalıdır.',
    'in' => 'Seçilen :attribute geçersiz.',
    'in_array' => ':attribute :other içinde bulunmalıdır.',
    'integer' => ':attribute tam sayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON dizisi olmalıdır.',
    'list' => ':attribute bir liste olmalıdır.',
    'lowercase' => ':attribute küçük harf olmalıdır.',
    'lt' => [
        'array' => ':attribute :value öğeden az olmalıdır.',
        'file' => ':attribute :value kilobyte\'tan küçük olmalıdır.',
        'numeric' => ':attribute :value\'dan küçük olmalıdır.',
        'string' => ':attribute :value karakterden kısa olmalıdır.',
    ],
    'lte' => [
        'array' => ':attribute :value öğe veya daha az olmalıdır.',
        'file' => ':attribute :value kilobyte veya daha küçük olmalıdır.',
        'numeric' => ':attribute :value veya daha küçük olmalıdır.',
        'string' => ':attribute :value karakter veya daha kısa olmalıdır.',
    ],
    'mac_address' => ':attribute geçerli bir MAC adresi olmalıdır.',
    'max' => [
        'array' => ':attribute :max öğeden fazla olamaz.',
        'file' => ':attribute :max kilobyte\'tan büyük olamaz.',
        'numeric' => ':attribute :max\'dan büyük olamaz.',
        'string' => ':attribute :max karakterden uzun olamaz.',
    ],
    'max_digits' => ':attribute :max basamaktan fazla olamaz.',
    'mimes' => ':attribute :values türünde bir dosya olmalıdır.',
    'mimetypes' => ':attribute :values türünde bir dosya olmalıdır.',
    'min' => [
        'array' => ':attribute en az :min öğe olmalıdır.',
        'file' => ':attribute en az :min kilobyte olmalıdır.',
        'numeric' => ':attribute en az :min olmalıdır.',
        'string' => ':attribute en az :min karakter olmalıdır.',
    ],
    'min_digits' => ':attribute en az :min basamak olmalıdır.',
    'missing' => ':attribute eksik olmalıdır.',
    'missing_if' => ':other :value olduğunda :attribute eksik olmalıdır.',
    'missing_unless' => ':other :value olmadığında :attribute eksik olmalıdır.',
    'missing_with' => ':values mevcut olduğunda :attribute eksik olmalıdır.',
    'missing_with_all' => ':values mevcut olduğunda :attribute eksik olmalıdır.',
    'multiple_of' => ':attribute :value\'nun katı olmalıdır.',
    'not_in' => 'Seçilen :attribute geçersiz.',
    'not_regex' => ':attribute formatı geçersiz.',
    'numeric' => ':attribute sayı olmalıdır.',
    'password' => 'Şifre yanlış.',
    'present' => ':attribute mevcut olmalıdır.',
    'present_if' => ':other :value olduğunda :attribute mevcut olmalıdır.',
    'present_unless' => ':other :value olmadığında :attribute mevcut olmalıdır.',
    'present_with' => ':values mevcut olduğunda :attribute mevcut olmalıdır.',
    'present_with_all' => ':values mevcut olduğunda :attribute mevcut olmalıdır.',
    'prohibited' => ':attribute yasak.',
    'prohibited_if' => ':other :value olduğunda :attribute yasak.',
    'prohibited_unless' => ':other :values içinde olmadığında :attribute yasak.',
    'prohibits' => ':attribute :other\'in mevcut olmasını yasaklar.',
    'regex' => ':attribute formatı geçersiz.',
    'required' => ':attribute gerekli.',
    'required_array_keys' => ':attribute şu anahtarları içermelidir: :values.',
    'required_if' => ':other :value olduğunda :attribute gerekli.',
    'required_if_accepted' => ':other kabul edildiğinde :attribute gerekli.',
    'required_if_declined' => ':other reddedildiğinde :attribute gerekli.',
    'required_unless' => ':other :values içinde olmadığında :attribute gerekli.',
    'required_with' => ':values mevcut olduğunda :attribute gerekli.',
    'required_with_all' => ':values mevcut olduğunda :attribute gerekli.',
    'required_without' => ':values mevcut olmadığında :attribute gerekli.',
    'required_without_all' => ':values\'dan hiçbiri mevcut olmadığında :attribute gerekli.',
    'same' => ':attribute ve :other eşleşmelidir.',
    'size' => [
        'array' => ':attribute :size öğe içermelidir.',
        'file' => ':attribute :size kilobyte olmalıdır.',
        'numeric' => ':attribute :size olmalıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
    ],
    'starts_with' => ':attribute şu değerlerden biriyle başlamalıdır: :values.',
    'string' => ':attribute metin olmalıdır.',
    'timezone' => ':attribute geçerli bir zaman dilimi olmalıdır.',
    'unique' => ':attribute zaten kullanılmış.',
    'uploaded' => ':attribute yükleme başarısız.',
    'uppercase' => ':attribute büyük harf olmalıdır.',
    'url' => ':attribute geçerli bir URL olmalıdır.',
    'ulid' => ':attribute geçerli bir ULID olmalıdır.',
    'uuid' => ':attribute geçerli bir UUID olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'E-posta Adresi',
        'password' => 'Şifre',
        'name' => 'ad',
        'first_name' => 'ad',
        'last_name' => 'soyad',
        'phone' => 'telefon',
        'address' => 'adres',
        'city' => 'şehir',
        'country' => 'ülke',
        'postal_code' => 'posta kodu',
        'message' => 'mesaj',
        'subject' => 'konu',
        'title' => 'başlık',
        'content' => 'içerik',
        'description' => 'açıklama',
        'terms' => 'şartlar',
        'privacy' => 'gizlilik',
        'remember' => 'Beni hatırla',
        'current_password' => 'mevcut şifre',
        'new_password' => 'yeni şifre',
        'password_confirmation' => 'şifre onayı',
        'slug' => 'URL adı',
        'seo_title' => 'SEO başlık',
        'seo_description' => 'SEO açıklama',
        'seo_keywords' => 'SEO anahtar kelimeler',
        'canonical_url' => 'Canonical URL',
        'is_active' => 'Aktif durum',
        'body' => 'İçerik',
        'multiLangInputs.*.title' => 'Başlık',
        'multiLangInputs.*.slug' => 'URL adı',
        'multiLangInputs.*.body' => 'İçerik',
        'multiLangInputs.tr.title' => 'Başlık (Türkçe)',
        'multiLangInputs.en.title' => 'Başlık (İngilizce)',
        'multiLangInputs.ar.title' => 'Başlık (Arapça)',
        'multiLangInputs.tr.slug' => 'URL adı (Türkçe)',
        'multiLangInputs.en.slug' => 'URL adı (İngilizce)',
        'multiLangInputs.ar.slug' => 'URL adı (Arapça)',
        'inputs.is_active' => 'Aktif durum',
        'seoDataCache.*.seo_title' => 'SEO başlık',
        'seoDataCache.*.seo_description' => 'SEO açıklama',
        'seoDataCache.*.seo_keywords' => 'SEO anahtar kelimeler',
        'seoDataCache.*.canonical_url' => 'Canonical URL',
    ],

];