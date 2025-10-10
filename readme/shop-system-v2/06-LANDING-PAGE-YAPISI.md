# 🎨 LANDING PAGE YAPISI

## 🎯 AMAÇ

Her ürün için **bağımsız landing page** oluştur:
- Marketing içerik (intro + body)
- Primary Specs (4 vitrin kartı)
- Features (branding: slogan, motto, technical_summary)
- Use Cases (6+ senaryo)
- Competitive Advantages (5+ avantaj)
- FAQ (10+ soru-cevap)
- CTA butonları (teklif al, iletişim)

---

## 📄 BLADE ŞABLONU

**Konum:** `resources/views/shop/product-landing.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="product-landing-page">

  {{-- HERO SECTION --}}
  <section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="display-4 fw-bold mb-3">
            {{ $product->getTranslated('title', app()->getLocale()) }}
          </h1>
          <p class="lead mb-4">
            {{ $product->getTranslated('short_description', app()->getLocale()) }}
          </p>
          <div class="d-flex gap-3">
            <a href="#contact-form" class="btn btn-light btn-lg">
              <i class="fa-solid fa-envelope me-2"></i>Teklif Al
            </a>
            <a href="tel:02167553555" class="btn btn-outline-light btn-lg">
              <i class="fa-solid fa-phone me-2"></i>0216 755 3 555
            </a>
          </div>
        </div>
        <div class="col-lg-6">
          @if($product->hasMedia('featured_image'))
            <img src="{{ $product->getFirstMediaUrl('featured_image', 'large') }}"
                 alt="{{ $product->getTranslated('title', app()->getLocale()) }}"
                 class="img-fluid rounded shadow-lg">
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- PRIMARY SPECS (4 KART) --}}
  <section class="primary-specs-section py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-5">Temel Özellikler</h2>
      <div class="row g-4">
        @foreach($product->primary_specs as $spec)
          <div class="col-md-3 col-6">
            <div class="spec-card card h-100 text-center border-0 shadow-sm">
              <div class="card-body">
                <div class="spec-icon mb-3">
                  <i class="{{ $spec['icon'] ?? 'fa-solid fa-check-circle' }} fa-3x text-primary"></i>
                </div>
                <h5 class="spec-label">{{ $spec['label'] }}</h5>
                <p class="spec-value fw-bold text-muted mb-0">{{ $spec['value'] }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- MARKETING CONTENT (intro + body) --}}
  <section class="marketing-content-section py-5">
    <div class="container">
      <div class="marketing-content">
        {!! $product->getTranslated('long_description', app()->getLocale()) !!}
      </div>
    </div>
  </section>

  {{-- BRANDING (Slogan + Motto + Technical Summary) --}}
  @if(isset($product->features['branding']))
    <section class="branding-section py-5 bg-primary text-white text-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h3 class="display-6 mb-3">
              {{ $product->features['branding']['slogan'] }}
            </h3>
            <p class="lead fst-italic mb-4">
              "{{ $product->features['branding']['motto'] }}"
            </p>
            <p class="small opacity-75">
              {{ $product->features['branding']['technical_summary'] }}
            </p>
          </div>
        </div>
      </div>
    </section>
  @endif

  {{-- HIGHLIGHTED FEATURES --}}
  <section class="highlighted-features-section py-5">
    <div class="container">
      <h2 class="text-center mb-5">Öne Çıkan Özellikler</h2>
      <div class="row g-4">
        @foreach($product->highlighted_features as $feature)
          <div class="col-md-4">
            <div class="feature-card card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="feature-icon mb-3">
                  <i class="fa-solid fa-{{ $feature['icon'] }} fa-2x text-primary"></i>
                </div>
                <h4 class="feature-title">
                  {{ $feature['title'][app()->getLocale()] }}
                </h4>
                <p class="feature-description text-muted">
                  {{ $feature['description'][app()->getLocale()] }}
                </p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- USE CASES --}}
  <section class="use-cases-section py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-5">Kullanım Alanları</h2>
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <ul class="use-cases-list list-unstyled">
            @foreach($product->use_cases[app()->getLocale()] as $index => $useCase)
              <li class="use-case-item mb-3">
                <i class="fa-solid fa-check-circle text-success me-3"></i>
                <span>{{ $useCase }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </section>

  {{-- COMPETITIVE ADVANTAGES --}}
  <section class="competitive-advantages-section py-5">
    <div class="container">
      <h2 class="text-center mb-5">Rekabet Avantajları</h2>
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <div class="row g-4">
            @foreach($product->competitive_advantages[app()->getLocale()] as $index => $advantage)
              <div class="col-md-6">
                <div class="advantage-card card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <div class="advantage-number badge bg-primary rounded-circle mb-3">
                      {{ $index + 1 }}
                    </div>
                    <p class="advantage-text mb-0">{{ $advantage }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- FAQ SECTION --}}
  <section class="faq-section py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-5">Sık Sorulan Sorular</h2>
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="accordion" id="faqAccordion">
            @foreach($product->faq_data as $index => $faq)
              <div class="accordion-item mb-3 border-0 shadow-sm {{ $faq['is_highlighted'] ?? false ? 'highlighted' : '' }}">
                <h3 class="accordion-header" id="faqHeading{{ $index }}">
                  <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#faqCollapse{{ $index }}">
                    <i class="fa-solid fa-question-circle me-3 text-primary"></i>
                    {{ $faq['question'][app()->getLocale()] }}
                  </button>
                </h3>
                <div id="faqCollapse{{ $index }}"
                     class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                     data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    {{ $faq['answer'][app()->getLocale()] }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="faq-cta mt-5 text-center p-4 bg-white rounded shadow-sm">
            <h4>Başka sorunuz mu var?</h4>
            <p class="mb-3">0216 755 3 555 numarasını arayın veya
              <a href="mailto:info@ixtif.com">info@ixtif.com</a> adresine yazın.</p>
            <a href="#contact-form" class="btn btn-primary btn-lg">
              <i class="fa-solid fa-envelope me-2"></i>İletişime Geç
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- CONTACT FORM --}}
  <section id="contact-form" class="contact-form-section py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 mx-auto">
          <h2 class="text-center mb-4">Teklif Al</h2>
          <form action="{{ route('shop.quote.submit') }}" method="POST" class="contact-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->product_id }}">

            <div class="mb-3">
              <label for="name" class="form-label">Ad Soyad *</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">E-posta *</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
              <label for="phone" class="form-label">Telefon *</label>
              <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>

            <div class="mb-3">
              <label for="message" class="form-label">Mesajınız</label>
              <textarea class="form-control" id="message" name="message" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
              <i class="fa-solid fa-paper-plane me-2"></i>Teklif İste
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

</div>
@endsection
```

