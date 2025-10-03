<?php

namespace Modules\Portfolio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Illuminate\Support\Str;

/**
 * Portfolio Factory
 *
 * Gerçekçi test verileri oluşturur.
 * Çoklu dil desteği ve SEO ayarları içerir.
 *
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Portfolio::class;

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
                     '<h3>Proje Detayları</h3>' .
                     '<p>' . $this->faker->paragraph(3) . '</p>' .
                     '<ul>' .
                     '<li><strong>Müşteri:</strong> ' . $this->faker->company() . '</li>' .
                     '<li><strong>Tarih:</strong> ' . $this->faker->date() . '</li>' .
                     '<li><strong>Teknolojiler:</strong> ' . implode(', ', $this->faker->randomElements(['Laravel', 'Vue.js', 'React', 'Node.js', 'PHP', 'MySQL'], 3)) . '</li>' .
                     '</ul>' .
                     '<h3>Sonuçlar</h3>' .
                     '<p>' . $this->faker->paragraph(4) . '</p>';

        $contentEn = '<h2>' . $this->faker->sentence() . '</h2>' .
                     '<p>' . $this->faker->paragraph(5) . '</p>' .
                     '<h3>Project Details</h3>' .
                     '<p>' . $this->faker->paragraph(3) . '</p>' .
                     '<ul>' .
                     '<li><strong>Client:</strong> ' . $this->faker->company() . '</li>' .
                     '<li><strong>Date:</strong> ' . $this->faker->date() . '</li>' .
                     '<li><strong>Technologies:</strong> ' . implode(', ', $this->faker->randomElements(['Laravel', 'Vue.js', 'React', 'Node.js', 'PHP', 'MySQL'], 3)) . '</li>' .
                     '</ul>' .
                     '<h3>Results</h3>' .
                     '<p>' . $this->faker->paragraph(4) . '</p>';

        return [
            'category_id' => PortfolioCategory::inRandomOrder()->first()?->category_id,
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
                '.portfolio-page { padding: 20px; }',
                '.portfolio-header { color: #333; font-size: 24px; }',
                '.portfolio-content { max-width: 1200px; margin: 0 auto; }'
            ]),
            'js' => $this->faker->randomElement([
                null,
                'console.log("Portfolio loaded");',
                'document.addEventListener("DOMContentLoaded", function() { console.log("Portfolio ready"); });'
            ]),
            'is_active' => $this->faker->boolean(80), // %80 aktif
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Active state
     * Aktif portfolio
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Inactive state
     * Pasif portfolio
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * With specific category
     * Belirli kategori ile portfolio
     */
    public function forCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * With custom CSS and JS
     * Özel CSS ve JS içeren portfolio
     */
    public function withCustomStyles(): static
    {
        return $this->state(fn (array $attributes) => [
            'css' => '
/* Custom Portfolio Styles */
.portfolio-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 20px;
}
.portfolio-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    margin-bottom: 40px;
    border-radius: 15px;
}
.portfolio-content {
    font-size: 18px;
    line-height: 1.8;
    color: #333;
}
.portfolio-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 40px 0;
}
            ',
            'js' => '
// Custom Portfolio JavaScript
document.addEventListener("DOMContentLoaded", function() {
    console.log("Portfolio page initialized");

    // Lightbox functionality
    const images = document.querySelectorAll(".portfolio-gallery img");
    images.forEach(img => {
        img.addEventListener("click", function() {
            // Lightbox implementation
            console.log("Image clicked:", this.src);
        });
    });

    // Lazy loading for images
    if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove("lazy");
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll("img.lazy").forEach(img => {
            imageObserver.observe(img);
        });
    }
});
            ',
        ]);
    }

    /**
     * Web design portfolio state
     * Web tasarım portfolio'su
     */
    public function webDesign(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => [
                'tr' => $this->faker->company() . ' Web Sitesi',
                'en' => $this->faker->company() . ' Website',
            ],
            'body' => [
                'tr' => '<h2>Proje Özeti</h2><p>Modern ve kullanıcı dostu web tasarım projesi. Responsive tasarım ve SEO optimizasyonu ile hazırlandı.</p><h3>Özellikler</h3><ul><li>Responsive Tasarım</li><li>SEO Optimizasyonu</li><li>Hızlı Yükleme</li><li>Modern UI/UX</li></ul>',
                'en' => '<h2>Project Summary</h2><p>Modern and user-friendly web design project. Prepared with responsive design and SEO optimization.</p><h3>Features</h3><ul><li>Responsive Design</li><li>SEO Optimization</li><li>Fast Loading</li><li>Modern UI/UX</li></ul>',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Mobile app portfolio state
     * Mobil uygulama portfolio'su
     */
    public function mobileApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => [
                'tr' => $this->faker->word() . ' Mobil Uygulaması',
                'en' => $this->faker->word() . ' Mobile App',
            ],
            'body' => [
                'tr' => '<h2>Uygulama Özeti</h2><p>iOS ve Android platformları için native mobil uygulama geliştirme projesi.</p><h3>Özellikler</h3><ul><li>Native Performance</li><li>Offline Çalışma</li><li>Push Notifications</li><li>Analytics Entegrasyonu</li></ul>',
                'en' => '<h2>Application Summary</h2><p>Native mobile application development project for iOS and Android platforms.</p><h3>Features</h3><ul><li>Native Performance</li><li>Offline Mode</li><li>Push Notifications</li><li>Analytics Integration</li></ul>',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * E-commerce portfolio state
     * E-ticaret portfolio'su
     */
    public function ecommerce(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => [
                'tr' => $this->faker->company() . ' E-Ticaret Platformu',
                'en' => $this->faker->company() . ' E-Commerce Platform',
            ],
            'body' => [
                'tr' => '<h2>Platform Özeti</h2><p>Tam özellikli e-ticaret platformu. Ödeme entegrasyonları ve stok yönetimi ile hazırlandı.</p><h3>Özellikler</h3><ul><li>Çoklu Ödeme Yöntemleri</li><li>Stok Yönetimi</li><li>Sipariş Takibi</li><li>Müşteri Paneli</li></ul>',
                'en' => '<h2>Platform Summary</h2><p>Full-featured e-commerce platform. Prepared with payment integrations and stock management.</p><h3>Features</h3><ul><li>Multiple Payment Methods</li><li>Stock Management</li><li>Order Tracking</li><li>Customer Panel</li></ul>',
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
