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
        // SEO alanlar seo_settings tablosunda tutulacak, sadece central'da çalış
        $this->command->info('PortfolioCategorySeeder başlatılıyor...');
        
        // TenantHelpers ile çalışma durumunu kontrol et - HER TENANT'TA kategoriler olmalı
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

        // JSON formatında çoklu dil verileri - SEO'lar seo_settings'de olacak
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
                ]
            ]
        ];

        foreach ($categories as $index => $category) {
            // Var mı kontrol et - yoksa oluştur
            $existingCategory = PortfolioCategory::where('slug->tr', $category['slug']['tr'])->first();
            
            if (!$existingCategory) {
                PortfolioCategory::create([
                    'title' => $category['title'],
                    'slug' => $category['slug'],
                    'body' => $category['body'],
                    'order' => $index,
                    'is_active' => true,
                ]);
                // $this->command->info("Oluşturuldu"): ' . $category['title']['tr']);
            } else {
                $this->command->info('Kategori zaten var: ' . $category['title']['tr']);
            }
        }
        
        if (TenantHelpers::isCentral()) {
            // $this->command->info("Oluşturuldu").');
        } else {
            // $this->command->info("Oluşturuldu").');
        }
    }
}