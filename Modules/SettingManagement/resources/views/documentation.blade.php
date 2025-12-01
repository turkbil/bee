@php
    View::share('pretitle', 'AI Dokümantasyonu');
@endphp
@include('settingmanagement::helper')
<div>
    {{-- KRİTİK AI KURALLARI --}}
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>0. KRİTİK AI KURALLARI - ÖNCE BUNU OKU!</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <strong>AI Asistan!</strong> Bu kuralları ihlal etmen, sistemde ciddi sorunlara yol açar.
                Geçmişte yaşanan hatalardan ders çıkarıldı ve bu kurallar oluşturuldu.
            </div>

            <h4 class="text-danger mt-4"><i class="fas fa-ban me-2"></i>ASLA YAPMA - YASAKLAR</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-danger-lt">
                        <div class="card-body">
                            <h5>1. HARDCODE LAYOUT JSON YAZMA!</h5>
                            <pre class="bg-dark text-light p-2 rounded small"><code>// ❌ YANLIŞ - Seeder'a layout JSON yazma!
$group->layout = [
    'elements' => [...]  // ASLA!
];

// ❌ YANLIŞ - Migration'da layout
$table->json('layout')->default('...');</code></pre>
                            <p class="text-danger mb-0"><strong>Sebep:</strong> Layout FormBuilder ile yönetilir. Hardcode yazarsan format uyumsuzluğu olur.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-danger-lt">
                        <div class="card-body">
                            <h5>2. SELECT OPTIONS FORMATINI KARIŞTIRMA!</h5>
                            <pre class="bg-dark text-light p-2 rounded small"><code>// ❌ YANLIŞ - String array
"options": ["Kapalı", "Açık"]

// ❌ YANLIŞ - Sadece label
"options": [{"label": "Kapalı"}]

// ✅ DOĞRU - DB formatı (settings tablosu)
"options": {"0": "Kapalı", "1": "Açık"}

// ✅ DOĞRU - JS formatı (layout JSON)
"options": [
  {"value": "0", "label": "Kapalı", "is_default": true}
]</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="text-success mt-4"><i class="fas fa-check-circle me-2"></i>DOĞRU YAKLAŞIM - YENİ AYAR EKLEME</h4>
            <div class="card bg-success-lt">
                <div class="card-body">
                    <h5>Adım 1: Sadece Settings Tablosuna Ekle</h5>
                    <pre class="bg-dark text-light p-3 rounded"><code>use Modules\SettingManagement\App\Models\Setting;

// Text input ekleme
Setting::create([
    'group_id' => 6,  // Hangi gruba eklenecek
    'key' => 'site_phone',  // Benzersiz key
    'label' => 'Site Telefon',
    'type' => 'text',  // text, select, switch, image, vb.
    'default_value' => null,
    'is_active' => true,
    'is_system' => false,
    'sort_order' => 100,
]);

// Select input ekleme - OPTIONS FORMATI KRİTİK!
Setting::create([
    'group_id' => 13,
    'key' => 'paytr_currency',
    'label' => 'Para Birimi',
    'type' => 'select',
    'options' => [  // ✅ KEY => LABEL formatı
        'TRY' => 'Türk Lirası',
        'USD' => 'Amerikan Doları',
        'EUR' => 'Euro',
    ],
    'default_value' => 'TRY',
    'is_active' => true,
    'sort_order' => 50,
]);</code></pre>

                    <h5 class="mt-4">Adım 2: Layout'a EKLEME (Opsiyonel - FormBuilder Tercih Edilir)</h5>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>
                        Layout'a manuel ekleme yerine <strong>FormBuilder UI</strong> kullanılması önerilir.
                        Eğer kod ile eklemen gerekiyorsa:
                    </p>
                    <pre class="bg-dark text-light p-3 rounded"><code>$group = SettingGroup::find(6);
$layout = $group->layout ?? ['elements' => []];

// Yeni element ekle
$layout['elements'][] = [
    'type' => 'text',
    'properties' => [
        'name' => 'site_phone',  // Setting key ile aynı olmalı!
        'label' => 'Site Telefon',
        'placeholder' => '+90 xxx xxx xx xx',
        'help_text' => 'İletişim telefonu',
        'width' => 6,
        'required' => false
    ]
];

// SELECT için options ekleme - JS FORMATI!
$layout['elements'][] = [
    'type' => 'select',
    'properties' => [
        'name' => 'paytr_currency',
        'label' => 'Para Birimi',
        'width' => 6,
        'options' => [  // ✅ JS formatı - Array of objects
            ['value' => 'TRY', 'label' => 'Türk Lirası', 'is_default' => true],
            ['value' => 'USD', 'label' => 'Amerikan Doları', 'is_default' => false],
            ['value' => 'EUR', 'label' => 'Euro', 'is_default' => false],
        ]
    ]
];

