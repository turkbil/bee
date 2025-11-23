# Muzibu Anasayfa Layout Planlama (Üye / Üye Olmayan)

**Tarih:** 2025-11-13
**Referans:** muzibu.com anasayfa analizi
**Hedef:** V1 Muzibu Tailwind tasarımına üye/üye olmayan ayrımı eklemek

---

## 📊 Muzibu.com Analiz Sonuçları

### 🔍 Mevcut Yapı (Guest User için):

**1. Hero Section (Sol Kolon - col-xl-6):**
- Search Bar
- "Yeni Çıkan" büyük featured card (800x410px)
  - Başlık + Açıklama
  - "Oku" CTA button

**2. Yeni Çıkanlar (Sağ Kolon - col-xl-6):**
- Başlık: "Yeni Çıkanlar"
- CTA Buttons: "Giriş Yap" + "Üye Ol" (sağ üstte)
- Song List (5 adet):
  - Sıra numarası
  - 80x80px cover image
  - Şarkı adı + Sanatçı
  - Play button
  - Favorilere ekle icon
  - Playlist'e ekle icon

**3. Son Eklenen Oynatma Listeleri:**
- Başlık: "Son Eklenen Oynatma Listeleri"
- Slick Slider (Horizontal carousel)
- Card'lar: 410x410px square image + title overlay

**4. Navigation Menu (Sidebar):**
- Ana Sayfa
- Oynatma Listeleri
- Favoriler (Login gerektir)
- Albümler
- Türler
- Sektörler
- Radyolar
- Fiyatlandırma
- **ÜYE OL** (highlighted button)
- **GİRİŞ YAP** (secondary button)

---

## 🎯 BİZİM ANASAYFA İÇİN YENİ PLANLAMA

### ✅ ÜYELER İÇİN (isLoggedIn = true)

Mevcut tasarımımız üyeler için zaten hazır:

1. **Hero Slider** (Fullwidth)
   - 3-5 slide
   - Featured albums/playlists

2. **Şarkı Listeleri** (2 Kolon - grid-cols-2)
   - Sol: Yeni Çıkan Şarkılar (10 adet)
   - Sağ: Popüler Şarkılar (10 adet)

3. **Albümler + Playlists** (Row by Row)
   - Her row: 1 Albüm + 1 Playlist
   - 5 row toplam
   - Kare card design

4. **Pricing Section** → GOSTER (x-show="!isLoggedIn")
   - Fiyat planları
   - CTA'lar

---

### 🆕 ÜYE OLMAYANLARA (isLoggedIn = false)

**TAMAMEN YENİ LAYOUT:**

#### 1️⃣ HERO SECTION (Featured Billboard)

**Tasarım:**
```html
<!-- Fullwidth centered hero -->
<section class="px-8 pt-12 pb-16 text-center">
    <div class="max-w-4xl mx-auto">
        <!-- Ana Başlık -->
        <div class="mb-6">
            <div class="flex items-center justify-center gap-3 mb-4">
                <span class="bg-muzibu-purple/20 text-muzibu-purple px-4 py-1.5 rounded-full text-sm font-semibold">
                    MSG Lisanslı
                </span>
                <span class="bg-green-500/20 text-green-400 px-4 py-1.5 rounded-full text-sm font-semibold">
                    100% Yasal
                </span>
                <span class="bg-blue-500/20 text-blue-400 px-4 py-1.5 rounded-full text-sm font-semibold">
                    İşletmeniz İçin
                </span>
            </div>

            <h1 class="text-5xl font-bold mb-4 text-white">
                Yasal & Telifsiz Müzik
            </h1>

            <p class="text-xl text-gray-300 mb-8">
                25.000+ telifsiz şarkı. Telif cezalarından kurtulun,
                müşterilerinize keyifli bir atmosfer sunun.
            </p>
        </div>

        <!-- İstatistikler -->
        <div class="flex items-center justify-center gap-12 mb-8">
            <div class="text-center">
                <div class="text-4xl font-bold text-muzibu-purple mb-1">25K+</div>
                <div class="text-sm text-gray-400">Şarkı</div>
            </div>
            <div class="w-px h-12 bg-white/10"></div>
            <div class="text-center">
                <div class="text-4xl font-bold text-muzibu-purple mb-1">5K+</div>
                <div class="text-sm text-gray-400">İşletme</div>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex items-center justify-center gap-4">
            <a href="/kayit" class="bg-muzibu-purple hover:bg-muzibu-purple/90 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all shadow-lg hover:shadow-xl">
                Ücretsiz Deneyin
            </a>
            <a href="/planlar" class="bg-white/10 hover:bg-white/20 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all border border-white/20">
                Planları İncele
            </a>
        </div>
    </div>
</section>
```

