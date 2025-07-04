<?php
namespace Modules\Portfolio\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

class PortfolioCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder hem central hem tenant'ta çalışabilir
        if (TenantHelpers::isCentral()) {
            $this->command->info('PortfolioCategorySeeder central veritabanında çalışıyor...');
        } else {
            $this->command->info('PortfolioCategorySeeder tenant veritabanında çalışıyor...');
        }
        
        // Tablo var mı kontrol et
        if (!Schema::hasTable('portfolio_categories')) {
            $this->command->info('portfolio_categories tablosu bulunamadı, işlem atlanıyor...');
            return;
        }
        
        $faker = Faker::create('tr_TR');

        // JSON formatında çoklu dil verileri
        $categories = [
            [
                'title' => [
                    'tr' => 'Web Tasarım',
                    'en' => 'Web Design',
                    'ar' => 'تصميم الويب'
                ],
                'slug' => [
                    'tr' => 'web-tasarim',
                    'en' => 'web-design',
                    'ar' => 'تصميم-الويب'
                ],
                'body' => [
                    'tr' => '<h2>Web Tasarım Projelerimiz</h2>
                        <p>Modern web tasarım anlayışı ile kullanıcı deneyimi odaklı projeler üretiyoruz.</p>
                        <ul>
                            <li>Responsive tasarım</li>
                            <li>SEO uyumlu kodlama</li>
                            <li>Hızlı yüklenen sayfalar</li>
                        </ul>',
                    'en' => '<h2>Our Web Design Projects</h2>
                        <p>We produce user experience-focused projects with modern web design approach.</p>
                        <ul>
                            <li>Responsive design</li>
                            <li>SEO compatible coding</li>
                            <li>Fast loading pages</li>
                        </ul>',
                    'ar' => '<h2>مشاريع تصميم الويب لدينا</h2>
                        <p>نحن ننتج مشاريع تركز على تجربة المستخدم مع نهج تصميم الويب الحديث.</p>
                        <ul>
                            <li>تصميم متجاوب</li>
                            <li>ترميز متوافق مع SEO</li>
                            <li>صفحات سريعة التحميل</li>
                        </ul>'
                ],
                'metakey' => [
                    'tr' => 'web tasarım, responsive, modern web, ui ux',
                    'en' => 'web design, responsive, modern web, ui ux',
                    'ar' => 'تصميم الويب، متجاوب، ويب حديث'
                ],
                'metadesc' => [
                    'tr' => 'Modern ve kullanıcı dostu web tasarım projelerimiz',
                    'en' => 'Our modern and user-friendly web design projects',
                    'ar' => 'مشاريع تصميم الويب الحديثة وسهلة الاستخدام'
                ],
                'seo' => [
                    'tr' => [
                        'title' => 'Web Tasarım Hizmetleri | Profesyonel Çözümler',
                        'description' => 'Modern web tasarım anlayışı ile kullanıcı deneyimi odaklı, responsive ve SEO uyumlu web siteleri geliştiriyoruz.',
                        'keywords' => 'web tasarım, responsive tasarım, modern web, ui ux, kullanıcı deneyimi'
                    ],
                    'en' => [
                        'title' => 'Web Design Services | Professional Solutions',
                        'description' => 'We develop user experience-focused, responsive and SEO-friendly websites with modern web design approach.',
                        'keywords' => 'web design, responsive design, modern web, ui ux, user experience'
                    ],
                    'ar' => [
                        'title' => 'خدمات تصميم الويب | حلول احترافية',
                        'description' => 'نطور مواقع ويب تركز على تجربة المستخدم ومتجاوبة ومتوافقة مع SEO بنهج تصميم ويب حديث.',
                        'keywords' => 'تصميم الويب، تصميم متجاوب، ويب حديث، تجربة المستخدم'
                    ]
                ]
            ],
            [
                'title' => [
                    'tr' => 'Mobil Uygulama',
                    'en' => 'Mobile Application',
                    'ar' => 'تطبيق الهاتف المحمول'
                ],
                'slug' => [
                    'tr' => 'mobil-uygulama',
                    'en' => 'mobile-application',
                    'ar' => 'تطبيق-الهاتف-المحمول'
                ],
                'body' => [
                    'tr' => '<h2>Mobil Uygulama Geliştirme</h2>
                        <p>iOS ve Android platformları için native ve cross-platform uygulamalar geliştiriyoruz.</p>
                        <ul>
                            <li>Native iOS (Swift)</li>
                            <li>Native Android (Kotlin)</li>
                            <li>Cross-platform (Flutter, React Native)</li>
                        </ul>',
                    'en' => '<h2>Mobile Application Development</h2>
                        <p>We develop native and cross-platform applications for iOS and Android platforms.</p>
                        <ul>
                            <li>Native iOS (Swift)</li>
                            <li>Native Android (Kotlin)</li>
                            <li>Cross-platform (Flutter, React Native)</li>
                        </ul>',
                    'ar' => '<h2>تطوير تطبيقات الهاتف المحمول</h2>
                        <p>نقوم بتطوير تطبيقات أصلية ومتعددة المنصات لمنصات iOS و Android.</p>
                        <ul>
                            <li>iOS أصلي (Swift)</li>
                            <li>Android أصلي (Kotlin)</li>
                            <li>متعدد المنصات (Flutter، React Native)</li>
                        </ul>'
                ],
                'metakey' => [
                    'tr' => 'mobil uygulama, ios, android, flutter',
                    'en' => 'mobile app, ios, android, flutter',
                    'ar' => 'تطبيق محمول، ios، android، flutter'
                ],
                'metadesc' => [
                    'tr' => 'iOS ve Android için mobil uygulama geliştirme hizmetleri',
                    'en' => 'Mobile application development services for iOS and Android',
                    'ar' => 'خدمات تطوير تطبيقات الهاتف المحمول لنظامي iOS و Android'
                ],
                'seo' => [
                    'tr' => [
                        'title' => 'Mobil Uygulama Geliştirme | iOS & Android',
                        'description' => 'iOS ve Android platformları için native ve cross-platform mobil uygulamalar geliştiriyoruz. Swift, Kotlin, Flutter uzmanı ekip.',
                        'keywords' => 'mobil uygulama, ios, android, flutter, swift, kotlin, react native'
                    ],
                    'en' => [
                        'title' => 'Mobile App Development | iOS & Android',
                        'description' => 'We develop native and cross-platform mobile applications for iOS and Android platforms. Expert team in Swift, Kotlin, Flutter.',
                        'keywords' => 'mobile app, ios, android, flutter, swift, kotlin, react native'
                    ],
                    'ar' => [
                        'title' => 'تطوير تطبيقات الهاتف المحمول | iOS & Android',
                        'description' => 'نطور تطبيقات الهاتف المحمول الأصلية ومتعددة المنصات لمنصات iOS و Android. فريق خبير في Swift و Kotlin و Flutter.',
                        'keywords' => 'تطبيق محمول، ios، android، flutter، swift، kotlin، react native'
                    ]
                ]
            ],
            [
                'title' => [
                    'tr' => 'E-Ticaret',
                    'en' => 'E-Commerce',
                    'ar' => 'التجارة الإلكترونية'
                ],
                'slug' => [
                    'tr' => 'e-ticaret',
                    'en' => 'e-commerce',
                    'ar' => 'التجارة-الإلكترونية'
                ],
                'body' => [
                    'tr' => '<h2>E-Ticaret Çözümleri</h2>
                        <p>Güvenli ve kullanıcı dostu e-ticaret sistemleri kuruyoruz.</p>
                        <ul>
                            <li>Özel e-ticaret yazılımları</li>
                            <li>Ödeme sistemi entegrasyonları</li>
                            <li>Stok ve sipariş yönetimi</li>
                            <li>B2B ve B2C çözümleri</li>
                        </ul>',
                    'en' => '<h2>E-Commerce Solutions</h2>
                        <p>We build secure and user-friendly e-commerce systems.</p>
                        <ul>
                            <li>Custom e-commerce software</li>
                            <li>Payment system integrations</li>
                            <li>Stock and order management</li>
                            <li>B2B and B2C solutions</li>
                        </ul>',
                    'ar' => '<h2>حلول التجارة الإلكترونية</h2>
                        <p>نبني أنظمة تجارة إلكترونية آمنة وسهلة الاستخدام.</p>
                        <ul>
                            <li>برمجيات التجارة الإلكترونية المخصصة</li>
                            <li>تكامل أنظمة الدفع</li>
                            <li>إدارة المخزون والطلبات</li>
                            <li>حلول B2B و B2C</li>
                        </ul>'
                ],
                'metakey' => [
                    'tr' => 'e-ticaret, online satış, ödeme sistemleri, b2b',
                    'en' => 'e-commerce, online sales, payment systems, b2b',
                    'ar' => 'التجارة الإلكترونية، المبيعات عبر الإنترنت، أنظمة الدفع'
                ],
                'metadesc' => [
                    'tr' => 'Profesyonel e-ticaret çözümleri ve online satış sistemleri',
                    'en' => 'Professional e-commerce solutions and online sales systems',
                    'ar' => 'حلول التجارة الإلكترونية المهنية وأنظمة البيع عبر الإنترنت'
                ],
                'seo' => [
                    'tr' => [
                        'title' => 'E-Ticaret Çözümleri | Online Satış Sistemleri',
                        'description' => 'Güvenli ve kullanıcı dostu e-ticaret sistemleri kuruyoruz. Özel yazılım, ödeme entegrasyonları ve stok yönetimi.',
                        'keywords' => 'e-ticaret, online satış, ödeme sistemleri, b2b, b2c, stok yönetimi'
                    ],
                    'en' => [
                        'title' => 'E-Commerce Solutions | Online Sales Systems',
                        'description' => 'We build secure and user-friendly e-commerce systems. Custom software, payment integrations and stock management.',
                        'keywords' => 'e-commerce, online sales, payment systems, b2b, b2c, stock management'
                    ],
                    'ar' => [
                        'title' => 'حلول التجارة الإلكترونية | أنظمة البيع عبر الإنترنت',
                        'description' => 'نبني أنظمة تجارة إلكترونية آمنة وسهلة الاستخدام. برمجيات مخصصة وتكامل الدفع وإدارة المخزون.',
                        'keywords' => 'التجارة الإلكترونية، المبيعات عبر الإنترنت، أنظمة الدفع، b2b، b2c، إدارة المخزون'
                    ]
                ]
            ]
        ];

        foreach ($categories as $index => $category) {
            PortfolioCategory::create([
                'title' => $category['title'],
                'slug' => $category['slug'],
                'body' => $category['body'],
                'order' => $index,
                'metakey' => $category['metakey'],
                'metadesc' => $category['metadesc'],
                'seo' => $category['seo'] ?? null,
                'is_active' => true,
            ]);
        }
    }
}