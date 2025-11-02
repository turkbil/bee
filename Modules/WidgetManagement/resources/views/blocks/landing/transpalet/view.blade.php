<div x-data="{ activeAccordion: null, showForm: false }" class="ads-landing">

<!-- CSS for Gradient Animation -->
<style>
@@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.animate-gradient {
  animation: gradient 3s ease infinite;
}
</style>

<!-- HERO - Agresif Satış Odaklı -->
<section class="relative py-12 md:py-16 overflow-hidden">
  <div class="container mx-auto px-4 max-w-7xl">
    <div class="grid lg:grid-cols-2 gap-8 items-center">

      <!-- Sol: Content -->
      <div class="text-center lg:text-left">
        <!-- Trust Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-full text-sm font-semibold text-green-700 dark:text-green-300 mb-4">
          <span class="animate-pulse">🟢</span>
          Stokta Var • Hemen Teslimat
        </div>

        <!-- Hero Title - GRADIENT VURGU -->
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-6 leading-tight">
          <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 dark:from-blue-400 dark:via-purple-400 dark:to-blue-400 bg-clip-text text-transparent">
            Türkiye'nin 1 Numaralı
          </span>
          <br>
          <span class="text-gray-900 dark:text-white">Elektrikli Transpaleti!</span>
        </h1>

        <!-- Value Proposition - TEK VURGU -->
        <div class="inline-flex items-center gap-3 px-5 py-3 bg-gray-900 dark:bg-white rounded-xl mb-6">
          <span class="text-2xl md:text-3xl font-black text-white dark:text-gray-900">1350$</span>
          <span class="text-sm font-semibold text-gray-300 dark:text-gray-600">'dan başlayan</span>
        </div>

        <p class="text-base md:text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-xl mx-auto lg:mx-0">
          <strong class="text-gray-900 dark:text-white">Lityum akü teknolojisi</strong> ile kesintisiz güç. <strong class="text-gray-900 dark:text-white">1 yıl ürün + 2 yıl akü garantisi!</strong>
        </p>

        <!-- CTA Buttons - 1 PRIMARY + 1 OUTLINE -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-8">
          <a href="tel:02167553555" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-bold rounded-xl shadow-lg transition-all duration-300 no-underline">
            <i class="fa-solid fa-phone text-lg"></i>
            <span>0216 755 3 555</span>
          </a>
          <a href="{{ whatsapp_link() }}" class="inline-flex items-center justify-center gap-3 px-8 py-4 border-2 border-gray-900 dark:border-white hover:bg-gray-900 dark:hover:bg-white text-gray-900 dark:text-white hover:text-white dark:hover:text-gray-900 font-bold rounded-xl transition-all duration-300 no-underline">
            <i class="fa-brands fa-whatsapp text-lg"></i>
            <span>WhatsApp</span>
          </a>
        </div>

        <!-- Trust Signals - SADECE İKONLAR -->
        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6 text-sm text-gray-600 dark:text-gray-400">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-shield-check text-green-600 dark:text-green-400 text-lg"></i>
            <span class="text-gray-900 dark:text-white font-semibold">1+2 Yıl Garanti</span>
          </div>
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-truck-fast text-blue-600 dark:text-blue-400 text-lg"></i>
            <span class="text-gray-900 dark:text-white font-semibold">Hızlı Teslimat</span>
          </div>
        </div>
      </div>

      <!-- Sağ: Product Image Placeholder -->
      <div class="relative">
        <div class="relative backdrop-blur-sm bg-gray-100 dark:bg-gray-800 rounded-3xl border border-gray-300 dark:border-gray-700 aspect-square flex items-center justify-center overflow-hidden shadow-2xl">
          <!-- Placeholder Icon -->
          <div class="text-center">
            <i class="fa-solid fa-dolly text-9xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400 font-semibold">Elektrikli Transpalet<br>Ürün Görseli</p>
          </div>

          <!-- Floating Badges -->
          <div class="absolute top-4 right-4 backdrop-blur-md bg-white/90 dark:bg-gray-800/90 px-4 py-2 rounded-full shadow-lg">
            <span class="text-green-600 dark:text-green-400 font-bold">✓ Stokta</span>
          </div>
          <div class="absolute bottom-4 left-4 backdrop-blur-md bg-orange-500 px-4 py-2 rounded-full shadow-lg">
            <span class="text-white font-bold">🔥 Çok Satan</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ÜRÜN LİSTESİ - ARKAPLAN KALDIRILDI -->