**Özellikler:**
- ✅ Centered layout (max-w-4xl)
- ✅ Badge'ler: MSG Lisanslı, 100% Yasal, İşletmeniz İçin
- ✅ Ana başlık: "Yasal & Telifsiz Müzik"
- ✅ Açıklama: Telif cezalarından bahseden copy
- ✅ İstatistikler: 25K+ Şarkı, 5K+ İşletme (büyük rakamlar)
- ✅ CTA: "Ücretsiz Deneyin" (primary) + "Planları İncele" (secondary)

---

#### 2️⃣ DEMO PLAYER SECTION (Önizleme Şarkıları)

**Tasarım:**
```html
<!-- Yeni Çıkan Şarkılar - Demo (Guest) -->
<section class="px-8 py-12">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">🎵 Yeni Çıkan Şarkılar</h2>
        <p class="text-gray-400">Demo olarak 5 şarkı dinleyin, beğendiyseniz üye olun!</p>
    </div>

    <div class="max-w-3xl mx-auto space-y-3">
        <!-- Song Card Template -->
        <template x-for="(song, index) in songs.slice(0, 5)" :key="index">
            <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 transition-all group border border-white/5">
                <!-- Play Button + Index -->
                <button class="w-12 h-12 bg-muzibu-purple/40 rounded-xl flex items-center justify-center group-hover:bg-muzibu-purple transition-all">
                    <i class="fas fa-play text-white text-sm ml-0.5"></i>
                </button>

                <!-- Cover -->
                <img :src="song.cover" class="w-14 h-14 rounded-lg shadow-lg">

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <h6 class="font-bold text-white truncate" x-text="song.title"></h6>
                    <p class="text-sm text-gray-400 truncate" x-text="song.artist"></p>
                </div>

                <!-- Duration -->
                <span class="text-sm text-gray-400" x-text="song.duration"></span>

                <!-- Demo Badge -->
                <div class="bg-yellow-400/10 text-yellow-400 px-3 py-1 rounded-lg text-xs font-semibold">
                    DEMO
                </div>
            </div>
        </template>
    </div>

    <!-- CTA after songs -->
    <div class="text-center mt-8">
        <p class="text-gray-400 mb-4">Tüm şarkıları dinlemek için üye olun</p>
        <a href="/kayit" class="inline-flex items-center gap-2 bg-muzibu-purple hover:bg-muzibu-purple/90 text-white px-6 py-3 rounded-xl font-semibold transition-all">
            Ücretsiz Üye Ol
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>
```

**Özellikler:**
- ✅ Sadece 5 şarkı göster (slice(0, 5))
- ✅ Compact list format
- ✅ "DEMO" badge her şarkıda
- ✅ Alt tarafta CTA: "Tüm şarkıları dinlemek için üye olun"
- ✅ Merkezi layout (max-w-3xl)

---

#### 3️⃣ FEATURED PLAYLISTS (Teaser Playlists)

**Tasarım:**
```html
<!-- Öne Çıkan Playlistler (Guest) -->
<section class="px-8 py-12 bg-white/5">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">🎧 Popüler Playlistler</h2>
        <p class="text-gray-400">İşletmenize özel hazırlanmış playlistler</p>
    </div>

    <div class="grid grid-cols-4 gap-6 max-w-6xl mx-auto">
        <!-- Playlist Card (6 adet) -->
        <template x-for="i in 6" :key="i">
            <a href="#" class="group relative bg-white/5 hover:bg-white/10 p-4 rounded-xl transition-all border border-white/5">
                <!-- Blur Overlay (Locked) -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all z-10">
                    <div class="text-center">
                        <i class="fas fa-lock text-3xl text-white mb-2"></i>
                        <p class="text-sm text-white font-semibold">Üye Olun</p>
                    </div>
                </div>

                <img :src="`https://picsum.photos/seed/playlist${i}/300`" class="w-full aspect-square rounded-lg shadow-lg mb-3">
                <h4 class="font-bold text-white truncate" x-text="`Playlist ${i}`"></h4>
                <p class="text-sm text-gray-400 truncate">42 şarkı</p>
            </a>
        </template>
    </div>
