<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use App\Models\SeoSetting;
use Faker\Factory as Faker;
use App\Helpers\TenantHelpers;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder hem central hem tenant'ta çalışabilir
        if (TenantHelpers::isCentral()) {
            $this->command->info('PageSeeder central veritabanında çalışıyor...');
        } else {
            $this->command->info('PageSeeder tenant veritabanında çalışıyor...');
        }
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
                'seo' => [
                    'tr' => [
                        'meta_title' => 'Turkbil Bee - Anasayfa | Modern Web Çözümleri',
                        'meta_description' => 'Turkbil Bee ile dijital dünyanın geleceğini keşfedin. Laravel 11, güvenlik odaklı geliştirme ve responsive tasarım ile güçlü web çözümleri sunuyoruz.',
                        'keywords' => ['anasayfa', 'web tasarım', 'Laravel', 'modern teknoloji', 'dijital çözümler', 'web geliştirme', 'Turkbil Bee'],
                        'og_title' => 'Turkbil Bee - Modern Web Teknolojileri ve Dijital Çözümler',
                        'og_description' => 'Hızlı geliştirme, güvenlik odaklı yaklaşım ve responsive tasarım ile dijital geleceği inşa ediyoruz.',
                        'robots' => 'index,follow'
                    ],
                    'en' => [
                        'meta_title' => 'Turkbil Bee - Homepage | Modern Web Solutions', 
                        'meta_description' => 'Discover the future of the digital world with Turkbil Bee. We offer powerful web solutions with Laravel 11, security-focused development and responsive design.',
                        'keywords' => ['homepage', 'web design', 'Laravel', 'modern technology', 'digital solutions', 'web development', 'Turkbil Bee'],
                        'og_title' => 'Turkbil Bee - Modern Web Technologies and Digital Solutions',
                        'og_description' => 'Building the digital future with fast development, security-focused approach and responsive design.',
                        'robots' => 'index,follow'
                    ],
                    'ar' => [
                        'meta_title' => 'Turkbil Bee - الصفحة الرئيسية | حلول الويب الحديثة',
                        'meta_description' => 'اكتشف مستقبل العالم الرقمي مع تورك بيل بي. نقدم حلول ويب قوية باستخدام Laravel 11 والتطوير المركز على الأمان والتصميم المتجاوب.',
                        'keywords' => ['الصفحة الرئيسية', 'تصميم الويب', 'Laravel', 'التكنولوجيا الحديثة', 'الحلول الرقمية', 'تطوير الويب', 'Turkbil Bee'],
                        'og_title' => 'Turkbil Bee - تقنيات الويب الحديثة والحلول الرقمية',
                        'og_description' => 'بناء المستقبل الرقمي بالتطوير السريع والنهج المركز على الأمان والتصميم المتجاوب.',
                        'robots' => 'index,follow'
                    ]
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
                'seo' => [
                    'tr' => [
                        'meta_title' => 'Çerez Politikası: Web Deneyiminizi İyileştirmek İçin - Turkbil Bee',
                        'meta_description' => 'Turkbil Bee çerez politikası hakkında detaylı bilgi edinin. Web sitemizde kullandığımız çerezler ve gizlilik haklarınız hakkında şeffaf bilgilendirme.',
                        'keywords' => ['çerez politikası', 'cookie policy', 'web çerezleri', 'gizlilik hakları', 'veri toplama', 'web deneyimi', 'çerez yönetimi'],
                        'og_title' => 'Şeffaf Çerez Politikamız | Turkbil Bee',
                        'og_description' => 'Web deneyiminizi iyileştirmek için kullandığımız çerezler hakkında şeffaf bilgilendirme.',
                        'robots' => 'index,follow'
                    ],
                    'en' => [
                        'meta_title' => 'Cookie Policy: Improving Your Web Experience - Turkbil Bee',
                        'meta_description' => 'Learn about Turkbil Bee\'s cookie policy. Transparent information about cookies we use on our website and your privacy rights.',
                        'keywords' => ['cookie policy', 'web cookies', 'privacy rights', 'data collection', 'web experience', 'cookie management', 'transparency'],
                        'og_title' => 'Our Transparent Cookie Policy | Turkbil Bee',
                        'og_description' => 'Transparent information about cookies we use to improve your web experience.',
                        'robots' => 'index,follow'
                    ],
                    'ar' => [
                        'meta_title' => 'سياسة ملفات تعريف الارتباط: تحسين تجربة الويب الخاصة بك - Turkbil Bee',
                        'meta_description' => 'تعرف على سياسة ملفات تعريف الارتباط في تورك بيل بي. معلومات شفافة حول الكوكيز التي نستخدمها في موقعنا وحقوق الخصوصية الخاصة بك.',
                        'keywords' => ['سياسة الكوكيز', 'ملفات تعريف الارتباط', 'حقوق الخصوصية', 'جمع البيانات', 'تجربة الويب', 'إدارة الكوكيز', 'الشفافية'],
                        'og_title' => 'سياسة الكوكيز الشفافة | Turkbil Bee',
                        'og_description' => 'معلومات شفافة حول ملفات تعريف الارتباط التي نستخدمها لتحسين تجربة الويب.',
                        'robots' => 'index,follow'
                    ]
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
                'seo' => [
                    'tr' => [
                        'meta_title' => 'Kişisel Veri Politikası: Güvenliğiniz Bizim İçin Öncelik - Turkbil Bee',
                        'meta_description' => 'Türk Bilişim\'in kişisel verilerin işlenmesi politikası hakkında detaylı bilgi edinin. KVKK uyumlu güvenli veri işleme süreçlerimizle kişisel verilerinizi koruyoruz.',
                        'keywords' => ['kişisel veriler', 'veri işleme', 'kişisel veri politikası', 'veri koruma', 'gizlilik politikası', 'KVKK', 'kişisel verilerin korunması'],
                        'og_title' => 'Güvenli Veri İşleme Politikamız | Turkbil Bee',
                        'og_description' => 'KVKK uyumlu kişisel veri işleme politikamızla verilerinizi güvende tutuyoruz.',
                        'robots' => 'index,follow'
                    ],
                    'en' => [
                        'meta_title' => 'Personal Data Policy: Your Security is Our Priority - Turkbil Bee',
                        'meta_description' => 'Learn about Turkbil\'s personal data processing policy. We protect your personal data with GDPR-compliant secure data processing procedures.',
                        'keywords' => ['personal data', 'data processing', 'personal data policy', 'data protection', 'privacy policy', 'GDPR', 'data security'],
                        'og_title' => 'Secure Data Processing Policy | Turkbil Bee',
                        'og_description' => 'We keep your data safe with our GDPR-compliant personal data processing policy.',
                        'robots' => 'index,follow'
                    ],
                    'ar' => [
                        'meta_title' => 'سياسة البيانات الشخصية: أمانكم أولويتنا - Turkbil Bee',
                        'meta_description' => 'تعرف على سياسة معالجة البيانات الشخصية في تورك بيليشيم. نحمي بياناتكم الشخصية بإجراءات معالجة آمنة متوافقة مع قوانين حماية البيانات.',
                        'keywords' => ['البيانات الشخصية', 'معالجة البيانات', 'سياسة البيانات الشخصية', 'حماية البيانات', 'سياسة الخصوصية', 'أمان البيانات'],
                        'og_title' => 'سياسة معالجة البيانات الآمنة | Turkbil Bee',
                        'og_description' => 'نحافظ على بياناتكم بأمان من خلال سياسة معالجة البيانات الشخصية المتوافقة مع القوانين.',
                        'robots' => 'index,follow'
                    ]
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
                'seo' => [
                    'tr' => [
                        'meta_title' => 'Hakkımızda: Teknoloji Dünyasında Yenilikçi Çözümler - Turkbil Bee',
                        'meta_description' => 'Turkbil Bee ekibi hakkında bilgi edinin. Misyonumuz, vizyonumuz ve değerlerimizle dijital dönüşüm yolculuğunda müşterilerimize rehberlik ediyoruz.',
                        'keywords' => ['hakkımızda', 'Turkbil Bee', 'teknoloji şirketi', 'dijital dönüşüm', 'yenilikçilik', 'kalite', 'güvenilirlik', 'müşteri odaklılık'],
                        'og_title' => 'Turkbil Bee: Yenilikçi Teknoloji Çözümleri',
                        'og_description' => 'Teknoloji dünyasında yenilikçi çözümler üreten dinamik ekibimizle tanışın.',
                        'robots' => 'index,follow'
                    ],
                    'en' => [
                        'meta_title' => 'About Us: Innovative Solutions in Technology World - Turkbil Bee',
                        'meta_description' => 'Learn about Turkbil Bee team. With our mission, vision and values, we guide our customers in their digital transformation journey.',
                        'keywords' => ['about us', 'Turkbil Bee', 'technology company', 'digital transformation', 'innovation', 'quality', 'reliability', 'customer focus'],
                        'og_title' => 'Turkbil Bee: Innovative Technology Solutions',
                        'og_description' => 'Meet our dynamic team that produces innovative solutions in the technology world.',
                        'robots' => 'index,follow'
                    ],
                    'ar' => [
                        'meta_title' => 'من نحن: حلول مبتكرة في عالم التكنولوجيا - Turkbil Bee',
                        'meta_description' => 'تعرف على فريق تورك بيل بي. برسالتنا ورؤيتنا وقيمنا، نوجه عملاءنا في رحلة التحول الرقمي.',
                        'keywords' => ['من نحن', 'Turkbil Bee', 'شركة تكنولوجيا', 'التحول الرقمي', 'الابتكار', 'الجودة', 'الموثوقية', 'التركيز على العميل'],
                        'og_title' => 'Turkbil Bee: حلول تكنولوجية مبتكرة',
                        'og_description' => 'تعرف على فريقنا الديناميكي الذي ينتج حلولاً مبتكرة في عالم التكنولوجيا.',
                        'robots' => 'index,follow'
                    ]
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
                'seo' => [
                    'tr' => [
                        'meta_title' => 'İletişim: Projeleriniz İçin Bizimle İletişime Geçin - Turkbil Bee',
                        'meta_description' => 'Turkbil Bee ile iletişime geçin. Web tasarım, dijital çözümler ve teknoloji projeleri için detaylı bilgi alın. İstanbul ofisimiz ve iletişim bilgilerimiz.',
                        'keywords' => ['iletişim', 'proje danışmanlığı', 'web tasarım teklifi', 'İstanbul ofis', 'teknoloji projeleri', 'dijital çözüm', 'teklif al'],
                        'og_title' => 'Projeleriniz İçin Bizimle İletişime Geçin | Turkbil Bee',
                        'og_description' => 'Web tasarım ve dijital çözüm projeleriniz için ücretsiz danışmanlık alın.',
                        'robots' => 'index,follow'
                    ],
                    'en' => [
                        'meta_title' => 'Contact: Get in Touch for Your Projects - Turkbil Bee',
                        'meta_description' => 'Contact Turkbil Bee. Get detailed information for web design, digital solutions and technology projects. Our Istanbul office and contact information.',
                        'keywords' => ['contact', 'project consultation', 'web design quote', 'Istanbul office', 'technology projects', 'digital solution', 'get quote'],
                        'og_title' => 'Get in Touch for Your Projects | Turkbil Bee',
                        'og_description' => 'Get free consultation for your web design and digital solution projects.',
                        'robots' => 'index,follow'
                    ],
                    'ar' => [
                        'meta_title' => 'اتصل بنا: تواصل معنا لمشاريعك - Turkbil Bee',
                        'meta_description' => 'تواصل مع تورك بيل بي. احصل على معلومات مفصلة لتصميم الويب والحلول الرقمية ومشاريع التكنولوجيا. مكتبنا في اسطنبول ومعلومات الاتصال.',
                        'keywords' => ['اتصال', 'استشارة المشاريع', 'عرض أسعار تصميم الويب', 'مكتب اسطنبول', 'مشاريع التكنولوجيا', 'حل رقمي', 'احصل على عرض'],
                        'og_title' => 'تواصل معنا لمشاريعك | Turkbil Bee',
                        'og_description' => 'احصل على استشارة مجانية لمشاريع تصميم الويب والحلول الرقمية.',
                        'robots' => 'index,follow'
                    ]
                ],
                'is_homepage' => false,
            ],
        ];

        foreach ($pages as $pageData) {
            // Force recreate - duplicate check yapma
            $existingPage = Page::where('title->tr', $pageData['title']['tr'])->first();
            
            if ($existingPage) {
                $this->command->info('Force updating existing page: ' . $pageData['title']['tr']);
                
                // Page'i güncelle
                $existingPage->update([
                    'title' => $pageData['title'],
                    'slug' => $pageData['slug'],
                    'body' => $pageData['body'],
                    'is_active' => true,
                    'is_homepage' => $pageData['is_homepage'],
                ]);
                
                $page = $existingPage;
            } else {
                $page = Page::create([
                    'title' => $pageData['title'],
                    'slug' => $pageData['slug'],
                    'body' => $pageData['body'],
                    'css' => null,
                    'js' => null,
                    'is_active' => true,
                    'is_homepage' => $pageData['is_homepage'],
                ]);
            }

            // SEO Settings force recreate
            $existingSeo = $page->seoSetting;
            if ($existingSeo) {
                $this->command->info('Force deleting and recreating SEO for page: ' . $pageData['title']['tr']);
                $existingSeo->delete();
            }
            
            // SEO Settings oluştur - trilingual format
            $page->seoSetting()->create([
                'titles' => [
                    'tr' => $pageData['seo']['tr']['meta_title'],
                    'en' => $pageData['seo']['en']['meta_title'],
                    'ar' => $pageData['seo']['ar']['meta_title']
                ],
                'descriptions' => [
                    'tr' => $pageData['seo']['tr']['meta_description'],
                    'en' => $pageData['seo']['en']['meta_description'],
                    'ar' => $pageData['seo']['ar']['meta_description']
                ],
                'keywords' => [
                    'tr' => $pageData['seo']['tr']['keywords'],
                    'en' => $pageData['seo']['en']['keywords'],
                    'ar' => $pageData['seo']['ar']['keywords']
                ],
                'focus_keyword' => $pageData['seo']['tr']['keywords'][0] ?? '',
                'focus_keywords' => [
                    'tr' => $pageData['seo']['tr']['keywords'][0] ?? '',
                    'en' => $pageData['seo']['en']['keywords'][0] ?? '',
                    'ar' => $pageData['seo']['ar']['keywords'][0] ?? ''
                ],
                'canonical_url' => '',
                'robots_meta' => [
                    'index' => true,
                    'follow' => true,
                    'archive' => true
                ],
                'og_title' => [
                    'tr' => $pageData['seo']['tr']['og_title'] ?? $pageData['seo']['tr']['meta_title'],
                    'en' => $pageData['seo']['en']['og_title'] ?? $pageData['seo']['en']['meta_title'],
                    'ar' => $pageData['seo']['ar']['og_title'] ?? $pageData['seo']['ar']['meta_title']
                ],
                'og_description' => [
                    'tr' => $pageData['seo']['tr']['og_description'] ?? $pageData['seo']['tr']['meta_description'],
                    'en' => $pageData['seo']['en']['og_description'] ?? $pageData['seo']['en']['meta_description'],
                    'ar' => $pageData['seo']['ar']['og_description'] ?? $pageData['seo']['ar']['meta_description']
                ],
                'og_image' => '',
                'og_type' => 'website',
                'twitter_card' => 'summary',
                'seo_score' => rand(75, 95),
            ]);
        }
    }
}