<section class="py-12">
  <div class="container mx-auto px-4 max-w-7xl">
    <div class="text-center mb-8">
      <span class="inline-block px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-bold rounded-full mb-3">
        POPÜLER MODELLER
      </span>
      <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-2">
        En Çok Tercih Edilen Modeller
      </h2>
      <p class="text-base text-gray-600 dark:text-gray-400">Profesyonel kullanım için tasarlandı</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6">

      <!-- F4 Lityum İyon -->
      <div class="group bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-2xl transition-all duration-300 overflow-hidden">
        <div class="relative bg-gray-100 dark:bg-gray-700 aspect-video flex items-center justify-center">
          <i class="fa-solid fa-dolly text-6xl text-gray-400 dark:text-gray-600"></i>
          <div class="absolute top-2 right-2 px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded-full">Stokta</div>
        </div>
        <div class="p-4">
          <div class="flex items-start justify-between mb-2">
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">F4 Lityum İyon</h3>
              <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold">En Popüler</p>
            </div>
            <div class="text-right">
              <div class="text-xl font-black text-gray-900 dark:text-white">$1350</div>
              <p class="text-xs text-gray-500">+KDV</p>
            </div>
          </div>
          <div class="flex flex-wrap gap-1 mb-2">
            <span class="text-xs px-2 py-1 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-full font-semibold">1500 kg</span>
            <span class="text-xs px-2 py-1 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-full font-semibold">Li-ion 24V</span>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">✓ Çift pil yuvası • 8 saat kullanım</p>
          <a href="tel:02167553555" class="block w-full text-center px-4 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-lg transition-all no-underline text-sm">
            Ara
          </a>
        </div>
      </div>

      <!-- F4 Terazili - ÇOK SATAN -->
      <div class="relative group bg-white dark:bg-gray-800 rounded-2xl border-2 border-orange-300 dark:border-orange-700 hover:border-orange-500 dark:hover:border-orange-400 hover:shadow-2xl hover:shadow-orange-500/30 transition-all duration-300 overflow-hidden">
        <div class="absolute -top-2 left-4 px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full shadow-lg z-10">
          🔥 ÇOK SATAN
        </div>
        <div class="relative bg-gray-100 dark:bg-gray-700 aspect-video flex items-center justify-center">
          <i class="fa-solid fa-scale-balanced text-6xl text-gray-400 dark:text-gray-600"></i>
          <div class="absolute top-2 right-2 px-2 py-1 bg-green-600 text-white text-xs font-bold rounded-full">Stokta</div>
        </div>
        <div class="p-4">
          <div class="flex items-start justify-between mb-2">
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">F4 Terazili</h3>
              <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold">Kantar Sistemli</p>
            </div>
            <div class="text-right">
              <div class="text-xl font-black text-gray-900 dark:text-white">$2500</div>
              <p class="text-xs text-gray-500">+KDV</p>
            </div>
          </div>
          <div class="flex flex-wrap gap-1 mb-2">
            <span class="text-xs px-2 py-1 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-full font-semibold">1500 kg</span>
            <span class="text-xs px-2 py-1 bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-700 text-orange-700 dark:text-orange-300 rounded-full font-semibold">Dijital Kantar</span>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">✓ Hassas ölçüm • %50 tasarruf</p>
          <a href="tel:02167553555" class="block w-full text-center px-4 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-lg transition-all no-underline text-sm">
            Ara
          </a>
        </div>
      </div>

      <!-- F4 201 -->
      <div class="group bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-2xl transition-all duration-300 overflow-hidden">
        <div class="relative bg-gray-100 dark:bg-gray-700 aspect-video flex items-center justify-center">
          <i class="fa-solid fa-truck-ramp-box text-6xl text-gray-400 dark:text-gray-600"></i>
          <div class="absolute top-2 right-2 px-2 py-1 bg-purple-600 text-white text-xs font-bold rounded-full">Stokta</div>
        </div>
        <div class="p-4">
          <div class="flex items-start justify-between mb-2">
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">F4 201</h3>
              <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold">Yüksek Kapasite</p>
            </div>
            <div class="text-right">
              <div class="text-xl font-black text-gray-900 dark:text-white">$2350</div>
              <p class="text-xs text-gray-500">+KDV</p>
            </div>
          </div>
          <div class="flex flex-wrap gap-1 mb-2">
            <span class="text-xs px-2 py-1 bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300 rounded-full font-semibold">2000 kg</span>
            <span class="text-xs px-2 py-1 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-full font-semibold">Li-ion 24V</span>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">✓ Çıkarılabilir pil • Kesintisiz güç</p>
          <a href="tel:02167553555" class="block w-full text-center px-4 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-lg transition-all no-underline text-sm">
            Ara
          </a>
        </div>
      </div>

      <!-- EPL 154 -->
      <div class="group bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-2xl transition-all duration-300 overflow-hidden">
        <div class="relative bg-gray-100 dark:bg-gray-700 aspect-video flex items-center justify-center">
          <i class="fa-solid fa-pallet text-6xl text-gray-400 dark:text-gray-600"></i>
          <div class="absolute top-2 right-2 px-2 py-1 bg-emerald-600 text-white text-xs font-bold rounded-full">Stokta</div>
        </div>
        <div class="p-4">
          <div class="flex items-start justify-between mb-2">
            <div>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">EPL 154</h3>
              <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">Kompakt & Hızlı</p>
            </div>
            <div class="text-right">
              <div class="text-xl font-black text-gray-900 dark:text-white">$2150</div>
              <p class="text-xs text-gray-500">+KDV</p>
            </div>
          </div>
          <div class="flex flex-wrap gap-1 mb-2">
            <span class="text-xs px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 rounded-full font-semibold">1500 kg</span>
            <span class="text-xs px-2 py-1 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-full font-semibold">6 saat şarj</span>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">✓ Dar alanlarda kullanım • Hızlı şarj</p>
          <a href="tel:02167553555" class="block w-full text-center px-4 py-2 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-lg transition-all no-underline text-sm">
            Ara
          </a>
        </div>
      </div>

    </div>

    <!-- Alt CTA -->
    <div class="mt-8 text-center">
      <button @click="showForm = true; $nextTick(() => document.getElementById('lead-form').scrollIntoView({ behavior: 'smooth' }))" class="inline-flex items-center gap-3 px-8 py-4 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-lg font-bold rounded-lg shadow-lg transition-all duration-300">
        <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
        <span>Tüm Modeller İçin Özel Fiyat Al</span>
      </button>
    </div>

  </div>
