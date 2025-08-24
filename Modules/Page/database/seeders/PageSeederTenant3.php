<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Page Seeder for Tenant3
 * Languages: en, ar
 */
class PageSeederTenant3 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT3 pages (en, ar)...');
        
        // Duplicate kontrolü
        $existingCount = Page::count();
        if ($existingCount > 0) {
            $this->command->info("Pages already exist in TENANT3 database ({$existingCount} pages), skipping seeder...");
            return;
        }
        
        // Mevcut sayfaları sil (sadece boşsa)
        Page::truncate();
        
        
        $this->createHomepage();
        $this->createAboutPage();
        $this->createServicesPage();
        $this->createNewsPage();
        $this->createContactPage();
    }
    
    private function createHomepage(): void
    {
        $page = Page::create([
            'title' => [
                'en' => 'Tech Solutions - Innovation Hub', 
                'ar' => 'حلول تقنية - مركز الابتكار'
            ],
            'slug' => [
                'en' => 'homepage', 
                'ar' => 'الرئيسية'
            ],
            'body' => [
                'en' => '<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50">
                    <div class="container mx-auto px-4 py-8">
                        <div class="text-center mb-8">
                            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                                <span class="bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">Future Technology</span><br>
                                <span class="text-gray-800">Innovation Solutions</span>
                            </h1>
                            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                                Leading the digital transformation with cutting-edge AI, cloud computing, and innovative tech solutions.
                            </p>
                            <div class="flex gap-4 justify-center">
                                <button class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-8 py-3 rounded-full font-semibold">
                                    Explore Solutions
                                </button>
                                <button class="border-2 border-green-600 text-green-600 px-8 py-3 rounded-full font-semibold">
                                    Learn More
                                </button>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50" dir="rtl">
                    <div class="container mx-auto px-4 py-8">
                        <div class="text-center mb-8">
                            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                                <span class="bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">تقنيات المستقبل</span><br>
                                <span class="text-gray-800">حلول الابتكار</span>
                            </h1>
                            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                                نقود التحول الرقمي بأحدث تقنيات الذكاء الاصطناعي والحوسبة السحابية والحلول التقنية المبتكرة.
                            </p>
                            <div class="flex gap-4 justify-center">
                                <button class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-8 py-3 rounded-full font-semibold">
                                    استكشف الحلول
                                </button>
                                <button class="border-2 border-green-600 text-green-600 px-8 py-3 rounded-full font-semibold">
                                    تعلم المزيد
                                </button>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => true,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Tech Solutions - Leading Digital Innovation',
            'حلول تقنية - رائدة في الابتكار الرقمي',
            'Leading the digital transformation with cutting-edge AI, cloud computing, and innovative tech solutions.',
            'نقود التحول الرقمي بأحدث تقنيات الذكاء الاصطناعي والحوسبة السحابية والحلول التقنية المبتكرة.'
        );
    }
    
    private function createAboutPage(): void
    {
        $page = Page::create([
            'title' => [
                'en' => 'About Our Company',
                'ar' => 'عن شركتنا'
            ],
            'slug' => [
                'en' => 'about-us',
                'ar' => 'عنا'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">About Our Company</h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                                We are a global technology company specializing in AI solutions, cloud infrastructure, and digital transformation services since 2010.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-4 gap-8 text-center">
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">13+</h3>
                                <p class="text-gray-600">Years of Excellence</p>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">1000+</h3>
                                <p class="text-gray-600">Global Projects</p>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">50+</h3>
                                <p class="text-gray-600">Countries</p>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">24/7</h3>
                                <p class="text-gray-600">Global Support</p>
                            </div>
                        </div>
                        <div class="mt-16 grid md:grid-cols-2 gap-16 items-center">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Mission</h2>
                                <p class="text-gray-600">To democratize access to advanced technology and empower businesses worldwide through innovative AI and cloud solutions.</p>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Vision</h2>
                                <p class="text-gray-600">To be the global leader in sustainable technology solutions that drive positive change for humanity.</p>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">عن شركتنا</h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                                نحن شركة تقنية عالمية متخصصة في حلول الذكاء الاصطناعي والبنية التحتية السحابية وخدمات التحول الرقمي منذ عام 2010.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-4 gap-8 text-center">
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">+13</h3>
                                <p class="text-gray-600">سنة من التميز</p>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">+1000</h3>
                                <p class="text-gray-600">مشروع عالمي</p>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">+50</h3>
                                <p class="text-gray-600">دولة</p>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-green-600 mb-2">24/7</h3>
                                <p class="text-gray-600">دعم عالمي</p>
                            </div>
                        </div>
                        <div class="mt-16 grid md:grid-cols-2 gap-16 items-center">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">مهمتنا</h2>
                                <p class="text-gray-600">إضفاء الطابع الديمقراطي على الوصول إلى التكنولوجيا المتقدمة وتمكين الشركات في جميع أنحاء العالم من خلال حلول الذكاء الاصطناعي والسحابة المبتكرة.</p>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">رؤيتنا</h2>
                                <p class="text-gray-600">أن نكون الرائد العالمي في حلول التكنولوجيا المستدامة التي تدفع التغيير الإيجابي للإنسانية.</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'About Our Company - Global Tech Solutions',
            'عن شركتنا - حلول تقنية عالمية',
            'We are a global technology company specializing in AI solutions, cloud infrastructure, and digital transformation services since 2010.',
            'نحن شركة تقنية عالمية متخصصة في حلول الذكاء الاصطناعي والبنية التحتية السحابية وخدمات التحول الرقمي منذ عام 2010.'
        );
    }
    
    private function createServicesPage(): void
    {
        $page = Page::create([
            'title' => [
                'en' => 'Our Services',
                'ar' => 'خدماتنا'
            ],
            'slug' => [
                'en' => 'services',
                'ar' => 'خدماتنا'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Our Services</h1>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-green-500">
                            <div class="text-5xl mb-6 text-green-600">🤖</div>
                            <h3 class="text-2xl font-bold mb-4">Artificial Intelligence</h3>
                            <p class="text-gray-600 mb-4">Custom AI solutions including machine learning, natural language processing, and computer vision.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• Machine Learning Models</li>
                                <li>• Deep Learning Solutions</li>
                                <li>• AI Consulting</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-blue-500">
                            <div class="text-5xl mb-6 text-blue-600">☁️</div>
                            <h3 class="text-2xl font-bold mb-4">Cloud Computing</h3>
                            <p class="text-gray-600 mb-4">Scalable cloud infrastructure and migration services for businesses of all sizes.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• Cloud Migration</li>
                                <li>• Infrastructure Management</li>
                                <li>• DevOps Solutions</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-purple-500">
                            <div class="text-5xl mb-6 text-purple-600">🔒</div>
                            <h3 class="text-2xl font-bold mb-4">Cybersecurity</h3>
                            <p class="text-gray-600 mb-4">Comprehensive security solutions to protect your digital assets and data.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• Security Assessment</li>
                                <li>• Threat Monitoring</li>
                                <li>• Compliance Solutions</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-orange-500">
                            <div class="text-5xl mb-6 text-orange-600">📊</div>
                            <h3 class="text-2xl font-bold mb-4">Data Analytics</h3>
                            <p class="text-gray-600 mb-4">Transform your data into actionable insights with advanced analytics solutions.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• Business Intelligence</li>
                                <li>• Predictive Analytics</li>
                                <li>• Real-time Dashboards</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-red-500">
                            <div class="text-5xl mb-6 text-red-600">🌐</div>
                            <h3 class="text-2xl font-bold mb-4">Digital Transformation</h3>
                            <p class="text-gray-600 mb-4">End-to-end digital transformation strategies and implementation.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• Process Automation</li>
                                <li>• Digital Strategy</li>
                                <li>• Change Management</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-teal-500">
                            <div class="text-5xl mb-6 text-teal-600">🛠️</div>
                            <h3 class="text-2xl font-bold mb-4">Technical Support</h3>
                            <p class="text-gray-600 mb-4">24/7 technical support and maintenance services for all our solutions.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• 24/7 Support</li>
                                <li>• System Maintenance</li>
                                <li>• Training Programs</li>
                            </ul>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">خدماتنا</h1>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-green-500">
                            <div class="text-5xl mb-6 text-green-600">🤖</div>
                            <h3 class="text-2xl font-bold mb-4">الذكاء الاصطناعي</h3>
                            <p class="text-gray-600 mb-4">حلول ذكاء اصطناعي مخصصة تشمل تعلم الآلة ومعالجة اللغة الطبيعية ورؤية الكمبيوتر.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• نماذج تعلم الآلة</li>
                                <li>• حلول التعلم العميق</li>
                                <li>• استشارات الذكاء الاصطناعي</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-blue-500">
                            <div class="text-5xl mb-6 text-blue-600">☁️</div>
                            <h3 class="text-2xl font-bold mb-4">الحوسبة السحابية</h3>
                            <p class="text-gray-600 mb-4">بنية تحتية سحابية قابلة للتطوير وخدمات الهجرة للشركات من جميع الأحجام.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• هجرة السحابة</li>
                                <li>• إدارة البنية التحتية</li>
                                <li>• حلول DevOps</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-purple-500">
                            <div class="text-5xl mb-6 text-purple-600">🔒</div>
                            <h3 class="text-2xl font-bold mb-4">الأمن السيبراني</h3>
                            <p class="text-gray-600 mb-4">حلول أمنية شاملة لحماية أصولك الرقمية وبياناتك.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• تقييم الأمن</li>
                                <li>• مراقبة التهديدات</li>
                                <li>• حلول الامتثال</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-orange-500">
                            <div class="text-5xl mb-6 text-orange-600">📊</div>
                            <h3 class="text-2xl font-bold mb-4">تحليل البيانات</h3>
                            <p class="text-gray-600 mb-4">حول بياناتك إلى رؤى قابلة للتنفيذ مع حلول التحليلات المتقدمة.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• ذكاء الأعمال</li>
                                <li>• التحليلات التنبؤية</li>
                                <li>• لوحات المعلومات الفورية</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-red-500">
                            <div class="text-5xl mb-6 text-red-600">🌐</div>
                            <h3 class="text-2xl font-bold mb-4">التحول الرقمي</h3>
                            <p class="text-gray-600 mb-4">استراتيجيات وتنفيذ شامل للتحول الرقمي من البداية إلى النهاية.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• أتمتة العمليات</li>
                                <li>• الاستراتيجية الرقمية</li>
                                <li>• إدارة التغيير</li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow border-t-4 border-teal-500">
                            <div class="text-5xl mb-6 text-teal-600">🛠️</div>
                            <h3 class="text-2xl font-bold mb-4">الدعم التقني</h3>
                            <p class="text-gray-600 mb-4">خدمات الدعم التقني والصيانة على مدار الساعة لجميع حلولنا.</p>
                            <ul class="text-sm text-gray-500">
                                <li>• دعم 24/7</li>
                                <li>• صيانة النظام</li>
                                <li>• برامج التدريب</li>
                            </ul>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Our Services - AI, Cloud & Digital Solutions',
            'خدماتنا - الذكاء الاصطناعي والسحابة والحلول الرقمية',
            'Comprehensive technology services including AI, cloud computing, cybersecurity, and digital transformation solutions.',
            'خدمات تقنية شاملة تشمل الذكاء الاصطناعي والحوسبة السحابية والأمن السيبراني وحلول التحول الرقمي.'
        );
    }
    
    private function createNewsPage(): void
    {
        $page = Page::create([
            'title' => [
                'en' => 'Latest News',
                'ar' => 'أحدث الأخبار'
            ],
            'slug' => [
                'en' => 'news',
                'ar' => 'الأخبار'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Latest News</h1>
                    <div class="max-w-6xl mx-auto">
                        <p class="text-xl text-gray-600 text-center mb-12">
                            Stay updated with the latest developments in technology, AI innovations, and industry insights.
                        </p>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="bg-green-500 h-2"></div>
                                <div class="p-6">
                                    <span class="text-sm text-gray-500 uppercase tracking-wide">AI Innovation</span>
                                    <h2 class="text-xl font-bold mt-2 mb-4">New AI Model Achieves 99% Accuracy</h2>
                                    <p class="text-gray-600 mb-4">Our latest machine learning model has achieved unprecedented accuracy in natural language processing tasks, setting new industry standards.</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">March 15, 2024</span>
                                        <a href="#" class="text-green-600 font-semibold hover:text-green-800">Read More →</a>
                                    </div>
                                </div>
                            </article>
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="bg-blue-500 h-2"></div>
                                <div class="p-6">
                                    <span class="text-sm text-gray-500 uppercase tracking-wide">Cloud Computing</span>
                                    <h2 class="text-xl font-bold mt-2 mb-4">Global Cloud Infrastructure Expansion</h2>
                                    <p class="text-gray-600 mb-4">We\'re expanding our cloud infrastructure to 10 new regions, bringing faster and more reliable services to millions of users worldwide.</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">March 10, 2024</span>
                                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
                                    </div>
                                </div>
                            </article>
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="bg-purple-500 h-2"></div>
                                <div class="p-6">
                                    <span class="text-sm text-gray-500 uppercase tracking-wide">Partnership</span>
                                    <h2 class="text-xl font-bold mt-2 mb-4">Strategic Partnership Announcement</h2>
                                    <p class="text-gray-600 mb-4">We\'ve partnered with leading universities to advance AI research and develop next-generation solutions for complex global challenges.</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">March 5, 2024</span>
                                        <a href="#" class="text-purple-600 font-semibold hover:text-purple-800">Read More →</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">أحدث الأخبار</h1>
                    <div class="max-w-6xl mx-auto">
                        <p class="text-xl text-gray-600 text-center mb-12">
                            ابق على اطلاع على آخر التطورات في التكنولوجيا وابتكارات الذكاء الاصطناعي ورؤى الصناعة.
                        </p>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="bg-green-500 h-2"></div>
                                <div class="p-6">
                                    <span class="text-sm text-gray-500 uppercase tracking-wide">ابتكار الذكاء الاصطناعي</span>
                                    <h2 class="text-xl font-bold mt-2 mb-4">نموذج ذكاء اصطناعي جديد يحقق دقة 99%</h2>
                                    <p class="text-gray-600 mb-4">حقق أحدث نماذج تعلم الآلة لدينا دقة غير مسبوقة في مهام معالجة اللغة الطبيعية، مما وضع معايير جديدة في الصناعة.</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">15 مارس 2024</span>
                                        <a href="#" class="text-green-600 font-semibold hover:text-green-800">اقرأ المزيد ←</a>
                                    </div>
                                </div>
                            </article>
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="bg-blue-500 h-2"></div>
                                <div class="p-6">
                                    <span class="text-sm text-gray-500 uppercase tracking-wide">الحوسبة السحابية</span>
                                    <h2 class="text-xl font-bold mt-2 mb-4">توسع البنية التحتية السحابية العالمية</h2>
                                    <p class="text-gray-600 mb-4">نقوم بتوسيع البنية التحتية السحابية لدينا إلى 10 مناطق جديدة، مما يوفر خدمات أسرع وأكثر موثوقية لملايين المستخدمين في جميع أنحاء العالم.</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">10 مارس 2024</span>
                                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">اقرأ المزيد ←</a>
                                    </div>
                                </div>
                            </article>
                            <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="bg-purple-500 h-2"></div>
                                <div class="p-6">
                                    <span class="text-sm text-gray-500 uppercase tracking-wide">شراكة</span>
                                    <h2 class="text-xl font-bold mt-2 mb-4">إعلان شراكة استراتيجية</h2>
                                    <p class="text-gray-600 mb-4">تشاركنا مع الجامعات الرائدة لتقدم بحوث الذكاء الاصطناعي وتطوير حلول الجيل القادم للتحديات العالمية المعقدة.</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">5 مارس 2024</span>
                                        <a href="#" class="text-purple-600 font-semibold hover:text-purple-800">اقرأ المزيد ←</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Latest News - Technology & AI Innovations',
            'أحدث الأخبار - التكنولوجيا وابتكارات الذكاء الاصطناعي',
            'Stay updated with the latest developments in technology, AI innovations, and industry insights.',
            'ابق على اطلاع على آخر التطورات في التكنولوجيا وابتكارات الذكاء الاصطناعي ورؤى الصناعة.'
        );
    }
    
    private function createContactPage(): void
    {
        $page = Page::create([
            'title' => [
                'en' => 'Contact Us',
                'ar' => 'اتصل بنا'
            ],
            'slug' => [
                'en' => 'contact',
                'ar' => 'اتصل-بنا'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">Contact Us</h1>
                    <div class="max-w-6xl mx-auto">
                        <div class="grid lg:grid-cols-3 gap-12">
                            <!-- Contact Information -->
                            <div class="lg:col-span-1">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6">Get in Touch</h2>
                                <p class="text-gray-600 mb-8">
                                    Ready to transform your business with cutting-edge technology? Contact our expert team today.
                                </p>
                                
                                <div class="space-y-6">
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 mt-1">
                                            <span class="text-green-600 text-xl">🌍</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">Global Headquarters</h3>
                                            <p class="text-gray-600">San Francisco, CA, USA</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 mt-1">
                                            <span class="text-green-600 text-xl">📧</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">Email</h3>
                                            <p class="text-gray-600">contact@techsolutions.com</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 mt-1">
                                            <span class="text-green-600 text-xl">📱</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">Phone</h3>
                                            <p class="text-gray-600">+1 (555) 123-4567</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 mt-1">
                                            <span class="text-green-600 text-xl">🕐</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">Business Hours</h3>
                                            <p class="text-gray-600">24/7 Global Support</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact Form -->
                            <div class="lg:col-span-2">
                                <div class="bg-white rounded-2xl shadow-xl p-8">
                                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Start Your Project</h2>
                                    <p class="text-gray-600 mb-8">
                                        Tell us about your project and let our experts provide you with the best technology solutions.
                                    </p>
                                    
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <button class="bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            🚀 AI Consultation
                                        </button>
                                        <button class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            ☁️ Cloud Solutions
                                        </button>
                                        <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            🔒 Security Audit
                                        </button>
                                        <button class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            📊 Data Analytics
                                        </button>
                                    </div>
                                    
                                    <div class="mt-8 p-6 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-4">
                                            <strong>Enterprise Clients:</strong> Schedule a personalized consultation with our solution architects.
                                        </p>
                                        <button class="bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                                            Schedule Enterprise Consultation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>',
                'ar' => '<div class="container mx-auto px-4 py-16" dir="rtl">
                    <h1 class="text-5xl font-bold text-center text-gray-800 mb-16">اتصل بنا</h1>
                    <div class="max-w-6xl mx-auto">
                        <div class="grid lg:grid-cols-3 gap-12">
                            <!-- معلومات الاتصال -->
                            <div class="lg:col-span-1">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6">تواصل معنا</h2>
                                <p class="text-gray-600 mb-8">
                                    مستعد لتحويل عملك بأحدث التقنيات؟ تواصل مع فريق الخبراء لدينا اليوم.
                                </p>
                                
                                <div class="space-y-6">
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center ml-4 mt-1">
                                            <span class="text-green-600 text-xl">🌍</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">المقر الرئيسي العالمي</h3>
                                            <p class="text-gray-600">سان فرانسيسكو، كاليفورنيا، الولايات المتحدة</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center ml-4 mt-1">
                                            <span class="text-green-600 text-xl">📧</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">البريد الإلكتروني</h3>
                                            <p class="text-gray-600">contact@techsolutions.com</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center ml-4 mt-1">
                                            <span class="text-green-600 text-xl">📱</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">الهاتف</h3>
                                            <p class="text-gray-600">+1 (555) 123-4567</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center ml-4 mt-1">
                                            <span class="text-green-600 text-xl">🕐</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">ساعات العمل</h3>
                                            <p class="text-gray-600">دعم عالمي 24/7</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- نموذج الاتصال -->
                            <div class="lg:col-span-2">
                                <div class="bg-white rounded-2xl shadow-xl p-8">
                                    <h2 class="text-2xl font-bold text-gray-800 mb-6">ابدأ مشروعك</h2>
                                    <p class="text-gray-600 mb-8">
                                        أخبرنا عن مشروعك ودع خبراءنا يقدمون لك أفضل الحلول التقنية.
                                    </p>
                                    
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <button class="bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            🚀 استشارة الذكاء الاصطناعي
                                        </button>
                                        <button class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            ☁️ الحلول السحابية
                                        </button>
                                        <button class="bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            🔒 مراجعة الأمان
                                        </button>
                                        <button class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 px-6 rounded-lg font-semibold hover:shadow-lg transition-all">
                                            📊 تحليل البيانات
                                        </button>
                                    </div>
                                    
                                    <div class="mt-8 p-6 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600 mb-4">
                                            <strong>العملاء المؤسسيون:</strong> حدد موعداً لاستشارة شخصية مع مهندسي الحلول لدينا.
                                        </p>
                                        <button class="bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                                            جدولة استشارة مؤسسية
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_homepage' => false,
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $page,
            'Contact Us - Global Tech Solutions',
            'اتصل بنا - حلول تقنية عالمية',
            'Ready to transform your business with cutting-edge technology? Contact our expert team for AI, cloud, and digital solutions.',
            'مستعد لتحويل عملك بأحدث التقنيات؟ تواصل مع فريق الخبراء لدينا للحصول على حلول الذكاء الاصطناعي والسحابة والحلول الرقمية.'
        );
    }

    private function createSeoSetting($page, $titleEn, $titleAr, $descriptionEn, $descriptionAr): void
    {
        // Eğer bu sayfa için zaten SEO ayarı varsa oluşturma
        if ($page->seoSetting()->exists()) {
            return;
        }
        
        $page->seoSetting()->create([
            'titles' => [
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'descriptions' => [
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'robots_meta' => ['index' => true, 'follow' => true, 'archive' => true],
            'og_titles' => [
                'en' => $titleEn,
                'ar' => $titleAr
            ],
            'og_descriptions' => [
                'en' => $descriptionEn,
                'ar' => $descriptionAr
            ],
            'og_type' => 'website',
            'twitter_card' => 'summary',
            'seo_score' => rand(80, 95),
        ]);
    }
}