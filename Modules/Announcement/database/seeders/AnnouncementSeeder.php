<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use Faker\Factory as Faker;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('tr_TR');

        // JSON formatında çoklu dil verileri
        $announcements = [
            [
                'title' => [
                    'tr' => 'Yeni Hizmetimiz Yayında!',
                    'en' => 'Our New Service is Live!',
                    'ar' => 'خدمتنا الجديدة مباشرة!'
                ],
                'slug' => [
                    'tr' => 'yeni-hizmetimiz-yayinda',
                    'en' => 'new-service-live',
                    'ar' => 'خدمة-جديدة-مباشرة'
                ],
                'body' => [
                    'tr' => '<div class="announcement-content">
                        <h2>Müşterilerimize Müjde!</h2>
                        <p>Uzun süredir üzerinde çalıştığımız yeni hizmetimiz artık yayında. Bu hizmet ile birlikte:</p>
                        <ul>
                            <li>Daha hızlı işlem süreleri</li>
                            <li>Gelişmiş kullanıcı arayüzü</li>
                            <li>7/24 destek hizmeti</li>
                            <li>Mobil uygulama desteği</li>
                        </ul>
                        <p>Detaylı bilgi için bizimle iletişime geçebilirsiniz.</p>
                    </div>',
                    'en' => '<div class="announcement-content">
                        <h2>Great News for Our Customers!</h2>
                        <p>Our new service that we have been working on for a long time is now live. With this service:</p>
                        <ul>
                            <li>Faster processing times</li>
                            <li>Advanced user interface</li>
                            <li>24/7 support service</li>
                            <li>Mobile app support</li>
                        </ul>
                        <p>Contact us for detailed information.</p>
                    </div>',
                    'ar' => '<div class="announcement-content">
                        <h2>أخبار رائعة لعملائنا!</h2>
                        <p>خدمتنا الجديدة التي كنا نعمل عليها لفترة طويلة متاحة الآن. مع هذه الخدمة:</p>
                        <ul>
                            <li>أوقات معالجة أسرع</li>
                            <li>واجهة مستخدم متقدمة</li>
                            <li>خدمة دعم على مدار الساعة</li>
                            <li>دعم تطبيق الهاتف المحمول</li>
                        </ul>
                        <p>اتصل بنا للحصول على معلومات مفصلة.</p>
                    </div>'
                ],
                'metakey' => [
                    'tr' => 'yeni hizmet, duyuru, müjde, güncelleme',
                    'en' => 'new service, announcement, news, update',
                    'ar' => 'خدمة جديدة، إعلان، أخبار، تحديث'
                ],
                'metadesc' => [
                    'tr' => 'Yeni hizmetimiz hakkında önemli duyuru',
                    'en' => 'Important announcement about our new service',
                    'ar' => 'إعلان مهم حول خدمتنا الجديدة'
                ]
            ],
            [
                'title' => [
                    'tr' => 'Bakım Çalışması Duyurusu',
                    'en' => 'Maintenance Work Announcement',
                    'ar' => 'إعلان أعمال الصيانة'
                ],
                'slug' => [
                    'tr' => 'bakim-calismasi-duyurusu',
                    'en' => 'maintenance-work-announcement',
                    'ar' => 'إعلان-أعمال-الصيانة'
                ],
                'body' => [
                    'tr' => '<div class="announcement-content">
                        <h2>Planlı Bakım Çalışması</h2>
                        <p><strong>Tarih:</strong> 15 Temmuz 2024, Cumartesi<br>
                        <strong>Saat:</strong> 02:00 - 06:00 (GMT+3)</p>
                        <p>Sistemlerimizi daha iyi hale getirmek için planlı bir bakım çalışması gerçekleştireceğiz. Bu süre zarfında:</p>
                        <ul>
                            <li>Web sitemize erişim geçici olarak kısıtlanabilir</li>
                            <li>Bazı hizmetler kesintiye uğrayabilir</li>
                            <li>Mobil uygulama sınırlı çalışabilir</li>
                        </ul>
                        <p>Anlayışınız için teşekkür ederiz.</p>
                    </div>',
                    'en' => '<div class="announcement-content">
                        <h2>Scheduled Maintenance</h2>
                        <p><strong>Date:</strong> July 15, 2024, Saturday<br>
                        <strong>Time:</strong> 02:00 - 06:00 (GMT+3)</p>
                        <p>We will be performing scheduled maintenance to improve our systems. During this time:</p>
                        <ul>
                            <li>Access to our website may be temporarily restricted</li>
                            <li>Some services may be interrupted</li>
                            <li>Mobile app may work with limitations</li>
                        </ul>
                        <p>Thank you for your understanding.</p>
                    </div>',
                    'ar' => '<div class="announcement-content">
                        <h2>صيانة مجدولة</h2>
                        <p><strong>التاريخ:</strong> 15 يوليو 2024، السبت<br>
                        <strong>الوقت:</strong> 02:00 - 06:00 (GMT+3)</p>
                        <p>سنقوم بإجراء صيانة مجدولة لتحسين أنظمتنا. خلال هذا الوقت:</p>
                        <ul>
                            <li>قد يكون الوصول إلى موقعنا مقيدًا مؤقتًا</li>
                            <li>قد تتعطل بعض الخدمات</li>
                            <li>قد يعمل تطبيق الهاتف المحمول بقيود</li>
                        </ul>
                        <p>شكرا لتفهمك.</p>
                    </div>'
                ],
                'metakey' => [
                    'tr' => 'bakım, sistem güncellemesi, duyuru, kesinti',
                    'en' => 'maintenance, system update, announcement, outage',
                    'ar' => 'صيانة، تحديث النظام، إعلان، انقطاع'
                ],
                'metadesc' => [
                    'tr' => 'Planlı sistem bakımı hakkında duyuru',
                    'en' => 'Announcement about scheduled system maintenance',
                    'ar' => 'إعلان حول صيانة النظام المجدولة'
                ]
            ],
            [
                'title' => [
                    'tr' => 'Yılbaşı Kampanyası',
                    'en' => 'New Year Campaign',
                    'ar' => 'حملة رأس السنة'
                ],
                'slug' => [
                    'tr' => 'yilbasi-kampanyasi',
                    'en' => 'new-year-campaign',
                    'ar' => 'حملة-رأس-السنة'
                ],
                'body' => [
                    'tr' => '<div class="announcement-content">
                        <h2>🎄 Özel Yılbaşı Kampanyası! 🎄</h2>
                        <p>Değerli müşterilerimiz, yeni yıla özel muhteşem kampanyamız başladı!</p>
                        <h3>Kampanya Detayları:</h3>
                        <ul>
                            <li>Tüm ürünlerde %25 indirim</li>
                            <li>İkinci üründe %50 indirim</li>
                            <li>100 TL üzeri alışverişlerde ücretsiz kargo</li>
                            <li>Yeni üyelere özel %10 ekstra indirim</li>
                        </ul>
                        <p><strong>Kampanya Süresi:</strong> 15 Aralık - 5 Ocak</p>
                        <p>Bu fırsatı kaçırmayın! Hemen alışverişe başlayın.</p>
                    </div>',
                    'en' => '<div class="announcement-content">
                        <h2>🎄 Special New Year Campaign! 🎄</h2>
                        <p>Dear customers, our amazing New Year campaign has started!</p>
                        <h3>Campaign Details:</h3>
                        <ul>
                            <li>25% discount on all products</li>
                            <li>50% discount on second product</li>
                            <li>Free shipping on orders over 100 TL</li>
                            <li>Extra 10% discount for new members</li>
                        </ul>
                        <p><strong>Campaign Period:</strong> December 15 - January 5</p>
                        <p>Don\'t miss this opportunity! Start shopping now.</p>
                    </div>',
                    'ar' => '<div class="announcement-content">
                        <h2>🎄 حملة رأس السنة الخاصة! 🎄</h2>
                        <p>عملاؤنا الأعزاء، لقد بدأت حملة رأس السنة المذهلة!</p>
                        <h3>تفاصيل الحملة:</h3>
                        <ul>
                            <li>خصم 25% على جميع المنتجات</li>
                            <li>خصم 50% على المنتج الثاني</li>
                            <li>شحن مجاني للطلبات فوق 100 ليرة</li>
                            <li>خصم إضافي 10% للأعضاء الجدد</li>
                        </ul>
                        <p><strong>فترة الحملة:</strong> 15 ديسمبر - 5 يناير</p>
                        <p>لا تفوت هذه الفرصة! ابدأ التسوق الآن.</p>
                    </div>'
                ],
                'metakey' => [
                    'tr' => 'kampanya, yılbaşı, indirim, fırsat',
                    'en' => 'campaign, new year, discount, opportunity',
                    'ar' => 'حملة، رأس السنة، خصم، فرصة'
                ],
                'metadesc' => [
                    'tr' => 'Yılbaşına özel büyük indirim kampanyası',
                    'en' => 'Special New Year discount campaign',
                    'ar' => 'حملة خصم خاصة برأس السنة'
                ]
            ]
        ];

        foreach ($announcements as $announcement) {
            Announcement::create([
                'title' => $announcement['title'],
                'slug' => $announcement['slug'],
                'body' => $announcement['body'],
                'metakey' => $announcement['metakey'],
                'metadesc' => $announcement['metadesc'],
                'is_active' => true,
            ]);
        }
    }
}