</section>

<!-- KULLANICI YORUMLARI - YENİ EKLENEN SECTION -->
<section class="py-12 bg-gray-50 dark:bg-gray-900/50">
  <div class="container mx-auto px-4 max-w-6xl">
    <div class="text-center mb-8">
      <span class="inline-block px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-bold rounded-full mb-3">
        MÜŞTERİ GÖRÜŞLERİ
      </span>
      <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-2">
        Binlerce Mutlu Müşteri
      </h2>
      <p class="text-base text-gray-600 dark:text-gray-400">Gerçek kullanıcı deneyimleri</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Yorum 1 -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 p-6 shadow-lg">
        <div class="flex items-center gap-1 mb-3">
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
        </div>
        <p class="text-gray-700 dark:text-gray-300 mb-4 text-sm italic">"F4 Terazili modeli aldık, kesinlikle fark yarattı! Hem taşıma hem tartı işlemlerini tek cihazla yapıyoruz. Nakliye maliyetlerimiz %40 düştü. Kesinlikle tavsiye ederim."</p>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
          <p class="font-bold text-gray-900 dark:text-white text-sm">Mehmet Yılmaz</p>
          <p class="text-xs text-gray-600 dark:text-gray-400">Lojistik Şirketi, İstanbul</p>
        </div>
      </div>

      <!-- Yorum 2 -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 p-6 shadow-lg">
        <div class="flex items-center gap-1 mb-3">
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
        </div>
        <p class="text-gray-700 dark:text-gray-300 mb-4 text-sm italic">"3 aydır EPL 154 kullanıyoruz. Hızlı şarj teknolojisi sayesinde molalarda bile şarj edebiliyoruz. Dar koridorlarda manevra kabiliyeti mükemmel. Ekibimiz çok memnun."</p>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
          <p class="font-bold text-gray-900 dark:text-white text-sm">Ayşe Kaya</p>
          <p class="text-xs text-gray-600 dark:text-gray-400">Depo Müdürü, Ankara</p>
        </div>
      </div>

      <!-- Yorum 3 -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 p-6 shadow-lg">
        <div class="flex items-center gap-1 mb-3">
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
          <i class="fa-solid fa-star text-yellow-500"></i>
        </div>
        <p class="text-gray-700 dark:text-gray-300 mb-4 text-sm italic">"F4 201 modelini fabrikamızda kullanıyoruz. 2 ton yük taşıma kapasitesi bize yeterli oluyor. Çıkarılabilir pil sistemi sayesinde gün boyu çalışıyor. Garanti sürecinde de destek hızlı."</p>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
          <p class="font-bold text-gray-900 dark:text-white text-sm">Can Özdemir</p>
          <p class="text-xs text-gray-600 dark:text-gray-400">Üretim Şefi, İzmir</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- LEAD GENERATION FORM - ARKAPLAN KALDIRILDI -->