$group->layout = $layout;
$group->save();</code></pre>
                </div>
            </div>

            <h4 class="text-info mt-4"><i class="fas fa-sync me-2"></i>OTOMATİK DÖNÜŞÜM MEKANİZMASI</h4>
            <div class="alert alert-info">
                <p><strong>SettingGroup modeli otomatik format dönüşümü yapar:</strong></p>
                <ul class="mb-0">
                    <li><code>getLayoutAttribute()</code> - Layout okunurken DB formatını JS formatına çevirir</li>
                    <li><code>syncSettingsFromLayout()</code> - FormBuilder kaydedilirken JS formatını DB formatına çevirir</li>
                    <li>Select options eksikse, <code>settings</code> tablosundan otomatik çeker</li>
                </ul>
            </div>

            <h4 class="text-warning mt-4"><i class="fas fa-history me-2"></i>GEÇMİŞTE YAŞANAN SORUN</h4>
            <div class="card bg-warning-lt">
                <div class="card-body">
                    <p><strong>Sorun:</strong> Select elementlerinin seçenekleri FormBuilder'da görünmüyordu.</p>
                    <p><strong>Sebep:</strong> Layout JSON'da options <code>["Kapalı","Açık"]</code> formatında saklanmıştı.
                    JavaScript ise <code>[{'{'}value, label{'}'}]</code> formatı bekliyordu.</p>
                    <p><strong>Çözüm:</strong> SettingGroup modeline otomatik format dönüşümü eklendi.
                    Artık hangi formatta yazarsan yaz, sistem otomatik düzeltir.</p>
                    <p class="mb-0"><strong>Ders:</strong> Yine de doğru formatı kullanmak best practice'tir.
                    Settings tablosuna <code>{'{'}key: label{'}'}</code>, Layout'a <code>[{'{'}value, label{'}'}]</code> yaz.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- GENEL BAKIŞ --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle me-2"></i>1. GENEL BAKIŞ</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>SettingManagement</strong>, Laravel multi-tenant sisteminde dinamik ayar yönetimi sağlayan bir modüldür.
                Ayarlar merkezi veritabanında tanımlanır, değerler tenant bazında saklanır.
            </div>

            <h4 class="mt-4">Mimari Yapı</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>┌─────────────────────────────────────────────────────────────────┐