</section>
```

**Özellikler:**
- ✅ 6 playlist card (grid-cols-4, 2 satır)
- ✅ Hover'da blur + lock icon
- ✅ "Üye Olun" mesajı
- ✅ Teaser olarak sadece cover + title
- ✅ Tıklanamaz (CTA yönlendir)

---

#### 4️⃣ FEATURES SECTION (Neden Muzibu?)

**Tasarım:**
```html
<!-- Neden Muzibu? -->
<section class="px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-bold mb-4">Neden Muzibu?</h2>
        <p class="text-xl text-gray-400">İşletmeniz için en iyi müzik çözümü</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <!-- Feature Card 1 -->
        <div class="text-center p-8 rounded-xl bg-white/5 border border-white/10">
            <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-certificate text-green-400 text-2xl"></i>
            </div>
            <h4 class="text-xl font-bold mb-2">MSG Lisanslı</h4>
            <p class="text-gray-400">Tüm şarkılarımız MSG lisanslı, telif cezası yok!</p>
        </div>

        <!-- Feature Card 2 -->
        <div class="text-center p-8 rounded-xl bg-white/5 border border-white/10">
            <div class="w-16 h-16 bg-muzibu-purple/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-music text-muzibu-purple text-2xl"></i>
            </div>
            <h4 class="text-xl font-bold mb-2">25.000+ Şarkı</h4>
            <p class="text-gray-400">Her tür işletme için geniş müzik kütüphanesi</p>
        </div>

        <!-- Feature Card 3 -->
        <div class="text-center p-8 rounded-xl bg-white/5 border border-white/10">
            <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-store text-blue-400 text-2xl"></i>
            </div>
            <h4 class="text-xl font-bold mb-2">5.000+ İşletme</h4>
            <p class="text-gray-400">Binlerce işletme bize güveniyor</p>
        </div>
    </div>
</section>
```

**Özellikler:**
- ✅ 3 kolon feature cards
- ✅ Icon + Başlık + Açıklama
- ✅ MSG Lisans vurgusu
- ✅ Şarkı ve işletme sayısı

---

#### 5️⃣ PRICING SECTION (Fiyat Planları)

**Tasarım:**
```html
<!-- Pricing (Guest only) -->
<section x-show="!isLoggedIn" x-transition class="px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-bold mb-4">Size Uygun Planı Seçin</h2>
        <p class="text-xl text-gray-400">Tüm planlarda esnek iptal seçeneği</p>
    </div>

    <!-- Mevcut pricing grid -->
    <div class="grid md:grid-cols-4 gap-8 max-w-7xl mx-auto">
        <!-- Pricing cards (zaten mevcut) -->
    </div>
</section>
```

**Özellikler:**
- ✅ Sadece guest'lere göster (x-show="!isLoggedIn")
- ✅ Mevcut pricing tasarımını koru
- ✅ 4 kolon: Deneme, Aylık, Yıllık, Kurumsal

---

## 🔀 LAYOUT KARŞILAŞTIRMA

### ÜYELER (isLoggedIn = true):

```
1. Hero Slider (Fullwidth)
2. Şarkı Listeleri (2 kolon: Yeni + Popüler)
3. Albümler + Playlists (Row by Row)
4. [Pricing GİZLİ]
```

### ÜYE OLMAYANLAR (isLoggedIn = false):

```
1. Hero Billboard (MSG Lisanslı, 25K+ şarkı vurgusu)
2. Demo Şarkılar (5 adet, DEMO badge)
3. Featured Playlists (6 adet, locked hover)
4. Neden Muzibu? (3 feature cards)
5. Pricing (4 plan)
```

---

## 📋 İMPLEMENTASYON ADIMLARI

### 1️⃣ index.html Güncellemesi

**Mevcut yapı:**
```html
<!-- Hero Slider -->
<section x-show="isLoggedIn"></section>

<!-- Şarkı Listeleri (2 kolon) -->
<section></section>