<section id="lead-form" x-show="showForm" x-transition class="py-12" x-cloak>
  <div class="container mx-auto px-4 max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-6 md:p-10 border-2 border-blue-200 dark:border-blue-800">
      <div class="text-center mb-6">
        <div class="inline-block px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-bold rounded-full mb-3">
          ÖZEL TEKLİF FORMU
        </div>
        <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-2">
          Size Özel Fiyat Teklifi
        </h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm">Formu doldurun, 30 dakika içinde fiyat teklifi gönderelim!</p>
      </div>

      <form action="/iletisim" method="POST" class="space-y-4">
        @@csrf
        <input type="hidden" name="source" value="transpalet-landing">

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
              <i class="fa-solid fa-user mr-1 text-blue-600"></i> Ad Soyad *
            </label>
            <input type="text" name="name" required class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-blue-600 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-600/20 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all" placeholder="Adınız Soyadınız">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
              <i class="fa-solid fa-phone mr-1 text-green-600"></i> Telefon *
            </label>
            <input type="tel" name="phone" required class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-blue-600 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-600/20 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all" placeholder="0 (5XX) XXX XX XX">
          </div>
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-envelope mr-1 text-purple-600"></i> E-posta (Opsiyonel)
          </label>
          <input type="email" name="email" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-blue-600 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-600/20 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all" placeholder="email@example.com">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-box mr-1 text-orange-600"></i> Hangi Model İlginizi Çekiyor?
          </label>
          <select name="product" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-blue-600 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-600/20 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all">
            <option>Henüz karar vermedim</option>
            <option>F4 Lityum İyon ($1350)</option>
            <option>F4 Terazili ($2500)</option>
            <option>F4 201 ($2350)</option>
            <option>EPL 154 ($2150)</option>
            <option>Birden fazla model</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-message mr-1 text-blue-600"></i> Mesajınız
          </label>
          <textarea name="message" rows="3" class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-blue-600 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-600/20 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all resize-none" placeholder="Taleplerinizi buraya yazabilirsiniz..."></textarea>
        </div>

        <div class="flex items-start gap-3">
          <input type="checkbox" name="gdpr" required class="mt-1 w-5 h-5 border-2 border-gray-300 rounded focus:ring-4 focus:ring-blue-600/20">
          <label class="text-sm text-gray-600 dark:text-gray-400">
            <a href="/kvkk" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">KVKK Aydınlatma Metni</a>'ni okudum, kabul ediyorum. *
          </label>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
          <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-base font-bold rounded-lg shadow-lg transition-all duration-300">
            <i class="fa-solid fa-paper-plane text-lg"></i>
            <span>Teklif Gönder</span>
          </button>
          <button type="button" @click="showForm = false" class="px-6 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white font-bold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300">
            İptal
          </button>
        </div>
      </form>

      <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
          <i class="fa-solid fa-lock text-green-600 dark:text-green-400"></i>
          Bilgileriniz güvende. Sadece fiyat teklifi için kullanılacaktır.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4 text-sm">
          <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <i class="fa-solid fa-shield-check text-green-600"></i>
            <span>SSL Korumalı</span>
          </div>
          <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <i class="fa-solid fa-user-shield text-blue-600"></i>
            <span>KVKK Uyumlu</span>
          </div>
          <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <i class="fa-solid fa-clock text-purple-600"></i>
            <span>30 Dk Cevap</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SSS - GENİŞLETİLDİ -->