│                    CENTRAL DATABASE                              │
│  (tuufi_4ekim - Tüm tenant'lar için ortak)                      │
├─────────────────────────────────────────────────────────────────┤
│  settings_groups    → Ayar grupları (Site, SEO, Payment, vb.)   │
│  settings           → Ayar tanımları (key, type, options, vb.)  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    TENANT DATABASE                               │
│  (tenant_ixtif, tenant_muzibu, vb. - Her tenant'a özel)         │
├─────────────────────────────────────────────────────────────────┤
│  settings_values    → Ayar değerleri (tenant'a özel)            │
└─────────────────────────────────────────────────────────────────┘</code></pre>

            <h4 class="mt-4">Temel Kavramlar</h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="20%">Kavram</th>
                        <th width="30%">Açıklama</th>
                        <th>Örnek</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>SettingGroup</strong></td>
                        <td>Ayarların mantıksal gruplaması</td>
                        <td>"Site Ayarları", "SEO Ayarları", "PayTR"</td>
                    </tr>
                    <tr>
                        <td><strong>Setting</strong></td>
                        <td>Tek bir ayar tanımı (şema)</td>
                        <td>site_title (text), paytr_enabled (boolean)</td>
                    </tr>
                    <tr>
                        <td><strong>SettingValue</strong></td>
                        <td>Ayarın gerçek değeri (tenant bazlı)</td>
                        <td>site_title = "İxtif Endüstriyel"</td>
                    </tr>
                    <tr>
                        <td><strong>Layout</strong></td>
                        <td>FormBuilder için JSON yapısı</td>
                        <td>Form elemanlarının sırası, tipi, özellikleri</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- VERİTABANI ŞEMASI --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-database me-2"></i>2. VERİTABANI ŞEMASI</h3>
        </div>
        <div class="card-body">

            {{-- settings_groups --}}
            <h4 class="text-primary">2.1 settings_groups (Central DB)</h4>
            <p class="text-muted">Ayar gruplarını tanımlar. Hiyerarşik yapıda olabilir (parent-child).</p>
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Kolon</th>
                        <th>Tip</th>
                        <th>Açıklama</th>
                        <th>Örnek</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>id</code></td><td>bigint</td><td>Primary key</td><td>6</td></tr>
                    <tr><td><code>parent_id</code></td><td>bigint|null</td><td>Üst grup ID (null ise ana grup)</td><td>1 (Genel altında)</td></tr>
                    <tr><td><code>name</code></td><td>string</td><td>Grup adı</td><td>"Site Ayarları"</td></tr>
                    <tr><td><code>slug</code></td><td>string</td><td>URL-friendly isim</td><td>"site-ayarlari"</td></tr>
                    <tr><td><code>description</code></td><td>text|null</td><td>Açıklama</td><td>"Temel site bilgileri"</td></tr>
                    <tr><td><code>icon</code></td><td>string|null</td><td>FontAwesome ikon</td><td>"fas fa-cog"</td></tr>
                    <tr><td><code>prefix</code></td><td>string|null</td><td>Ayar key prefix'i</td><td>"site" → site_title</td></tr>
                    <tr>
                        <td><code>layout</code></td>
                        <td>json|null</td>
                        <td>FormBuilder JSON yapısı</td>
                        <td><code>{"elements":[...]}</code></td>
                    </tr>
                    <tr><td><code>is_active</code></td><td>boolean</td><td>Aktif/Pasif</td><td>true</td></tr>
                    <tr><td><code>meta_data</code></td><td>json|null</td><td>Ek veriler</td><td>null</td></tr>
                </tbody>
            </table>

            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Önemli:</strong> <code>parent_id = null</code> olan gruplar ana kategorilerdir (Genel, Ödeme, Auth).
                Alt gruplar <code>parent_id</code> ile bağlanır ve FormBuilder sadece alt gruplarda çalışır.
            </div>

            {{-- settings --}}
            <h4 class="text-primary mt-5">2.2 settings (Central DB)</h4>
            <p class="text-muted">Her bir ayarın tanımını (şemasını) içerir. Değer değil, yapı bilgisi.</p>
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Kolon</th>
                        <th>Tip</th>
                        <th>Açıklama</th>
                        <th>Örnek</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>id</code></td><td>bigint</td><td>Primary key</td><td>1</td></tr>
                    <tr><td><code>group_id</code></td><td>bigint</td><td>Bağlı olduğu grup</td><td>6 (Site Ayarları)</td></tr>
                    <tr>
                        <td><code>key</code></td>
                        <td>string</td>
                        <td><strong>Benzersiz ayar anahtarı</strong></td>
                        <td>"site_title"</td>
                    </tr>
                    <tr><td><code>label</code></td><td>string</td><td>Kullanıcıya gösterilen etiket</td><td>"Site Başlığı"</td></tr>
                    <tr>
                        <td><code>type</code></td>
                        <td>string</td>
                        <td>Form element tipi</td>
                        <td>text, select, switch, image, vb.</td>
                    </tr>
                    <tr>
                        <td><code>options</code></td>
                        <td>json|null</td>
                        <td>Select/radio seçenekleri</td>
                        <td><code>{"0":"Kapalı","1":"Açık"}</code></td>
                    </tr>
                    <tr><td><code>default_value</code></td><td>text|null</td><td>Varsayılan değer</td><td>"Sitem"</td></tr>
                    <tr><td><code>is_required</code></td><td>boolean</td><td>Zorunlu mu?</td><td>false</td></tr>
                    <tr><td><code>is_active</code></td><td>boolean</td><td>Aktif mi?</td><td>true</td></tr>
                    <tr><td><code>is_system</code></td><td>boolean</td><td>Sistem ayarı mı? (Silinemez)</td><td>false</td></tr>
                    <tr><td><code>sort_order</code></td><td>integer</td><td>Sıralama</td><td>10</td></tr>
                </tbody>
            </table>

            <h5 class="mt-4">Desteklenen Type Değerleri:</h5>
            <div class="row">
                <div class="col-md-4">
                    <ul class="list-unstyled">
                        <li><code>text</code> - Kısa metin</li>
                        <li><code>textarea</code> - Uzun metin</li>
                        <li><code>number</code> - Sayı</li>
                        <li><code>email</code> - E-posta</li>
                        <li><code>password</code> - Şifre</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-unstyled">
                        <li><code>select</code> - Açılır liste</li>
                        <li><code>radio</code> - Radyo buton</li>
                        <li><code>checkbox</code> - Onay kutusu</li>
                        <li><code>switch</code> - Aç/Kapa</li>
                        <li><code>color</code> - Renk seçici</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="list-unstyled">
                        <li><code>image</code> - Görsel yükleme</li>
                        <li><code>file</code> - Dosya yükleme</li>
                        <li><code>favicon</code> - Favicon</li>
                        <li><code>date</code> - Tarih</li>
                        <li><code>time</code> - Saat</li>
                    </ul>
                </div>
            </div>

            {{-- settings_values --}}
            <h4 class="text-primary mt-5">2.3 settings_values (Tenant DB)</h4>
            <p class="text-muted">Ayarların gerçek değerlerini saklar. Her tenant kendi database'inde tutar.</p>
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Kolon</th>
                        <th>Tip</th>
                        <th>Açıklama</th>
                        <th>Örnek</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>id</code></td><td>bigint</td><td>Primary key</td><td>1</td></tr>
                    <tr><td><code>setting_id</code></td><td>bigint</td><td>Central DB'deki setting ID</td><td>1</td></tr>
                    <tr>
                        <td><code>value</code></td>
                        <td>longtext|null</td>
                        <td>Ayarın değeri (JSON veya string)</td>
                        <td>"İxtif Endüstriyel"</td>
                    </tr>
                </tbody>
            </table>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Neden ayrı tablo?</strong> Central DB'de şema (ne tür ayar?), Tenant DB'de değer (ne değer?).
                Bu sayede tüm tenant'lar aynı ayar yapısını kullanır ama farklı değerlere sahip olabilir.
            </div>
        </div>
    </div>

    {{-- LAYOUT JSON YAPISI --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-code me-2"></i>3. LAYOUT JSON YAPISI</h3>
        </div>
        <div class="card-body">
            <p><code>settings_groups.layout</code> kolonu FormBuilder için form yapısını JSON olarak saklar.</p>

            <h4 class="mt-4">Temel Yapı:</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "title": "Grup Başlığı",
    "elements": [
        {
            "type": "element_tipi",
            "properties": {
                "name": "setting_key",
                "label": "Görünen Etiket",
                "help_text": "Yardım metni",
                "width": 12,
                "required": false,
                "options": [...]  // Sadece select/radio için
            }
        },
        {
            "type": "row",
            "columns": [
                {
                    "width": 6,
                    "elements": [...]
                },
                {
                    "width": 6,
                    "elements": [...]
                }
            ]
        }
    ]
}</code></pre>

            <h4 class="mt-4">Select Options Formatı:</h4>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>KRİTİK:</strong> Select options iki farklı formatta saklanır. Dönüşüm otomatik yapılır.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Settings Tablosu (DB formatı):</h5>
                    <pre class="bg-secondary text-light p-3 rounded"><code>{
    "0": "Kapalı",
    "1": "Açık"
}</code></pre>
                    <small class="text-muted">Key-value object</small>
                </div>
                <div class="col-md-6">
                    <h5>Layout JSON (JS formatı):</h5>
                    <pre class="bg-secondary text-light p-3 rounded"><code>[
    {"value": "0", "label": "Kapalı", "is_default": true},
    {"value": "1", "label": "Açık", "is_default": false}
]</code></pre>
                    <small class="text-muted">Array of objects</small>
                </div>
            </div>

            <div class="alert alert-success mt-3">
                <i class="fas fa-magic me-2"></i>
                <strong>Otomatik Dönüşüm:</strong> <code>SettingGroup</code> modeli <code>getLayoutAttribute</code> accessor'ı ile
                layout okunurken otomatik olarak options formatını JS formatına çevirir.
            </div>

            <h4 class="mt-4">Örnek Tam Layout:</h4>
            <pre class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{
    "title": "PayTR Ayarları",
    "elements": [
        {
            "type": "row",
            "columns": [
                {
                    "width": 6,
                    "elements": [
                        {
                            "type": "switch",
                            "properties": {
                                "name": "paytr_enabled",
                                "label": "PayTR Aktif",
                                "help_text": "Ödeme yöntemini aktif/pasif yapın",
                                "width": 12
                            }
                        },
                        {
                            "type": "select",
                            "properties": {
                                "name": "paytr_max_installment",
                                "label": "Maksimum Taksit",
                                "width": 12,
                                "options": [
                                    {"value": "1", "label": "Tek Çekim", "is_default": true},
                                    {"value": "3", "label": "3 Taksit", "is_default": false},
                                    {"value": "6", "label": "6 Taksit", "is_default": false}
                                ]
                            }
                        }
                    ]
                },
                {
                    "width": 6,
                    "elements": [
                        {
                            "type": "text",
                            "properties": {
                                "name": "paytr_merchant_id",
                                "label": "Merchant ID",
                                "width": 12
                            }
                        },
                        {
                            "type": "password",
                            "properties": {
                                "name": "paytr_merchant_key",
                                "label": "Merchant Key",
                                "width": 12
                            }
                        }
                    ]
                }
            ]
        }
    ]
}</code></pre>
        </div>
    </div>

    {{-- ELEMENT TİPLERİ VE ÖZELLİKLERİ --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title"><i class="fas fa-cubes me-2"></i>3.5 TÜM ELEMENT TİPLERİ VE ÖZELLİKLERİ</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>AI Asistan!</strong> Her element tipinin kullanabileceği özellikleri aşağıda bulabilirsin.
                help_text ve placeholder özelliklerini mutlaka kullan - son kullanıcılar için çok önemli!
            </div>

            {{-- ORTAK ÖZELLİKLER --}}
            <h4 class="text-primary mt-4"><i class="fas fa-cogs me-2"></i>Ortak Özellikler (Tüm Elementler)</h4>
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="15%">Özellik</th>
                        <th width="15%">Tip</th>
                        <th width="15%">Zorunlu</th>
                        <th>Açıklama</th>
                        <th width="25%">Örnek</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>name</code></td>
                        <td>string</td>
                        <td><span class="badge bg-danger">Zorunlu</span></td>
                        <td>Setting key ile aynı olmalı! Benzersiz tanımlayıcı.</td>
                        <td><code>"site_title"</code></td>
                    </tr>
                    <tr>
                        <td><code>label</code></td>
                        <td>string</td>
                        <td><span class="badge bg-danger">Zorunlu</span></td>
                        <td>Form elemanının üzerinde görünen başlık</td>
                        <td><code>"Site Başlığı"</code></td>
                    </tr>
                    <tr class="table-success">
                        <td><code>help_text</code></td>
                        <td>string</td>
                        <td><span class="badge bg-warning">Önerilen</span></td>
                        <td><strong>Kullanıcıya yardım metni!</strong> Formun altında küçük yazıyla görünür. Son kullanıcılar için çok önemli!</td>
                        <td><code>"Sitenizin tarayıcı sekmesinde görünecek başlık"</code></td>
                    </tr>
                    <tr class="table-success">
                        <td><code>placeholder</code></td>
                        <td>string</td>
                        <td><span class="badge bg-warning">Önerilen</span></td>
                        <td><strong>Input içinde soluk görünen örnek değer.</strong> Kullanıcıya ne yazması gerektiğini gösterir.</td>
                        <td><code>"Örn: Firmam E-Ticaret"</code></td>
                    </tr>
                    <tr>
                        <td><code>width</code></td>
                        <td>integer</td>
                        <td><span class="badge bg-secondary">Opsiyonel</span></td>
                        <td>Bootstrap grid genişliği (1-12). Varsayılan: 12</td>
                        <td><code>6</code> (yarım genişlik)</td>
                    </tr>
                    <tr>
                        <td><code>required</code></td>
                        <td>boolean</td>
                        <td><span class="badge bg-secondary">Opsiyonel</span></td>
                        <td>Zorunlu alan mı? Varsayılan: false</td>
                        <td><code>true</code></td>
                    </tr>
                </tbody>
            </table>

            {{-- TEXT --}}
            <h4 class="text-success mt-5"><i class="fas fa-font me-2"></i>text - Kısa Metin</h4>
            <p class="text-muted">Tek satırlık metin girişi için kullanılır.</p>
            <div class="row">
                <div class="col-md-6">
                    <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "text",
    "properties": {
        "name": "site_title",
        "label": "Site Başlığı",
        "placeholder": "Örn: Firmam E-Ticaret",
        "help_text": "Tarayıcı sekmesinde görünecek başlık",
        "width": 12,
        "required": true
    }
}</code></pre>
                </div>
                <div class="col-md-6">
                    <strong>Kullanım Alanları:</strong>
                    <ul>
                        <li>Site başlığı, slogan</li>
                        <li>Telefon numarası</li>
                        <li>API key, merchant ID</li>
                        <li>Kısa metin bilgileri</li>
                    </ul>
                </div>
            </div>

            {{-- TEXTAREA --}}
            <h4 class="text-success mt-4"><i class="fas fa-align-left me-2"></i>textarea - Uzun Metin</h4>
            <p class="text-muted">Çok satırlı metin girişi için kullanılır.</p>
            <div class="row">
                <div class="col-md-6">
                    <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "textarea",
    "properties": {
        "name": "site_description",
        "label": "Site Açıklaması",
        "placeholder": "Sitenizi kısaca tanımlayın...",
        "help_text": "Ana sayfada ve SEO için kullanılacak",
        "width": 12,
        "rows": 4
    }
}</code></pre>
                </div>
                <div class="col-md-6">
                    <strong>Ek Özellikler:</strong>
                    <table class="table table-sm">
                        <tr><td><code>rows</code></td><td>Satır sayısı (varsayılan: 3)</td></tr>
                    </table>
                    <strong>Kullanım Alanları:</strong>
                    <ul>
                        <li>Site açıklaması</li>
                        <li>Adres bilgisi</li>
                        <li>Özel CSS/JavaScript kodu</li>
                    </ul>
                </div>
            </div>

            {{-- NUMBER --}}
            <h4 class="text-success mt-4"><i class="fas fa-hashtag me-2"></i>number - Sayı</h4>
            <p class="text-muted">Sadece sayı girişi için kullanılır.</p>
            <div class="row">
                <div class="col-md-6">
                    <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "number",
    "properties": {
        "name": "items_per_page",
        "label": "Sayfa Başına Ürün",
        "placeholder": "12",
        "help_text": "Listelerde kaç ürün gösterilsin? (Önerilen: 12-24)",
        "width": 6,
        "min": 1,
        "max": 100,
        "step": 1
    }
}</code></pre>
                </div>
                <div class="col-md-6">
                    <strong>Ek Özellikler:</strong>
                    <table class="table table-sm">
                        <tr><td><code>min</code></td><td>Minimum değer</td></tr>
                        <tr><td><code>max</code></td><td>Maximum değer</td></tr>
                        <tr><td><code>step</code></td><td>Artış miktarı</td></tr>
                    </table>
                </div>
            </div>

            {{-- EMAIL --}}
            <h4 class="text-success mt-4"><i class="fas fa-envelope me-2"></i>email - E-posta</h4>
            <p class="text-muted">E-posta formatı doğrulaması yapar.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "email",
    "properties": {
        "name": "contact_email",
        "label": "İletişim E-posta",
        "placeholder": "info@firmaniz.com",
        "help_text": "Müşterilerinizin size ulaşacağı e-posta adresi",
        "width": 6
    }
}</code></pre>

            {{-- PASSWORD --}}
            <h4 class="text-success mt-4"><i class="fas fa-key me-2"></i>password - Şifre</h4>
            <p class="text-muted">Gizli metin girişi. Değer maskelenir.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "password",
    "properties": {
        "name": "paytr_merchant_key",
        "label": "Merchant Key",
        "placeholder": "••••••••",
        "help_text": "PayTR panelinizden aldığınız gizli anahtar",
        "width": 6
    }
}</code></pre>

            {{-- SELECT --}}
            <h4 class="text-warning mt-5"><i class="fas fa-list-ul me-2"></i>select - Açılır Liste</h4>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>KRİTİK!</strong> Options formatına dikkat et. Layout'ta JS formatı kullan!
            </div>
            <div class="row">
                <div class="col-md-6">
                    <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "select",
    "properties": {
        "name": "default_currency",
        "label": "Para Birimi",
        "help_text": "Fiyatların gösterileceği para birimi",
        "width": 6,
        "options": [
            {"value": "TRY", "label": "Türk Lirası (₺)", "is_default": true},
            {"value": "USD", "label": "Amerikan Doları ($)", "is_default": false},
            {"value": "EUR", "label": "Euro (€)", "is_default": false}
        ]
    }
}</code></pre>
                </div>
                <div class="col-md-6">
                    <strong>Ek Özellikler:</strong>
                    <table class="table table-sm">
                        <tr><td><code>options</code></td><td>Seçenek listesi (JS formatı)</td></tr>
                    </table>
                    <div class="alert alert-info mt-2">
                        <small><strong>Options Formatı:</strong><br>
                        Layout JSON: <code>[{'{'}value, label, is_default{'}'}]</code><br>
                        Settings DB: <code>{'{'}key: label{'}'}</code></small>
                    </div>
                </div>
            </div>

            {{-- RADIO --}}
            <h4 class="text-warning mt-4"><i class="fas fa-dot-circle me-2"></i>radio - Radyo Buton</h4>
            <p class="text-muted">Tek seçimlik liste. Az seçenek olduğunda kullan.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "radio",
    "properties": {
        "name": "layout_type",
        "label": "Sayfa Düzeni",
        "help_text": "Ana sayfa için varsayılan düzen tipi",
        "width": 12,
        "options": [
            {"value": "full", "label": "Tam Genişlik", "is_default": true},
            {"value": "boxed", "label": "Kutu Düzeni", "is_default": false}
        ]
    }
}</code></pre>

            {{-- SWITCH --}}
            <h4 class="text-info mt-5"><i class="fas fa-toggle-on me-2"></i>switch - Aç/Kapa</h4>
            <p class="text-muted">Boolean değerler için. Açık/Kapalı, Evet/Hayır gibi.</p>
            <div class="row">
                <div class="col-md-6">
                    <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "switch",
    "properties": {
        "name": "maintenance_mode",
        "label": "Bakım Modu",
        "help_text": "Açıldığında site ziyaretçilere kapatılır",
        "width": 6
    }
}</code></pre>
                </div>
                <div class="col-md-6">
                    <strong>Değerler:</strong>
                    <ul>
                        <li>Açık: <code>"1"</code> (string)</li>
                        <li>Kapalı: <code>"0"</code> (string)</li>
                    </ul>
                    <strong>PHP'de Kontrol:</strong>
                    <pre class="bg-secondary text-light p-2 rounded small"><code>if (setting('maintenance_mode') === '1') {
    // Bakım modu açık
}</code></pre>
                </div>
            </div>

            {{-- CHECKBOX --}}
            <h4 class="text-info mt-4"><i class="fas fa-check-square me-2"></i>checkbox - Onay Kutusu</h4>
            <p class="text-muted">Tek bir onay kutusu. Switch'e benzer ama farklı görünüm.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "checkbox",
    "properties": {
        "name": "show_prices",
        "label": "Fiyatları Göster",
        "help_text": "İşaretlenirse ürün fiyatları görünür olur",
        "width": 6
    }
}</code></pre>

            {{-- IMAGE --}}
            <h4 class="text-purple mt-5"><i class="fas fa-image me-2"></i>image - Görsel Yükleme</h4>
            <p class="text-muted">Logo, favicon, arka plan gibi görseller için.</p>
            <div class="row">
                <div class="col-md-6">
                    <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "image",
    "properties": {
        "name": "site_logo",
        "label": "Site Logosu",
        "help_text": "Önerilen boyut: 200x60px, PNG veya SVG formatı",
        "width": 6,
        "accept": "image/png,image/svg+xml",
        "maxSize": 2048
    }
}</code></pre>
                </div>
                <div class="col-md-6">
                    <strong>Ek Özellikler:</strong>
                    <table class="table table-sm">
                        <tr><td><code>accept</code></td><td>Kabul edilen dosya tipleri</td></tr>
                        <tr><td><code>maxSize</code></td><td>Max boyut (KB)</td></tr>
                    </table>
                </div>
            </div>

            {{-- FAVICON --}}
            <h4 class="text-purple mt-4"><i class="fas fa-star me-2"></i>favicon - Favicon</h4>
            <p class="text-muted">Tarayıcı sekmesi ikonu için özel tip.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "favicon",
    "properties": {
        "name": "site_favicon",
        "label": "Site Favicon",
        "help_text": "32x32px ICO veya PNG formatı. Tarayıcı sekmesinde görünür.",
        "width": 6
    }
}</code></pre>

            {{-- FILE --}}
            <h4 class="text-purple mt-4"><i class="fas fa-file me-2"></i>file - Dosya Yükleme</h4>
            <p class="text-muted">PDF, Excel gibi dosyalar için.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "file",
    "properties": {
        "name": "terms_pdf",
        "label": "Kullanım Koşulları PDF",
        "help_text": "Müşterilerinize gösterilecek kullanım koşulları belgesi",
        "width": 6,
        "accept": "application/pdf"
    }
}</code></pre>

            {{-- COLOR --}}
            <h4 class="text-danger mt-5"><i class="fas fa-palette me-2"></i>color - Renk Seçici</h4>
            <p class="text-muted">Tema rengi, marka rengi gibi değerler için.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "color",
    "properties": {
        "name": "primary_color",
        "label": "Ana Renk",
        "help_text": "Sitenizin ana tema rengi (butonlar, linkler)",
        "width": 4
    }
}</code></pre>

            {{-- DATE --}}
            <h4 class="text-secondary mt-4"><i class="fas fa-calendar me-2"></i>date - Tarih</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "date",
    "properties": {
        "name": "campaign_end_date",
        "label": "Kampanya Bitiş Tarihi",
        "help_text": "Bu tarihten sonra kampanya otomatik kapanır",
        "width": 6
    }
}</code></pre>

            {{-- TIME --}}
            <h4 class="text-secondary mt-4"><i class="fas fa-clock me-2"></i>time - Saat</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "time",
    "properties": {
        "name": "store_opening_time",
        "label": "Mağaza Açılış Saati",
        "help_text": "Çalışma saatleri bilgisinde gösterilir",
        "width": 4
    }
}</code></pre>

            {{-- ROW (Layout) --}}
            <h4 class="text-dark mt-5"><i class="fas fa-columns me-2"></i>row - Satır Düzeni (Layout)</h4>
            <p class="text-muted">Elementleri yan yana yerleştirmek için kullanılır.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "row",
    "columns": [
        {
            "width": 6,
            "elements": [
                {"type": "text", "properties": {"name": "field1", "label": "Sol Alan"}}
            ]
        },
        {
            "width": 6,
            "elements": [
                {"type": "text", "properties": {"name": "field2", "label": "Sağ Alan"}}
            ]
        }
    ]
}</code></pre>

            {{-- SEPARATOR --}}
            <h4 class="text-dark mt-4"><i class="fas fa-minus me-2"></i>separator - Ayırıcı Çizgi</h4>
            <p class="text-muted">Form bölümlerini ayırmak için.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "separator",
    "properties": {
        "label": "Gelişmiş Ayarlar",
        "width": 12
    }
}</code></pre>

            {{-- HEADING --}}
            <h4 class="text-dark mt-4"><i class="fas fa-heading me-2"></i>heading - Başlık</h4>
            <p class="text-muted">Form içinde bölüm başlığı eklemek için.</p>
            <pre class="bg-dark text-light p-3 rounded"><code>{
    "type": "heading",
    "properties": {
        "label": "API Ayarları",
        "level": "h4",
        "width": 12
    }
}</code></pre>

            <div class="alert alert-success mt-5">
                <h5><i class="fas fa-lightbulb me-2"></i>En İyi Uygulamalar</h5>
                <ul class="mb-0">
                    <li><strong>help_text</strong> mutlaka ekle - "Bu ne işe yarar?" sorusunu yanıtla</li>
                    <li><strong>placeholder</strong> örnek değer göster - Kullanıcı ne yazacağını anlasın</li>
                    <li>İlgili alanları <strong>row</strong> ile grupla - Görsel düzen iyileşir</li>
                    <li>Uzun formları <strong>separator</strong> ile böl - Okunabilirlik artar</li>
                    <li>Switch için <strong>help_text</strong>'te sonucu açıkla - "Açıldığında X olur"</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- AKIŞ DİYAGRAMI --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-project-diagram me-2"></i>4. VERİ AKIŞI</h3>
        </div>
        <div class="card-body">
            <h4>4.1 Ayar Değeri Okuma</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>setting('site_title')
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 1. Cache kontrol (SettingsService)                            │
│    - Cache varsa → direkt döndür                              │
│    - Cache yoksa → DB'den çek                                 │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 2. Central DB: settings tablosunda key='site_title' bul       │
│    → setting_id = 1                                           │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 3. Tenant DB: settings_values'dan setting_id=1 değerini al    │
│    → value = "İxtif Endüstriyel"                              │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 4. Değer yoksa → settings.default_value kullan                │
│    Değer varsa → settings_values.value döndür                 │
└───────────────────────────────────────────────────────────────┘</code></pre>

            <h4 class="mt-5">4.2 FormBuilder Kaydetme</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>FormBuilder "Kaydet" butonu
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 1. JavaScript: getFormJSON() → Layout JSON oluştur            │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 2. Livewire: saveLayout() çağrılır                            │
│    - FormBuilderComponent.php                                  │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 3. syncSettingsFromLayout() otomatik çalışır:                 │
│    - Layout'taki her element için settings tablosunu güncelle │
│    - Yeni element varsa → Setting oluştur                     │
│    - Select options varsa → settings.options'a kaydet         │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 4. settings_groups.layout = JSON olarak kaydet                │
└───────────────────────────────────────────────────────────────┘</code></pre>

            <h4 class="mt-5">4.3 FormBuilder Yükleme</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>FormBuilder sayfası açılır
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 1. API: /form-builder/{groupId}/load                          │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 2. SettingGroup::find($id)->layout                            │
│    - getLayoutAttribute() accessor çalışır                    │
│    - Select options otomatik JS formatına dönüştürülür        │
└───────────────────────────────────────────────────────────────┘
        │
        ▼
