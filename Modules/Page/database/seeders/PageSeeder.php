<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use Faker\Factory as Faker;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('tr_TR');

        // JSON formatında çoklu dil verileri
        $pages = [
            [
                'title' => [
                    'tr' => 'Anasayfa',
                    'en' => 'Homepage',
                    'ar' => 'الصفحة الرئيسية'
                ],
                'slug' => [
                    'tr' => 'anasayfa',
                    'en' => 'homepage',
                    'ar' => 'الصفحة-الرئيسية'
                ],
                'body' => [
                    'tr' => '<div class="hero-section">
                        <h1 class="display-4">Turkbil Bee\'ye Hoşgeldiniz</h1>
                        <p class="lead">Modern web teknolojileri ile güçlü çözümler üretiyoruz. Dijital dünyanın geleceğini birlikte inşa ediyoruz.</p>
                        <div class="features mt-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>🚀 Hızlı Geliştirme</h3>
                                    <p>Laravel 11 ve modern araçlarla hızlı prototipleme.</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>🔒 Güvenlik Odaklı</h3>
                                    <p>En son güvenlik standartları ile korumalı uygulamalar.</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>📱 Responsive Tasarım</h3>
                                    <p>Tüm cihazlarda mükemmel görünüm.</p>
                                </div>
                            </div>
                        </div>
                    </div>',
                    'en' => '<div class="hero-section">
                        <h1 class="display-4">Welcome to Turkbil Bee</h1>
                        <p class="lead">We create powerful solutions with modern web technologies. Building the future of the digital world together.</p>
                        <div class="features mt-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>🚀 Fast Development</h3>
                                    <p>Rapid prototyping with Laravel 11 and modern tools.</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>🔒 Security Focused</h3>
                                    <p>Protected applications with latest security standards.</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>📱 Responsive Design</h3>
                                    <p>Perfect appearance on all devices.</p>
                                </div>
                            </div>
                        </div>
                    </div>',
                    'ar' => '<div class="hero-section">
                        <h1 class="display-4">مرحباً بكم في Turkbil Bee</h1>
                        <p class="lead">نحن ننشئ حلولاً قوية بتقنيات الويب الحديثة. نبني مستقبل العالم الرقمي معاً.</p>
                        <div class="features mt-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3>🚀 تطوير سريع</h3>
                                    <p>نماذج سريعة باستخدام Laravel 11 والأدوات الحديثة.</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>🔒 مركز على الأمان</h3>
                                    <p>تطبيقات محمية بأحدث معايير الأمان.</p>
                                </div>
                                <div class="col-md-4">
                                    <h3>📱 تصميم متجاوب</h3>
                                    <p>مظهر مثالي على جميع الأجهزة.</p>
                                </div>
                            </div>
                        </div>
                    </div>'
                ],
                'metakey' => [
                    'tr' => 'anasayfa, ana sayfa, hoşgeldin',
                    'en' => 'homepage, home page, welcome',
                    'ar' => 'الصفحة الرئيسية، ترحيب'
                ],
                'metadesc' => [
                    'tr' => 'Web sitemizin ana sayfası',
                    'en' => 'Main page of our website',
                    'ar' => 'الصفحة الرئيسية لموقعنا'
                ],
                'is_homepage' => true,
            ],
            [
                'title' => [
                    'tr' => 'Çerez Politikası',
                    'en' => 'Cookie Policy',
                    'ar' => 'سياسة ملفات تعريف الارتباط'
                ],
                'slug' => [
                    'tr' => 'cerez-politikasi',
                    'en' => 'cookie-policy',
                    'ar' => 'سياسة-ملفات-تعريف-الارتباط'
                ],
                'body' => [
                    'tr' => '<h1>Çerez Politikası</h1><p>Çerez politikamız hakkında bilgiler.</p>',
                    'en' => '<h1>Cookie Policy</h1><p>Information about our cookie policy.</p>',
                    'ar' => '<h1>سياسة ملفات تعريف الارتباط</h1><p>معلومات حول سياسة ملفات تعريف الارتباط الخاصة بنا.</p>'
                ],
                'metakey' => [
                    'tr' => 'çerez, cookie, politika, gizlilik',
                    'en' => 'cookie, policy, privacy',
                    'ar' => 'ملفات تعريف الارتباط، سياسة، خصوصية'
                ],
                'metadesc' => [
                    'tr' => 'Web sitemizin çerez politikası',
                    'en' => 'Cookie policy of our website',
                    'ar' => 'سياسة ملفات تعريف الارتباط لموقعنا'
                ],
                'is_homepage' => false,
            ],
            [
                'title' => [
                    'tr' => 'Kişisel Verilerin İşlenmesi Politikası',
                    'en' => 'Personal Data Processing Policy',
                    'ar' => 'سياسة معالجة البيانات الشخصية'
                ],
                'slug' => [
                    'tr' => 'kisisel-verilerin-islenmesi-politikasi',
                    'en' => 'personal-data-processing-policy',
                    'ar' => 'سياسة-معالجة-البيانات-الشخصية'
                ],
                'body' => [
                    'tr' => '<h1>Kişisel Verilerin İşlenmesi Politikası</h1><p>Kişisel verilerinizin işlenmesi ile ilgili bilgiler.</p>',
                    'en' => '<h1>Personal Data Processing Policy</h1><p>Information about processing your personal data.</p>',
                    'ar' => '<h1>سياسة معالجة البيانات الشخصية</h1><p>معلومات حول معالجة بياناتك الشخصية.</p>'
                ],
                'metakey' => [
                    'tr' => 'kişisel veri, KVKK, gizlilik, politika',
                    'en' => 'personal data, GDPR, privacy, policy',
                    'ar' => 'البيانات الشخصية، الخصوصية، سياسة'
                ],
                'metadesc' => [
                    'tr' => 'Kişisel verilerinizin korunması politikamız',
                    'en' => 'Our personal data protection policy',
                    'ar' => 'سياسة حماية البيانات الشخصية الخاصة بنا'
                ],
                'is_homepage' => false,
            ],
            [
                'title' => [
                    'tr' => 'Hakkımızda',
                    'en' => 'About Us',
                    'ar' => 'من نحن'
                ],
                'slug' => [
                    'tr' => 'hakkimizda',
                    'en' => 'about-us',
                    'ar' => 'من-نحن'
                ],
                'body' => [
                    'tr' => '<div class="about-content">
                        <h1>Hakkımızda</h1>
                        <p class="lead">Turkbil Bee, teknoloji dünyasında yenilikçi çözümler üreten dinamik bir ekiptir.</p>
                        
                        <h2>Misyonumuz</h2>
                        <p>Dijital çağın gereksinimlerini karşılayan, kullanıcı dostu ve güvenli web uygulamaları geliştirmek. Müşterilerimizin dijital dönüşüm yolculuğunda onlara rehberlik etmek.</p>
                        
                        <h2>Vizyonumuz</h2>
                        <p>Türkiye\'nin öncü teknoloji şirketlerinden biri olmak ve global pazarda rekabet edebilir çözümler sunmak.</p>
                        
                        <h2>Değerlerimiz</h2>
                        <ul>
                            <li><strong>Yenilikçilik:</strong> Sürekli öğrenme ve gelişim</li>
                            <li><strong>Kalite:</strong> En yüksek standartlarda hizmet</li>
                            <li><strong>Güvenilirlik:</strong> Sözümüzün arkasında durma</li>
                            <li><strong>Müşteri Odaklılık:</strong> Her projede müşteri memnuniyeti</li>
                        </ul>
                    </div>',
                    'en' => '<div class="about-content">
                        <h1>About Us</h1>
                        <p class="lead">Turkbil Bee is a dynamic team that produces innovative solutions in the technology world.</p>
                        
                        <h2>Our Mission</h2>
                        <p>To develop user-friendly and secure web applications that meet the requirements of the digital age. To guide our customers in their digital transformation journey.</p>
                        
                        <h2>Our Vision</h2>
                        <p>To become one of Turkey\'s leading technology companies and offer solutions that can compete in the global market.</p>
                        
                        <h2>Our Values</h2>
                        <ul>
                            <li><strong>Innovation:</strong> Continuous learning and development</li>
                            <li><strong>Quality:</strong> Service at the highest standards</li>
                            <li><strong>Reliability:</strong> Standing behind our word</li>
                            <li><strong>Customer Focus:</strong> Customer satisfaction in every project</li>
                        </ul>
                    </div>',
                    'ar' => '<div class="about-content">
                        <h1>من نحن</h1>
                        <p class="lead">Turkbil Bee هو فريق ديناميكي ينتج حلولاً مبتكرة في عالم التكنولوجيا.</p>
                        
                        <h2>مهمتنا</h2>
                        <p>تطوير تطبيقات ويب سهلة الاستخدام وآمنة تلبي متطلبات العصر الرقمي. توجيه عملائنا في رحلة التحول الرقمي.</p>
                        
                        <h2>رؤيتنا</h2>
                        <p>أن نصبح إحدى شركات التكنولوجيا الرائدة في تركيا وأن نقدم حلولاً قادرة على المنافسة في السوق العالمي.</p>
                        
                        <h2>قيمنا</h2>
                        <ul>
                            <li><strong>الابتكار:</strong> التعلم والتطوير المستمر</li>
                            <li><strong>الجودة:</strong> خدمة بأعلى المعايير</li>
                            <li><strong>الموثوقية:</strong> الوقوف وراء كلمتنا</li>
                            <li><strong>التركيز على العميل:</strong> رضا العميل في كل مشروع</li>
                        </ul>
                    </div>'
                ],
                'metakey' => [
                    'tr' => 'hakkımızda, şirket, ekip, misyon',
                    'en' => 'about us, company, team, mission',
                    'ar' => 'من نحن، شركة، فريق، مهمة'
                ],
                'metadesc' => [
                    'tr' => 'Şirketimiz ve ekibimiz hakkında bilgiler',
                    'en' => 'Information about our company and team',
                    'ar' => 'معلومات حول شركتنا وفريقنا'
                ],
                'is_homepage' => false,
            ],
            [
                'title' => [
                    'tr' => 'İletişim',
                    'en' => 'Contact',
                    'ar' => 'اتصل بنا'
                ],
                'slug' => [
                    'tr' => 'iletisim',
                    'en' => 'contact',
                    'ar' => 'اتصل-بنا'
                ],
                'body' => [
                    'tr' => '<div class="contact-content">
                        <h1>İletişim</h1>
                        <p class="lead">Bizimle iletişime geçmek için aşağıdaki bilgileri kullanabilirsiniz.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h3>📧 E-posta</h3>
                                <p><a href="mailto:info@turkbilbee.com">info@turkbilbee.com</a></p>
                                
                                <h3>📞 Telefon</h3>
                                <p><a href="tel:+902123456789">+90 (212) 345 67 89</a></p>
                                
                                <h3>📍 Adres</h3>
                                <p>Teknoloji Caddesi No: 123<br>
                                Şişli / İstanbul<br>
                                Türkiye</p>
                            </div>
                            <div class="col-md-6">
                                <h3>🕒 Çalışma Saatleri</h3>
                                <p><strong>Pazartesi - Cuma:</strong> 09:00 - 18:00<br>
                                <strong>Cumartesi:</strong> 10:00 - 16:00<br>
                                <strong>Pazar:</strong> Kapalı</p>
                                
                                <h3>🌐 Sosyal Medya</h3>
                                <p>
                                    <a href="#" class="me-3">LinkedIn</a>
                                    <a href="#" class="me-3">Twitter</a>
                                    <a href="#">GitHub</a>
                                </p>
                            </div>
                        </div>
                    </div>',
                    'en' => '<div class="contact-content">
                        <h1>Contact</h1>
                        <p class="lead">You can use the information below to contact us.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h3>📧 Email</h3>
                                <p><a href="mailto:info@turkbilbee.com">info@turkbilbee.com</a></p>
                                
                                <h3>📞 Phone</h3>
                                <p><a href="tel:+902123456789">+90 (212) 345 67 89</a></p>
                                
                                <h3>📍 Address</h3>
                                <p>Technology Street No: 123<br>
                                Şişli / Istanbul<br>
                                Turkey</p>
                            </div>
                            <div class="col-md-6">
                                <h3>🕒 Working Hours</h3>
                                <p><strong>Monday - Friday:</strong> 09:00 - 18:00<br>
                                <strong>Saturday:</strong> 10:00 - 16:00<br>
                                <strong>Sunday:</strong> Closed</p>
                                
                                <h3>🌐 Social Media</h3>
                                <p>
                                    <a href="#" class="me-3">LinkedIn</a>
                                    <a href="#" class="me-3">Twitter</a>
                                    <a href="#">GitHub</a>
                                </p>
                            </div>
                        </div>
                    </div>',
                    'ar' => '<div class="contact-content">
                        <h1>اتصل بنا</h1>
                        <p class="lead">يمكنكم استخدام المعلومات أدناه للاتصال بنا.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h3>📧 البريد الإلكتروني</h3>
                                <p><a href="mailto:info@turkbilbee.com">info@turkbilbee.com</a></p>
                                
                                <h3>📞 الهاتف</h3>
                                <p><a href="tel:+902123456789">+90 (212) 345 67 89</a></p>
                                
                                <h3>📍 العنوان</h3>
                                <p>شارع التكنولوجيا رقم: 123<br>
                                شيشلي / اسطنبول<br>
                                تركيا</p>
                            </div>
                            <div class="col-md-6">
                                <h3>🕒 ساعات العمل</h3>
                                <p><strong>الاثنين - الجمعة:</strong> 09:00 - 18:00<br>
                                <strong>السبت:</strong> 10:00 - 16:00<br>
                                <strong>الأحد:</strong> مغلق</p>
                                
                                <h3>🌐 وسائل التواصل الاجتماعي</h3>
                                <p>
                                    <a href="#" class="me-3">LinkedIn</a>
                                    <a href="#" class="me-3">Twitter</a>
                                    <a href="#">GitHub</a>
                                </p>
                            </div>
                        </div>
                    </div>'
                ],
                'metakey' => [
                    'tr' => 'iletişim, telefon, email, adres',
                    'en' => 'contact, phone, email, address',
                    'ar' => 'اتصال، هاتف، بريد إلكتروني، عنوان'
                ],
                'metadesc' => [
                    'tr' => 'Bizimle iletişime geçmek için bilgiler',
                    'en' => 'Information to contact us',
                    'ar' => 'معلومات للاتصال بنا'
                ],
                'is_homepage' => false,
            ],
        ];

        foreach ($pages as $page) {
            Page::create([
                'title' => $page['title'],
                'slug' => $page['slug'],
                'body' => $page['body'],
                'css' => null,
                'js' => null,
                'metakey' => $page['metakey'],
                'metadesc' => $page['metadesc'],
                'is_active' => true,
                'is_homepage' => $page['is_homepage'],
            ]);
        }
    }
}