<section class="py-12">
  <div class="container mx-auto px-4 max-w-3xl">
    <div class="text-center mb-8">
      <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-2">
        Sıkça Sorulan Sorular
      </h2>
      <p class="text-gray-600 dark:text-gray-400 text-sm">Merak ettiğiniz her şey burada</p>
    </div>

    <div class="space-y-3">
      <!-- SSS 1 -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Elektrikli transpalet avantajları neler?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 1 }"></i>
        </button>
        <div x-show="activeAccordion === 1" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          <strong>Manuel güç gerektirmez</strong>, ağır yükleri kolay taşır, <strong>iş gücü maliyetini %40'a kadar düşürür</strong>. Lityum batarya ile 8 saat kesintisiz çalışma. Üretim hızını artırır, çalışan yorgunluğunu azaltır.
        </div>
      </div>

      <!-- SSS 2 -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 2 ? null : 2" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Hangi model bana uygun?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 2 }"></i>
        </button>
        <div x-show="activeAccordion === 2" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          <strong>Hafif kullanım:</strong> F4 Lityum İyon (1500 kg)<br>
          <strong>Kantar ihtiyacı:</strong> F4 Terazili (ağırlık ölçüm)<br>
          <strong>Ağır yükler:</strong> F4 201 (2000 kg) veya EPL 154<br>
          Detaylı danışmanlık için <strong>0216 755 3 555</strong> numaralı telefondan bize ulaşabilirsiniz.
        </div>
      </div>

      <!-- SSS 3 -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 3 ? null : 3" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Pil ömrü ne kadar? Şarj süresi nedir?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 3 }"></i>
        </button>
        <div x-show="activeAccordion === 3" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          Lityum iyon piller <strong>2000+ şarj döngüsü</strong> ile 3-5 yıl kullanılabilir. <strong>Hızlı şarj:</strong> 2-4 saat tam dolu. Çıkarılabilir pil sisteminde yedek pil ile kesintisiz çalışma.
        </div>
      </div>

      <!-- SSS 4 - GARANTİ GÜNCELLENDİ -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 4 ? null : 4" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Garanti kapsamı nedir?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 4 }"></i>
        </button>
        <div x-show="activeAccordion === 4" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          <strong>1 yıl ürün, 2 yıl akü garantisi.</strong> Tüm mekanik ve elektronik arızalar garanti kapsamında. Yedek parça desteği. 7/24 teknik destek hattı aktif.
        </div>
      </div>

      <!-- SSS 5 - YENİ -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 5 ? null : 5" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Teslimat süresi ne kadar?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 5 }"></i>
        </button>
        <div x-show="activeAccordion === 5" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          <strong>Stoklu ürünler 1-2 iş günü</strong> içinde teslim edilir. İstanbul ve çevre illerde aynı gün teslimat imkanı. Diğer şehirlere kargo ile 2-3 iş günü. Kurulum ve eğitim teslimat sırasında yapılır.
        </div>
      </div>

      <!-- SSS 6 - YENİ -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 6 ? null : 6" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Ödeme seçenekleri nelerdir?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 6 }"></i>
        </button>
        <div x-show="activeAccordion === 6" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          <strong>Nakit, kredi kartı, banka transferi ve taksitli ödeme</strong> seçenekleri mevcut. Kurumsal müşteriler için <strong>vadeli ödeme imkanı</strong>. Finansal kiralama (leasing) işlemleri yapılabilir.
        </div>
      </div>

      <!-- SSS 7 - YENİ -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 7 ? null : 7" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Bakım ve servis hizmeti var mı?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 7 }"></i>
        </button>
        <div x-show="activeAccordion === 7" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          <strong>Periyodik bakım hizmeti</strong> sunuyoruz. Yıllık bakım anlaşmaları mevcuttur. Acil arıza durumunda <strong>24 saat içinde yerinde müdahale</strong>. Tüm yedek parçalar stokta.
        </div>
      </div>

      <!-- SSS 8 - YENİ -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 8 ? null : 8" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">İkinci el transpalet değişimi yapıyor musunuz?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 8 }"></i>
        </button>
        <div x-show="activeAccordion === 8" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          Evet, <strong>eski transpaletinizi değerinde alıyoruz</strong>. Marka ve model fark etmeksizin uygun fiyat teklifi veriyoruz. Detaylı bilgi için <strong>0216 755 3 555</strong> numaralı telefondan bize ulaşın.
        </div>
      </div>

      <!-- SSS 9 - YENİ -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 9 ? null : 9" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Operatör eğitimi veriliyor mu?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 9 }"></i>
        </button>
        <div x-show="activeAccordion === 9" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          <strong>Teslimat sırasında ücretsiz temel eğitim</strong> veriyoruz. Güvenli kullanım, şarj işlemleri, bakım konularında personel eğitimi. Detaylı video eğitim materyalleri.
        </div>
      </div>

      <!-- SSS 10 - YENİ -->
      <div class="backdrop-blur-sm bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <button @click="activeAccordion = activeAccordion === 10 ? null : 10" class="w-full flex justify-between items-center p-5 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <span class="font-bold text-base text-gray-900 dark:text-white pr-4">Yedek akü satın alabilir miyim?</span>
          <i class="fa-solid fa-chevron-down text-blue-600 flex-shrink-0 transition-transform text-lg" :class="{ 'rotate-180': activeAccordion === 10 }"></i>
        </button>
        <div x-show="activeAccordion === 10" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-400 text-sm">
          Evet, <strong>tüm modeller için yedek akü</strong> satışı yapıyoruz. Çıkarılabilir pil sisteminde ikinci akü ile <strong>kesintisiz 16 saat çalışma</strong> imkanı. Yedek akü fiyatları için iletişime geçin.
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA - SADELEŞTIRILDI -->
<section class="py-12 bg-gray-900 dark:bg-gray-950">
  <div class="container mx-auto px-4 max-w-4xl text-center">
    <h2 class="text-3xl md:text-4xl font-black text-white mb-4">
      Şimdi Sipariş Verin,<br>Kampanyadan Yararlanın!
    </h2>
    <p class="text-lg text-gray-300 mb-6 max-w-2xl mx-auto">
      Stoklar sınırlı. <strong class="text-white">Bugün sipariş, hemen teslimat.</strong> 1 yıl ürün + 2 yıl akü garantisi dahil!
    </p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
      <a href="tel:02167553555" class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white hover:bg-gray-100 text-gray-900 text-lg font-bold rounded-lg shadow-lg transition-all duration-300 no-underline">
        <i class="fa-solid fa-phone-volume text-xl"></i>
        <span>0216 755 3 555</span>
      </a>
      <a href="{{ whatsapp_link() }}" class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white hover:bg-gray-100 text-gray-900 text-lg font-bold rounded-lg shadow-lg transition-all duration-300 no-underline">
        <i class="fa-brands fa-whatsapp text-2xl"></i>
        <span>WhatsApp</span>
      </a>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-6 text-gray-300 text-sm">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-shield-check text-xl"></i>
        <span class="font-semibold">1+2 Yıl Garanti</span>
      </div>
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-truck-fast text-xl"></i>
        <span class="font-semibold">Hızlı Teslimat</span>
      </div>
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-headset text-xl"></i>
        <span class="font-semibold">7/24 Destek</span>
      </div>
    </div>
  </div>
</section>

</div>