<!-- Albümler + Playlists (Row by Row) -->
<section x-show="isLoggedIn"></section>

<!-- Pricing -->
<section x-show="!isLoggedIn"></section>
```

**Yeni yapı:**
```html
<!-- ÜYELER İÇİN -->
<template x-if="isLoggedIn">
    <div>
        <!-- Hero Slider -->
        <section>...</section>

        <!-- Şarkı Listeleri -->
        <section>...</section>

        <!-- Albümler + Playlists -->
        <section>...</section>
    </div>
</template>

<!-- ÜYE OLMAYANLAR İÇİN -->
<template x-if="!isLoggedIn">
    <div>
        <!-- Hero Billboard -->
        <section>...</section>

        <!-- Demo Şarkılar -->
        <section>...</section>

        <!-- Featured Playlists -->
        <section>...</section>

        <!-- Neden Muzibu? -->
        <section>...</section>

        <!-- Pricing -->
        <section>...</section>
    </div>
</template>
```

### 2️⃣ Alpine.js Data Updates

```javascript
// isLoggedIn state kontrolü zaten var
// Demo şarkı limiti için helper
songs: [], // Mevcut
guestSongLimit: 5, // Yeni: Guest'ler için limit
```

### 3️⃣ Stil Güncellemeleri

- Hero billboard için centered layout
- Feature cards için icon styles
- Locked playlist overlay için blur effect
- Badge styles (DEMO, MSG Lisanslı, vb.)

---

## ✅ KALİTE KONTROLLERİ

1. **x-show vs x-if Kullanımı:**
   - `x-show`: DOM'da var ama gizli (toggle sık ise)
   - `x-if`: DOM'da yok (büyük section'lar için)
   - **Karar:** `x-if` kullan (büyük section'lar, SEO için)

2. **Responsive Design:**
   - Hero: Mobile'da tek kolon
   - Stats: Mobile'da küçült
   - Playlists: Mobile'da grid-cols-2
   - Features: Mobile'da tek kolon

3. **Performance:**
   - Demo şarkılar: Sadece 5 adet (slice)
   - Playlists: Sadece 6 adet (limit)
   - Images: Lazy loading

4. **SEO:**
   - Hero'da H1: "Yasal & Telifsiz Müzik"
   - Feature cards'da semantik HTML
   - Meta tags: İşletme odaklı keywords

---

## 🎨 TASARIM PRENSİPLERİ

### Renkler:
- **Primary:** Muzibu Purple (#8B5CF6 / muzibu-purple)
- **Success:** Green (#22C55E) - MSG Lisanslı
- **Info:** Blue (#3B82F6) - İşletme badge
- **Warning:** Yellow (#FBBF24) - DEMO badge
- **Background:** Dark (#0A0E27 / muzibu-dark)

### Typography:
- **H1:** 5xl (48px) - Hero başlık
- **H2:** 4xl (36px) - Section başlıklar
- **H3:** 3xl (30px) - Alt başlıklar
- **Body:** Base (16px) - Normal text
- **Small:** sm (14px) - Metadata

### Spacing:
- **Section padding:** py-16 (64px)
- **Card padding:** p-8 (32px)
- **Gap:** gap-8 (32px)

---

## 🚀 DEĞERLENDİRME

**Kullanıcı Feedback:**
> "üye olmayanlara MSG Lisanslı, 100% Yasal, İşletmeniz İçin, Yasal & Telifsiz Müzik, 25.000+ telifsiz şarkı. Telif cezalarından kurtulun, müşterilerinize keyifli bir atmosfer sunun. 25K+ Şarkı, 5K+ İşletme ilk böyle bir şey göstermen güzel olackatır"

**Planlama Sonucu:**
✅ Tüm istekler karşılandı:
- MSG Lisanslı badge
- 100% Yasal badge
- İşletmeniz İçin badge
- Ana başlık: "Yasal & Telifsiz Müzik"
- Telif cezası vurgusu
- 25K+ Şarkı stat
- 5K+ İşletme stat
- Hero section'da prominent placement

**Sonraki Adım:**
1. Kullanıcı onayı al
2. index.html'e implement et
3. Test et (üye/üye olmayan switch)
4. Cache+Build
5. Production deploy

---

**Döküman Sonu**
