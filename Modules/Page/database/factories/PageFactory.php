<?php

namespace Modules\Page\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Str;

/**
 * Page Factory
 *
 * Gerçekçi test verileri oluşturur.
 * Çoklu dil desteği ve SEO ayarları içerir.
 *
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titleTr = $this->faker->sentence(3);
        $titleEn = $this->faker->sentence(3);

        $slugTr = Str::slug($titleTr);
        $slugEn = Str::slug($titleEn);

        // Gerçekçi içerikler
        $contentTr = '<h2>' . $this->faker->sentence() . '</h2>' .
                     '<p>' . $this->faker->paragraph(5) . '</p>' .
                     '<h3>' . $this->faker->sentence() . '</h3>' .
                     '<p>' . $this->faker->paragraph(3) . '</p>' .
                     '<ul>' .
                     '<li>' . $this->faker->sentence() . '</li>' .
                     '<li>' . $this->faker->sentence() . '</li>' .
                     '<li>' . $this->faker->sentence() . '</li>' .
                     '</ul>' .
                     '<p>' . $this->faker->paragraph(4) . '</p>';

        $contentEn = '<h2>' . $this->faker->sentence() . '</h2>' .
                     '<p>' . $this->faker->paragraph(5) . '</p>' .
                     '<h3>' . $this->faker->sentence() . '</h3>' .
                     '<p>' . $this->faker->paragraph(3) . '</p>' .
                     '<ul>' .
                     '<li>' . $this->faker->sentence() . '</li>' .
                     '<li>' . $this->faker->sentence() . '</li>' .
                     '<li>' . $this->faker->sentence() . '</li>' .
                     '</ul>' .
                     '<p>' . $this->faker->paragraph(4) . '</p>';

        return [
            'title' => [
                'tr' => $titleTr,
                'en' => $titleEn,
            ],
            'slug' => [
                'tr' => $slugTr,
                'en' => $slugEn,
            ],
            'body' => [
                'tr' => $contentTr,
                'en' => $contentEn,
            ],
            'css' => $this->faker->randomElement([
                null,
                '.custom-page { padding: 20px; }',
                '.page-header { color: #333; font-size: 24px; }',
                '.content-wrapper { max-width: 1200px; margin: 0 auto; }'
            ]),
            'js' => $this->faker->randomElement([
                null,
                'console.log("Page loaded");',
                'document.addEventListener("DOMContentLoaded", function() { console.log("Ready"); });'
            ]),
            'is_active' => $this->faker->boolean(80), // %80 aktif
            'is_homepage' => false, // Default olarak homepage değil
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Homepage state
     * Ana sayfa olarak işaretlenmiş sayfa
     */
    public function homepage(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_homepage' => true,
            'is_active' => true, // Homepage her zaman aktif olmalı
            'title' => [
                'tr' => 'Ana Sayfa',
                'en' => 'Home Page',
            ],
            'slug' => [
                'tr' => 'ana-sayfa',
                'en' => 'home',
            ],
        ]);
    }

    /**
     * Active state
     * Aktif sayfa
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Inactive state
     * Pasif sayfa
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * With custom CSS and JS
     * Özel CSS ve JS içeren sayfa
     */
    public function withCustomStyles(): static
    {
        return $this->state(fn (array $attributes) => [
            'css' => '
/* Custom Page Styles */
.page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 20px;
    text-align: center;
    margin-bottom: 40px;
    border-radius: 10px;
}
.page-content {
    font-size: 18px;
    line-height: 1.8;
    color: #333;
}
.page-content h2 {
    color: #667eea;
    margin-top: 40px;
    margin-bottom: 20px;
}
            ',
            'js' => '
// Custom Page JavaScript
document.addEventListener("DOMContentLoaded", function() {
    console.log("Custom page initialized");

    // Smooth scroll for anchor links
    document.querySelectorAll("a[href^=\"#\"]").forEach(anchor => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -100px 0px"
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("animated");
            }
        });
    }, observerOptions);

    document.querySelectorAll(".page-content > *").forEach(el => {
        observer.observe(el);
    });
});
            ',
        ]);
    }

    /**
     * About page state
     * Hakkımızda sayfası
     */
    public function aboutPage(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => [
                'tr' => 'Hakkımızda',
                'en' => 'About Us',
            ],
            'slug' => [
                'tr' => 'hakkimizda',
                'en' => 'about-us',
            ],
            'body' => [
                'tr' => '<h2>Biz Kimiz?</h2><p>1990 yılından bu yana sektörde öncü bir firma olarak hizmet vermekteyiz. Müşteri memnuniyetini ön planda tutarak, kaliteli ve güvenilir çözümler sunuyoruz.</p><h3>Misyonumuz</h3><p>Teknoloji ve inovasyonu birleştirerek, müşterilerimize en iyi hizmeti sunmak.</p><h3>Vizyonumuz</h3><p>Sektörde lider konumumuzu koruyarak, global pazarda söz sahibi olmak.</p>',
                'en' => '<h2>Who We Are?</h2><p>We have been serving as a leading company in the industry since 1990. We offer quality and reliable solutions by prioritizing customer satisfaction.</p><h3>Our Mission</h3><p>To provide the best service to our customers by combining technology and innovation.</p><h3>Our Vision</h3><p>To maintain our leading position in the sector and have a say in the global market.</p>',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Contact page state
     * İletişim sayfası
     */
    public function contactPage(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => [
                'tr' => 'İletişim',
                'en' => 'Contact',
            ],
            'slug' => [
                'tr' => 'iletisim',
                'en' => 'contact',
            ],
            'body' => [
                'tr' => '<h2>Bize Ulaşın</h2><p>Size en iyi hizmeti sunabilmek için buradayız.</p><h3>Adres</h3><p>Atatürk Cad. No:123<br>Kadıköy / İstanbul</p><h3>Telefon</h3><p>+90 (216) 123 45 67</p><h3>E-posta</h3><p>info@example.com</p>',
                'en' => '<h2>Contact Us</h2><p>We are here to provide you with the best service.</p><h3>Address</h3><p>Ataturk Street No:123<br>Kadikoy / Istanbul</p><h3>Phone</h3><p>+90 (216) 123 45 67</p><h3>Email</h3><p>info@example.com</p>',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Privacy policy page state
     * Gizlilik politikası sayfası
     */
    public function privacyPage(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => [
                'tr' => 'Gizlilik Politikası',
                'en' => 'Privacy Policy',
            ],
            'slug' => [
                'tr' => 'gizlilik-politikasi',
                'en' => 'privacy-policy',
            ],
            'body' => [
                'tr' => '<h2>Gizlilik Politikası</h2><p>Bu gizlilik politikası, kişisel verilerinizin nasıl toplandığını, kullanıldığını ve korunduğunu açıklamaktadır.</p><h3>Veri Toplama</h3><p>Web sitemizi ziyaret ettiğinizde bazı kişisel bilgiler toplanabilir.</p><h3>Veri Kullanımı</h3><p>Toplanan veriler sadece belirtilen amaçlar için kullanılır.</p><h3>Veri Güvenliği</h3><p>Verilerinizin güvenliği bizim için önemlidir ve gerekli tüm önlemler alınmıştır.</p>',
                'en' => '<h2>Privacy Policy</h2><p>This privacy policy explains how your personal data is collected, used and protected.</p><h3>Data Collection</h3><p>Some personal information may be collected when you visit our website.</p><h3>Data Usage</h3><p>Collected data is only used for specified purposes.</p><h3>Data Security</h3><p>The security of your data is important to us and all necessary precautions have been taken.</p>',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Terms page state
     * Kullanım koşulları sayfası
     */
    public function termsPage(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => [
                'tr' => 'Kullanım Koşulları',
                'en' => 'Terms of Use',
            ],
            'slug' => [
                'tr' => 'kullanim-kosullari',
                'en' => 'terms-of-use',
            ],
            'body' => [
                'tr' => '<h2>Kullanım Koşulları</h2><p>Bu web sitesini kullanarak aşağıdaki koşulları kabul etmiş olursunuz.</p><h3>Genel Kurallar</h3><p>Site içeriği telif hakkı ile korunmaktadır.</p><h3>Sorumluluk Reddi</h3><p>Site içeriğinin doğruluğu garanti edilmemektedir.</p><h3>Değişiklikler</h3><p>Bu koşullar önceden haber verilmeksizin değiştirilebilir.</p>',
                'en' => '<h2>Terms of Use</h2><p>By using this website, you accept the following terms.</p><h3>General Rules</h3><p>Site content is protected by copyright.</p><h3>Disclaimer</h3><p>The accuracy of the site content is not guaranteed.</p><h3>Changes</h3><p>These terms may be changed without prior notice.</p>',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Bulk create için optimize edilmiş state
     * Performans testleri için
     */
    public function simple(): static
    {
        return $this->state(fn (array $attributes) => [
            'css' => null,
            'js' => null,
            'body' => [
                'tr' => '<p>' . $this->faker->paragraph() . '</p>',
                'en' => '<p>' . $this->faker->paragraph() . '</p>',
            ],
        ]);
    }
}