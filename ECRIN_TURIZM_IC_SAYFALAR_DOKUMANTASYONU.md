# ECRİN TURİZM - İÇ SAYFALAR DOKÜMANTASYONU

> **Hizmetlerimiz, Hakkımızda ve İletişim Sayfaları İçerik Kılavuzu**

**Son Güncelleme:** 10 Ocak 2026
**Versiyon:** 1.0

---

## 📋 İÇİNDEKİLER

1. [Hizmetlerimiz Sayfası](#hizmetlerimiz-sayfasi)
2. [Hakkımızda Sayfası](#hakkimizda-sayfasi)
3. [İletişim Sayfası](#iletisim-sayfasi)

---

## 🚌 HİZMETLERİMİZ SAYFASI

**Dosya:** `hizmetlerimiz.html`

### Hero Bölümü

```html
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white py-20 md:py-28">
    <div class="container mx-auto px-4 text-center max-w-4xl">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
            Hizmetlerimiz
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto">
            Profesyonel ve güvenilir taşımacılık çözümleriyle hizmetinizdeyiz
        </p>
    </div>
</section>
```

### Hizmet Detay Bölümü

Her hizmet için ayrı detay kartı:

#### 1. Turizm Taşımacılığı

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <!-- Başlık ve İkon -->
            <div class="flex items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bus text-4xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                        Turizm Taşımacılığı
                    </h2>
                    <p class="text-lg text-gray-600">
                        Konforlu ve güvenli yolculuklar
                    </p>
                </div>
            </div>

            <!-- Açıklama -->
            <div class="prose prose-lg max-w-none mb-8">
                <p class="text-gray-600 leading-relaxed">
                    Yurt içi ve yurt dışı turlarda modern araç filomuzla
                    profesyonel taşımacılık hizmeti sunuyoruz. VIP otobüslerimiz
                    ve deneyimli şoförlerimizle yolculuğunuzun her anında
                    konfor ve güvenliğiniz bizim önceliğimizdir.
                </p>
            </div>

            <!-- Özellikler -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Modern ve bakımlı araçlar</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Profesyonel şoförler</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Konforlu yolculuk deneyimi</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Esnek seyahat planları</span>
                </div>
            </div>

            <!-- CTA -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="tel:+905468101717" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-phone"></i>
                    <span>Hemen Arayın</span>
                </a>
                <a href="https://wa.me/905468101717" target="_blank" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>

        </div>
    </div>
</section>
```

#### 2. Personel Taşımacılığı

```html
<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <div class="flex items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-briefcase text-4xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                        Personel Taşımacılığı
                    </h2>
                    <p class="text-lg text-gray-600">
                        Kurumunuza özel servis çözümleri
                    </p>
                </div>
            </div>

            <div class="prose prose-lg max-w-none mb-8">
                <p class="text-gray-600 leading-relaxed">
                    Kurumunuza özel personel servis çözümleri sunuyoruz.
                    Düzenli güzergah planlaması, zamanında varış garantisi
                    ve profesyonel hizmet anlayışımızla iş gücünüzün
                    ulaşımını optimize ediyoruz.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Özel güzergah planlaması</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Zamanında varış garantisi</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">GPS takip sistemi</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Esnek sözleşme seçenekleri</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="tel:+905468101717" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-phone"></i>
                    <span>Hemen Arayın</span>
                </a>
                <a href="https://wa.me/905468101717" target="_blank" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>

        </div>
    </div>
</section>
```

#### 3. Öğrenci Taşımacılığı

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <div class="flex items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-graduation-cap text-4xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                        Öğrenci Taşımacılığı
                    </h2>
                    <p class="text-lg text-gray-600">
                        Güvenli okul servisi
                    </p>
                </div>
            </div>

            <div class="prose prose-lg max-w-none mb-8">
                <p class="text-gray-600 leading-relaxed">
                    Çocuklarınızın eğitim yolculuğunda güvenlik en önemli
                    önceliğimizdir. Güvenlik standartlarına uygun araçlarımız,
                    deneyimli sürücülerimiz ve takip sistemimizle ailelere
                    gönül rahatlığı sağlıyoruz.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Güvenlik standartlarına uygun araçlar</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Deneyimli sürücüler</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">GPS takip sistemi</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Güvenli taşıma garantisi</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="tel:+905468101717" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-phone"></i>
                    <span>Hemen Arayın</span>
                </a>
                <a href="https://wa.me/905468101717" target="_blank" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>

        </div>
    </div>
</section>
```

#### 4. Otel Rezervasyonları

```html
<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <div class="flex items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-hotel text-4xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                        Otel Rezervasyonları
                    </h2>
                    <p class="text-lg text-gray-600">
                        En iyi fiyat garantisi
                    </p>
                </div>
            </div>

            <div class="prose prose-lg max-w-none mb-8">
                <p class="text-gray-600 leading-relaxed">
                    Anlaşmalı otellerimizle en uygun fiyat garantisi sunuyoruz.
                    Tatil paketleri, grup rezervasyonları ve özel organizasyonlar
                    için kapsamlı çözümler.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Anlaşmalı oteller</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Uygun fiyat garantisi</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Paket tur seçenekleri</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Hızlı rezervasyon</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="tel:+905468101717" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-phone"></i>
                    <span>Hemen Arayın</span>
                </a>
                <a href="https://wa.me/905468101717" target="_blank" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>

        </div>
    </div>
</section>
```

#### 5. Yat Kiralama

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <div class="flex items-center gap-4 mb-8">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-ship text-4xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                        Yat Kiralama
                    </h2>
                    <p class="text-lg text-gray-600">
                        Mavi yolculuk deneyimi
                    </p>
                </div>
            </div>

            <div class="prose prose-lg max-w-none mb-8">
                <p class="text-gray-600 leading-relaxed">
                    Mavi yolculuğun keyfini premium yat kiralamaları ile çıkarın.
                    Profesyonel mürettebat ve kişiye özel rotalarla unutulmaz
                    tatil deneyimi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Lüks yat seçenekleri</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Kişiye özel rotalar</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Profesyonel mürettebat</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-check text-green-500 mt-1"></i>
                    <span class="text-gray-700">Özel organizasyonlar</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="tel:+905468101717" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-phone"></i>
                    <span>Hemen Arayın</span>
                </a>
                <a href="https://wa.me/905468101717" target="_blank" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>

        </div>
    </div>
</section>
```

---

## 📖 HAKKIMIZDA SAYFASI

**Dosya:** `hakkimizda.html`

### Hero Bölümü

```html
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white py-20 md:py-28">
    <div class="container mx-auto px-4 text-center max-w-4xl">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
            Hakkımızda
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto">
            2008'den beri güvenle hizmet veren A Grubu Seyahat Acentası
        </p>
    </div>
</section>
```

### Hikayemiz Bölümü

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8 text-center">
                Hikayemiz
            </h2>

            <div class="prose prose-lg max-w-none">
                <p class="text-gray-600 leading-relaxed mb-6">
                    <strong>Ecrin Turizm Sanayi ve Ticaret Limited Şirketi</strong>,
                    2008 yılında İstanbul'da kurulmuştur. "Olçun Travel" markasıyla
                    hizmet veren firmamız, A Grubu Seyahat Acentası İşletme Belgesi
                    (No: 9817) ile faaliyetlerini sürdürmektedir.
                </p>

                <p class="text-gray-600 leading-relaxed mb-6">
                    Kuruluşumuzdan bu yana, turizm ve taşımacılık sektöründe
                    güvenilir, kaliteli ve müşteri odaklı hizmet anlayışımızla
                    yolcularımızın konforunu ve güvenliğini ön planda tutarak
                    hizmet veriyoruz.
                </p>

                <p class="text-gray-600 leading-relaxed">
                    Güngören / İstanbul merkezli olarak faaliyet göstermekteyiz.
                    Turizm taşımacılığı, personel servisleri, öğrenci taşımacılığı,
                    otel rezervasyonları ve yat kiralama alanlarında geniş bir
                    hizmet yelpazesi sunmaktayız.
                </p>
            </div>

        </div>
    </div>
</section>
```

### Değerlerimiz Bölümü

```html
<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-4">

        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 text-center">
            Değerlerimiz
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">

            <!-- Değer 1: Güven -->
            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-handshake text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Güven</h3>
                <p class="text-gray-600 leading-relaxed">
                    Müşterilerimizin bize emanet ettiği en değerli varlıkları
                    güvenle taşımak, her zaman önceliğimizdir.
                </p>
            </div>

            <!-- Değer 2: Kalite -->
            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-award text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Kalite</h3>
                <p class="text-gray-600 leading-relaxed">
                    İş süreçlerimizde en yüksek kalite standartlarını benimseyerek,
                    sürekli gelişim prensibiyle çalışırız.
                </p>
            </div>

            <!-- Değer 3: Müşteri Odaklılık -->
            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-users text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Müşteri Odaklılık</h3>
                <p class="text-gray-600 leading-relaxed">
                    Müşterilerimizin ihtiyaçlarını dinler, beklentilerini aşmak
                    için çaba gösteririz.
                </p>
            </div>

            <!-- Değer 4: Profesyonellik -->
            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-user-tie text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Profesyonellik</h3>
                <p class="text-gray-600 leading-relaxed">
                    İşimizi tutkuyla yapar, profesyonel bir ekip anlayışıyla
                    hareket ederiz.
                </p>
            </div>

            <!-- Değer 5: Güvenlik -->
            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-shield-halved text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Güvenlik</h3>
                <p class="text-gray-600 leading-relaxed">
                    Yolcularımızın güvenliği her zaman en önemli önceliğimizdir.
                    Tüm standartlara uyum sağlarız.
                </p>
            </div>

            <!-- Değer 6: Sürdürülebilirlik -->
            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fa-solid fa-leaf text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Sürdürülebilirlik</h3>
                <p class="text-gray-600 leading-relaxed">
                    Çevreye duyarlı, sosyal sorumluluğa önem veren bir kurum
                    olarak faaliyet gösteririz.
                </p>
            </div>

        </div>
    </div>
</section>
```

### Misyon & Vizyon

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

                <!-- Misyon -->
                <div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fa-solid fa-bullseye text-3xl text-blue-600"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Misyonumuz
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Turizm ve taşımacılık sektöründe güvenilir, kaliteli ve
                        müşteri odaklı hizmet anlayışıyla, yolcularımızın konforunu
                        ve güvenliğini ön planda tutarak profesyonel çözümler sunmak.
                    </p>
                </div>

                <!-- Vizyon -->
                <div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fa-solid fa-lightbulb text-3xl text-blue-600"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Vizyonumuz
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Türkiye'nin en çok tercih edilen, güvenilir ve yenilikçi
                        turizm ve taşımacılık markası olmak. Sektöre yön veren,
                        müşteri memnuniyetinde örnek gösterilen bir kurum olmaktır.
                    </p>
                </div>

            </div>

        </div>
    </div>
</section>
```

### Lisans ve Belgeler

```html
<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-4">

        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 text-center">
            Lisans ve Belgelerimiz
        </h2>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-8 rounded-xl shadow-lg">

                <div class="space-y-6">

                    <!-- A Grubu Lisans -->
                    <div class="flex items-start gap-4 p-6 bg-blue-50 rounded-lg">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-certificate text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                A Grubu Seyahat Acentası İşletme Belgesi
                            </h3>
                            <p class="text-gray-600 mb-2">
                                <strong>Belge No:</strong> 9817
                            </p>
                            <p class="text-gray-600">
                                T.C. Kültür ve Turizm Bakanlığı tarafından verilen
                                A Grubu Seyahat Acentası İşletme Belgesi ile
                                faaliyetlerimizi sürdürmekteyiz.
                            </p>
                        </div>
                    </div>

                    <!-- Diğer Bilgiler -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                <i class="fa-solid fa-calendar-check text-blue-600 mr-2"></i>
                                Kuruluş Tarihi
                            </h4>
                            <p class="text-gray-600">17.09.2008</p>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                <i class="fa-solid fa-location-dot text-blue-600 mr-2"></i>
                                Lokasyon
                            </h4>
                            <p class="text-gray-600">Güngören / İstanbul</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>
```

---

## 📞 İLETİŞİM SAYFASI

**Dosya:** `iletisim.html`

### Hero Bölümü

```html
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white py-20 md:py-28">
    <div class="container mx-auto px-4 text-center max-w-4xl">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
            İletişim
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto">
            Her an ulaşabileceğiniz profesyonel destek ekibimiz
        </p>
    </div>
</section>
```

### İletişim Bilgileri Kartları

```html
<section class="py-16 md:py-24">
    <div class="container mx-auto px-4">

        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 text-center">
            Bize Ulaşın
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto mb-16">

            <!-- Telefon Kartı -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center hover:shadow-2xl transition-all">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-phone text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">
                    Telefon
                </h3>
                <a href="tel:+905468101717" class="text-2xl font-bold text-blue-600 hover:text-blue-700">
                    0546 810 17 17
                </a>
                <p class="text-sm text-gray-500 mt-3">7/24 Destek Hattı</p>
            </div>

            <!-- WhatsApp Kartı -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center hover:shadow-2xl transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-brands fa-whatsapp text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">
                    WhatsApp
                </h3>
                <a href="https://wa.me/905468101717" target="_blank" class="text-2xl font-bold text-green-600 hover:text-green-700">
                    Mesaj Gönderin
                </a>
                <p class="text-sm text-gray-500 mt-3">Hızlı İletişim</p>
            </div>

            <!-- E-posta Kartı -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center hover:shadow-2xl transition-all">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-envelope text-3xl text-amber-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">
                    E-posta
                </h3>
                <a href="mailto:info@ecrinturizm.org" class="text-lg font-semibold text-amber-600 hover:text-amber-700 break-all">
                    info@ecrinturizm.org
                </a>
                <p class="text-sm text-gray-500 mt-3">Bilgi ve Destek</p>
            </div>

        </div>

    </div>
</section>
```

### Adres ve Konum

```html
<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-4">

        <div class="max-w-4xl mx-auto">

            <!-- Adres Kartı -->
            <div class="bg-white p-8 md:p-12 rounded-xl shadow-lg">

                <div class="flex items-start gap-6 mb-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-location-dot text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                            Adresimiz
                        </h2>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            <strong>Ecrin Turizm Sanayi ve Ticaret Limited Şirketi</strong><br>
                            Güngören / İstanbul
                        </p>
                    </div>
                </div>

                <!-- İletişim Özeti -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-200">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">
                            <i class="fa-solid fa-phone text-blue-600 mr-2"></i>
                            Telefon
                        </h4>
                        <p class="text-gray-600">0546 810 17 17</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">
                            <i class="fa-solid fa-envelope text-blue-600 mr-2"></i>
                            E-posta
                        </h4>
                        <p class="text-gray-600">info@ecrinturizm.org</p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
```

### Hızlı İletişim CTA

**ÖNEMLİ:** Form kullanılmaz.

```html
<section class="py-16 md:py-24 bg-gradient-to-r from-blue-600 to-blue-700">
    <div class="container max-w-4xl mx-auto px-4 text-center">

        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
            Hemen İletişime Geçin
        </h2>

        <p class="text-lg md:text-xl text-blue-100 mb-10">
            Size en uygun çözümü sunmak için buradayız
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">

            <!-- Telefon Butonu -->
            <a href="tel:+905468101717" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-10 py-6 bg-white text-gray-900 rounded-xl hover:bg-gray-50 transition-all shadow-2xl hover:shadow-3xl hover:-translate-y-1">
                <i class="fa-solid fa-phone text-3xl text-blue-600"></i>
                <div class="text-left">
                    <div class="text-xs text-gray-500">Hemen Arayın</div>
                    <div class="text-2xl font-bold">0546 810 17 17</div>
                </div>
            </a>

            <!-- WhatsApp Butonu -->
            <a href="https://wa.me/905468101717" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-10 py-6 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all shadow-2xl hover:shadow-3xl hover:-translate-y-1">
                <i class="fa-brands fa-whatsapp text-4xl"></i>
                <div class="text-left">
                    <div class="text-xs text-green-100">WhatsApp</div>
                    <div class="text-2xl font-bold">Mesaj Gönderin</div>
                </div>
            </a>

        </div>

    </div>
</section>
```

---

## ✅ İÇ SAYFALAR KONTROL LİSTESİ

### Her İç Sayfada Bulunması Gerekenler

**Header:**
- [ ] Responsive üst bar (telefon görünür)
- [ ] Ana navigasyon
- [ ] Aktif sayfa vurgusu
- [ ] CTA butonları (telefon + WhatsApp)
- [ ] Mobil menü

**Hero:**
- [ ] Temiz ve minimal tasarım
- [ ] Sayfa başlığı
- [ ] Kısa açıklama

**İçerik:**
- [ ] Doğrulanmış bilgiler
- [ ] Tutarlı ikonlar
- [ ] Responsive layout
- [ ] İletişim CTA'ları

**Footer:**
- [ ] Sosyal medya linkı YOK
- [ ] İletişim bilgileri
- [ ] Hızlı linkler
- [ ] Lisans bilgisi

---

## 📝 ÖZEL NOTLAR

### Hizmetlerimiz Sayfası

- Her hizmet için ayrı bölüm
- Özellikler check işareti ile liste
- Her bölümde telefon + WhatsApp CTA
- Alternatif arkaplan renkleri (beyaz/gri)

### Hakkımızda Sayfası

- Firma geçmişi ve hikaye
- Değerler vurgulanmalı
- Misyon ve vizyon belirtilmeli
- Lisans bilgisi öne çıkarılmalı

### İletişim Sayfası

- Form KULLANILMAZ
- Telefon, WhatsApp, E-posta kartları
- Büyük ve belirgin CTA butonları
- Adres bilgisi net ve okunaklı

---

**Müşteri:** Ecrin Turizm San. ve Tic. Ltd. Şti.
**Versiyon:** 1.0
**Tarih:** 10 Ocak 2026