---

## 🎨 CSS STYLES

**Konum:** `public/css/shop-landing.css`

```css
.product-landing-page {
  font-family: 'Inter', sans-serif;
}

.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 500px;
}

.primary-specs-section .spec-card {
  transition: transform 0.3s;
}

.primary-specs-section .spec-card:hover {
  transform: translateY(-10px);
}

.spec-icon i {
  color: var(--bs-primary);
}

.marketing-content section.marketing-intro {
  background: #f8f9fa;
  padding: 2rem;
  border-left: 4px solid var(--bs-primary);
  margin-bottom: 2rem;
}

.marketing-content section.marketing-body {
  line-height: 1.8;
}

.branding-section {
  background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
}

.advantage-number {
  width: 40px;
  height: 40px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  font-weight: bold;
}

.faq-section .accordion-item.highlighted {
  border: 2px solid var(--bs-warning);
}

.contact-form-section {
  background: #f8f9fa;
}
```

---

## 🔗 ROUTE

**Konum:** `routes/web.php`

```php
// Landing page route
Route::get('/shop/urun/{slug}', [ShopProductController::class, 'landing'])
    ->name('shop.product.landing');

// Quote form submission
Route::post('/shop/teklif-gonder', [ShopQuoteController::class, 'submit'])
    ->name('shop.quote.submit');
```

---

## 🎯 CONTROLLER

**Konum:** `app/Http/Controllers/ShopProductController.php`

```php
public function landing(string $slug)
{
    $locale = app()->getLocale();

    $product = ShopProduct::active()
        ->published()
        ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
        ->firstOrFail();

    // SEO ayarları
    SEO::setTitle($product->getTranslated('title', $locale));
    SEO::setDescription($product->getTranslated('short_description', $locale));

    return view('shop.product-landing', compact('product'));
}
```

---

## ✅ ÖZET

| Bölüm | Veri Kaynağı | Konum |
|-------|-------------|-------|
| Hero | `title`, `short_description` | `shop_products` |
| Primary Specs | `primary_specs` (4 kart) | `shop_products` |
| Marketing | `long_description` (intro+body) | `shop_products` |
| Branding | `features.branding` (slogan, motto) | `shop_products` |
| Highlighted Features | `highlighted_features` | `shop_products` |
| Use Cases | `use_cases` (6+) | `shop_products` |
| Advantages | `competitive_advantages` (5+) | `shop_products` |
| FAQ | `faq_data` (10+) | `shop_products` |

**TÜM VERİ TEK TABLODA (`shop_products`) → TEK QUERY İLE YÜKLENİR!**

---

🎉 **SHOP SİSTEMİ V2 HAZIR!**