┌───────────────────────────────────────────────────────────────┐
│ 3. JavaScript: loadFormFromJSON(layout)                       │
│    - Her element için createFormElement() çağrılır            │
│    - Select options → &lt;option&gt; elementleri oluşturulur        │
└───────────────────────────────────────────────────────────────┘</code></pre>
        </div>
    </div>

    {{-- KULLANIM ÖRNEKLERİ --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-terminal me-2"></i>5. KULLANIM ÖRNEKLERİ</h3>
        </div>
        <div class="card-body">
            <h4>5.1 Yeni Grup Oluşturma (PHP/Tinker)</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>use Modules\SettingManagement\App\Models\SettingGroup;

// Ana grup altında yeni alt grup
$group = SettingGroup::create([
    'parent_id' => 1,              // 1 = "Genel" ana grubu
    'name' => 'API Ayarları',
    'slug' => 'api-ayarlari',
    'icon' => 'fas fa-plug',
    'prefix' => 'api',             // Ayarlar api_xxx olarak adlandırılır
    'is_active' => true,
]);

echo "Yeni grup ID: " . $group->id;</code></pre>

            <h4 class="mt-4">5.2 Yeni Setting Oluşturma</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>use Modules\SettingManagement\App\Models\Setting;

// Text input
Setting::create([
    'group_id' => $group->id,
    'key' => 'api_base_url',
    'label' => 'API Base URL',
    'type' => 'text',
    'default_value' => 'https://api.example.com',
    'is_required' => true,
    'is_active' => true,
    'sort_order' => 10,
]);

// Select input
Setting::create([
    'group_id' => $group->id,
    'key' => 'api_version',
    'label' => 'API Versiyon',
    'type' => 'select',
    'options' => [
        'v1' => 'Version 1',
        'v2' => 'Version 2 (Önerilen)',
        'v3' => 'Version 3 (Beta)',
    ],
    'default_value' => 'v2',
    'is_active' => true,
    'sort_order' => 20,
]);

// Switch (boolean)
Setting::create([
    'group_id' => $group->id,
    'key' => 'api_debug_mode',
    'label' => 'Debug Modu',
    'type' => 'switch',
    'default_value' => '0',
    'is_active' => true,
    'sort_order' => 30,
]);</code></pre>

            <h4 class="mt-4">5.3 Layout Oluşturma</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>$group->layout = [
    'title' => 'API Ayarları',
    'elements' => [
        [
            'type' => 'text',
            'properties' => [
                'name' => 'api_base_url',
                'label' => 'API Base URL',
                'placeholder' => 'https://api.example.com',
                'help_text' => 'API endpoint adresi',
                'width' => 12,
                'required' => true
            ]
        ],
        [
            'type' => 'row',
            'columns' => [
                [
                    'width' => 6,
                    'elements' => [
                        [
                            'type' => 'select',
                            'properties' => [
                                'name' => 'api_version',
                                'label' => 'API Versiyon',
                                'width' => 12
                                // options otomatik settings'ten çekilir
                            ]
                        ]
                    ]
                ],
                [
                    'width' => 6,
                    'elements' => [
                        [
                            'type' => 'switch',
                            'properties' => [
                                'name' => 'api_debug_mode',
                                'label' => 'Debug Modu',
                                'width' => 12
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$group->save();</code></pre>

            <h4 class="mt-4">5.4 Ayar Değeri Okuma/Yazma</h4>
            <pre class="bg-dark text-light p-3 rounded"><code>// Okuma - Helper fonksiyon
$apiUrl = setting('api_base_url');
$isDebug = setting('api_debug_mode'); // "1" veya "0" string döner

// Boolean kontrol
if (setting('api_debug_mode') === '1') {
    // Debug modu açık
}

// Yazma - SettingValue modeli ile
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;

$setting = Setting::where('key', 'api_base_url')->first();
SettingValue::updateOrCreate(
    ['setting_id' => $setting->id],
    ['value' => 'https://new-api.example.com']
);

// Cache temizle
Cache::forget('settings_' . tenant('id'));</code></pre>
        </div>
    </div>

    {{-- ÖNEMLİ NOTLAR --}}
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>6. ÖNEMLİ NOTLAR VE KURALLAR</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="text-success"><i class="fas fa-check-circle me-2"></i>YAPILMASI GEREKENLER</h4>
                    <ul>
                        <li><strong>Key Adlandırma:</strong> <code>prefix_feature_name</code> formatı kullan
                            <br><small class="text-muted">Örn: site_title, paytr_enabled, api_base_url</small>
                        </li>
                        <li><strong>Select Options:</strong> Her zaman <code>{"key": "label"}</code> formatında kaydet</li>
                        <li><strong>Group Prefix:</strong> Yeni grup oluştururken prefix belirle</li>
                        <li><strong>Layout Senkron:</strong> FormBuilder kullanılıyorsa layout oluştur</li>
                        <li><strong>Cache:</strong> Değer değişikliğinde cache temizle</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4 class="text-danger"><i class="fas fa-times-circle me-2"></i>YAPILMAMASI GEREKENLER</h4>
                    <ul>
                        <li><strong>Duplicate Key:</strong> Aynı key'i iki kez kullanma</li>
                        <li><strong>Hardcode:</strong> Seeder'larda layout JSON hardcode etme</li>
                        <li><strong>Direct DB:</strong> settings_values'a direkt yazmak yerine service kullan</li>
                        <li><strong>Tenant Karışıklığı:</strong> Central DB'ye tenant verisi yazma</li>
                        <li><strong>Format Karışıklığı:</strong> Options formatını manuel değiştirme</li>
                    </ul>
                </div>
            </div>

            <h4 class="mt-4 text-info"><i class="fas fa-lightbulb me-2"></i>OTOMATİK DAVRANIŞLAR</h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Olay</th>
                        <th>Otomatik Davranış</th>
                        <th>Dosya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Layout okunduğunda</td>
                        <td>Select options JS formatına dönüştürülür</td>
                        <td><code>SettingGroup::getLayoutAttribute()</code></td>
                    </tr>
                    <tr>
                        <td>FormBuilder kaydedildiğinde</td>
                        <td>Settings tablosu senkronize edilir</td>
                        <td><code>FormBuilderComponent::syncSettingsFromLayout()</code></td>
                    </tr>
                    <tr>
                        <td>Yeni element eklendiğinde</td>
                        <td>Setting otomatik oluşturulur</td>
                        <td><code>FormBuilderComponent::syncSettingsFromLayout()</code></td>
                    </tr>
                    <tr>
                        <td>Options değiştiğinde</td>
                        <td>Settings.options güncellenir</td>
                        <td><code>FormBuilderComponent::extractSettingsRecursive()</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- DOSYA YAPISI --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-folder-tree me-2"></i>7. DOSYA YAPISI</h3>
        </div>
        <div class="card-body">
            <pre class="bg-dark text-light p-3 rounded"><code>Modules/SettingManagement/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── SettingManagementController.php  # Ana controller
│   │   └── Livewire/
│   │       └── FormBuilderComponent.php         # FormBuilder Livewire
│   ├── Models/
│   │   ├── Setting.php                          # Ayar şeması modeli
│   │   ├── SettingGroup.php                     # Grup modeli (layout accessor)
│   │   └── SettingValue.php                     # Tenant değer modeli
│   └── Services/
│       └── SettingsService.php                  # Cache ve helper
├── database/
│   ├── migrations/
│   │   ├── create_settings_groups_table.php    # Central
│   │   └── create_settings_table.php           # Central
│   └── migrations/tenant/
│       └── create_settings_values_table.php    # Tenant
├── resources/views/
│   ├── form-builder/
│   │   ├── edit.blade.php                       # FormBuilder sayfası
│   │   └── partials/form-elements/              # Element blade'leri
│   ├── groups/
│   │   └── index.blade.php                      # Grup listesi
│   └── values/
│       └── index.blade.php                      # Değer düzenleme
└── routes/
    └── web.php                                  # Modül route'ları

public/admin-assets/libs/form-builder/settingmanagement/js/
├── form-builder.js                              # Ana başlatma
├── form-builder-core.js                         # loadFormFromJSON, getFormJSON
├── form-builder-elements.js                     # createFormElement, options işleme
└── form-builder-templates.js                    # HTML şablonları</code></pre>
        </div>
    </div>

    {{-- MEVCUT GRUPLAR --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list me-2"></i>8. MEVCUT GRUPLAR VE AYARLAR</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>AI Asistan Dikkat!</strong> Bu tablo dinamik verilerdir ve zamanla değişebilir.
                Grup ID'leri ve ayar sayıları güncel olmayabilir. Kod yazarken bu verileri hardcode etme,
                her zaman veritabanından sorgula.
                <br><br>
                <small class="text-muted">Son güncelleme: {{ now()->format('d.m.Y H:i') }}</small>
            </div>
            @php
                $groups = \Modules\SettingManagement\App\Models\SettingGroup::whereNotNull('parent_id')
                    ->orderBy('id')
                    ->get();
            @endphp

            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Grup Adı</th>
                        <th>Prefix</th>
                        <th>Ayar Sayısı</th>
                        <th>Layout</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                    @php
                        $settingCount = \Modules\SettingManagement\App\Models\Setting::where('group_id', $group->id)->count();
                        $hasLayout = !empty($group->getRawOriginal('layout'));
                    @endphp
                    <tr>
                        <td>{{ $group->id }}</td>
                        <td>{{ $group->name }}</td>
                        <td><code>{{ $group->prefix ?? '-' }}</code></td>
                        <td>{{ $settingCount }}</td>
                        <td>{!! $hasLayout ? '<span class="badge bg-success">Var</span>' : '<span class="badge bg-secondary">Yok</span>' !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
