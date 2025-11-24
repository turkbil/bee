# Muzibu Platform - UI Components & SPA Implementation

**Date:** 2025-11-24 21:23
**Tenant:** 1001 (muzibu.com.tr)
**Priority:** CRITICAL
**Status:** In Progress

---

## 📋 Overview

Muzibu kurumsal müzik platformu için ana sayfa, persistent audio player ve SPA routing sistemi geliştirme.

**Ana Hedef:**
- Sayfa değişmeden çalışan single page application
- Alt kısımda sabit duran audio player
- Multiple design alternatives (header, footer, player, main content)
- Component-based modular architecture
- Dynamic styling (player color based on album art)
- Smooth animations without layout shift

---

## 🎯 Phase 1: Core Infrastructure

### 1.1 Alpine.js SPA Router Setup
**File:** `resources/views/themes/muzibu/layouts/app.blade.php`

- [ ] Create main layout structure
- [ ] Implement Alpine.js global store
- [ ] Setup custom router function
- [ ] History API integration (pushState/popState)
- [ ] Route definition system
- [ ] Page component registration

**Technical Details:**
```javascript
Alpine.store('router', {
    currentRoute: 'home',
    params: {},
    query: {},
    navigate(route, params = {}) {
        this.currentRoute = route;
        this.params = params;
        history.pushState({ route, params }, '', this.buildUrl(route, params));
        // Dispatch route change event
    },
    buildUrl(route, params) {
        // Route to URL mapping
    }
});

// Listen to browser back/forward
window.addEventListener('popstate', (e) => {
    Alpine.store('router').currentRoute = e.state?.route || 'home';
});
```

### 1.2 Global State Management
**File:** `resources/views/themes/muzibu/layouts/stores.blade.php`

- [ ] Player store (currentSong, queue, isPlaying, progress, volume, etc.)
- [ ] User store (auth, subscription, preferences)
- [ ] UI store (sidebar open/closed, theme, etc.)
- [ ] Playlist store (active playlist, shuffle, repeat)

**Technical Details:**
```javascript
Alpine.store('player', {
    currentSong: null,
    queue: [],
    history: [],
    isPlaying: false,
    isPaused: false,
    progress: 0,
    duration: 0,
    volume: 80,
    isMuted: false,
    shuffle: false,
    repeat: 'off', // off, one, all
    eq: { bass: 0, mid: 0, treble: 0 },

    play(song) {
        this.currentSong = song;
        this.isPlaying = true;
        // Trigger audio element play
    },

    pause() {
        this.isPlaying = false;
        this.isPaused = true;
    },

    next() {
        // Get next song from queue
    },

    previous() {
        // Get previous song from history
    }
});
```

---

## 🎨 Phase 2: Design Components

### 2.1 Header Alternatives (3+ versions)

#### Header v1: Classic Top Bar
**File:** `resources/views/themes/muzibu/components/headers/v1.blade.php`

- [ ] Logo left
- [ ] Search bar center
- [ ] User menu right
- [ ] Sticky on scroll
- [ ] Responsive mobile menu

**CSS Classes:** `.header-v1`, `.header-v1__logo`, `.header-v1__search`, `.header-v1__user`

#### Header v2: Sidebar Left Navigation
**File:** `resources/views/themes/muzibu/components/headers/v2.blade.php`

- [ ] Fixed left sidebar (250px)
- [ ] Logo top
- [ ] Navigation menu vertical
- [ ] Collapsible on mobile
- [ ] Active route highlighting

**CSS Classes:** `.header-v2`, `.sidebar`, `.sidebar__nav`, `.sidebar__item`, `.sidebar__item--active`

#### Header v3: Minimal Top + Quick Actions
**File:** `resources/views/themes/muzibu/components/headers/v3.blade.php`

- [ ] Minimal height (60px)
- [ ] Hamburger menu left
- [ ] Quick action buttons right
- [ ] Slide-in drawer menu
- [ ] Glass morphism effect

**CSS Classes:** `.header-v3`, `.header-v3__drawer`, `.header-v3__quick-actions`

**Navigation Items:**
- Home / Ana Sayfa
- Browse / Gözat (Songs, Albums, Artists, Playlists, Radios, Genres, Sectors)
- My Library / Kütüphanem (Favorites, Playlists, History)
- Subscription / Üyelik (only if not subscribed)
- Account / Hesap (Settings, Devices, Logout)

**Auth States:**
- Logged in: Show full menu + user avatar
- Guest: Show "Login" + "Sign Up" buttons

### 2.2 Footer Alternatives (3+ versions)

#### Footer v1: Full Width Multi Column
**File:** `resources/views/themes/muzibu/components/footers/v1.blade.php`

- [ ] 4 columns (About, Quick Links, Sectors, Contact)
- [ ] Social media icons
- [ ] Copyright text
- [ ] Newsletter signup

**CSS Classes:** `.footer-v1`, `.footer-v1__column`, `.footer-v1__links`, `.footer-v1__social`

#### Footer v2: Minimal Single Row
**File:** `resources/views/themes/muzibu/components/footers/v2.blade.php`

- [ ] Logo left
- [ ] Links center
- [ ] Copyright right
- [ ] Fixed height (80px)

**CSS Classes:** `.footer-v2`, `.footer-v2__logo`, `.footer-v2__links`, `.footer-v2__copyright`

#### Footer v3: Compact with Player Offset
**File:** `resources/views/themes/muzibu/components/footers/v3.blade.php`

- [ ] Padding bottom for player (90px)
- [ ] 2 columns only
- [ ] Essential links only
- [ ] Mobile friendly

**CSS Classes:** `.footer-v3`, `.footer-v3--with-player`

### 2.3 Main Content Alternatives (3+ versions)

#### Main v1: Grid Layout (Spotify-like)
**File:** `resources/views/themes/muzibu/components/mains/v1.blade.php`

- [ ] Section headers
- [ ] Horizontal scroll carousels
- [ ] Card grid (album/playlist cards)
- [ ] Lazy loading
- [ ] Skeleton loaders

**Sections:**
- Featured / Öne Çıkanlar
- New Releases / Yeni Çıkanlar
- Popular This Week / Bu Hafta Popüler
- Recommended Playlists / Önerilenler
- Browse by Genre / Türlere Göre
- Browse by Sector / Sektörlere Göre

**CSS Classes:** `.main-v1`, `.section`, `.carousel`, `.card-grid`, `.card`

#### Main v2: List View (Apple Music-like)
**File:** `resources/views/themes/muzibu/components/mains/v2.blade.php`

- [ ] Large hero section
- [ ] List-based song display
- [ ] Album art thumbnails
- [ ] Play button on hover
- [ ] Contextual actions (favorite, add to playlist)

**CSS Classes:** `.main-v2`, `.hero`, `.song-list`, `.song-item`, `.song-actions`

#### Main v3: Category Tiles
**File:** `resources/views/themes/muzibu/components/mains/v3.blade.php`

- [ ] Large category tiles (4x4 grid)
- [ ] Genre colors
- [ ] Hover effects
- [ ] Background images
- [ ] Click to navigate

**CSS Classes:** `.main-v3`, `.category-grid`, `.category-tile`, `.category-tile--hover`

#### Main v4: Radio Station Focus
**File:** `resources/views/themes/muzibu/components/mains/v4.blade.php`

- [ ] Radio stations grid
- [ ] Live indicator
- [ ] Sector badges
- [ ] One-click play
- [ ] Featured radio hero

**CSS Classes:** `.main-v4`, `.radio-grid`, `.radio-card`, `.radio-badge`, `.live-indicator`

#### Main v5: Hybrid Dashboard
**File:** `resources/views/themes/muzibu/components/mains/v5.blade.php`

- [ ] Mix of grid + list
- [ ] Quick actions top
- [ ] Recently played section
- [ ] Jump back in (resume playback)
- [ ] Personalized recommendations

**CSS Classes:** `.main-v5`, `.dashboard`, `.quick-actions`, `.recently-played`, `.recommendations`

**Main Menu States:**

**Guest User:**
- Browse all content (read-only)
- "Play" button → Show login modal
- Limited preview (30 seconds)

**Logged In User:**
- Full playback access
- Personal library visible
- Create playlists
- Subscription status banner (if not subscribed)

### 2.4 Audio Player Alternatives (3+ versions)

#### Player v1: Full Width Bottom Bar
**File:** `resources/views/themes/muzibu/components/players/v1.blade.php`

- [ ] Fixed bottom (height: 90px)
- [ ] Album art left (80x80)
- [ ] Song info + controls center
- [ ] Volume + queue right
- [ ] Progress bar top edge
- [ ] EQ button (opens modal)

**Controls:**
- Previous, Play/Pause, Next
- Shuffle, Repeat
- Progress bar (scrubbing)
- Volume slider
- Queue list toggle
- Favorite button

**CSS Classes:** `.player-v1`, `.player-v1__artwork`, `.player-v1__controls`, `.player-v1__volume`, `.player-v1__progress`

#### Player v2: Compact Mini Player
**File:** `resources/views/themes/muzibu/components/players/v2.blade.php`

- [ ] Fixed bottom right (width: 400px)
- [ ] Rounded corners
- [ ] Expandable (click to full width)
- [ ] Minimalistic design
- [ ] Hide when inactive (auto-hide after 10s)

**CSS Classes:** `.player-v2`, `.player-v2--minimized`, `.player-v2--expanded`, `.player-v2__toggle`

#### Player v3: Sidebar Player
**File:** `resources/views/themes/muzibu/components/players/v3.blade.php`

- [ ] Fixed right sidebar (width: 300px)
- [ ] Full height player
- [ ] Queue visible by default
- [ ] Large album art
- [ ] Lyrics display (scrollable)

**CSS Classes:** `.player-v3`, `.player-v3__queue`, `.player-v3__lyrics`, `.player-v3__artwork-large`

#### Player v4: Dynamic Color Theme Player
**File:** `resources/views/themes/muzibu/components/players/v4.blade.php`

- [ ] Extract dominant color from album art (Vibrant.js)
- [ ] Apply gradient background
- [ ] Text color auto-adjust (contrast ratio)
- [ ] Smooth color transitions (CSS transitions)
- [ ] Glassmorphism effect

**Technical Implementation:**
```javascript
// Extract color from album art
const vibrant = new Vibrant(albumArtUrl);
vibrant.getPalette().then((palette) => {
    const dominantColor = palette.Vibrant.hex;
    const lightColor = palette.LightVibrant.hex;

    // Apply to player
    playerElement.style.background = `linear-gradient(135deg, ${dominantColor}, ${lightColor})`;

    // Adjust text color for contrast
    const textColor = getContrastColor(dominantColor);
    playerElement.style.color = textColor;
});
```

**CSS Classes:** `.player-v4`, `.player-v4--dynamic`, `.player-v4__gradient`

**Player Features (All Versions):**
- HTML5 Audio element
- HLS.js integration (for HLS streams)
- Web Audio API (for EQ)
- Media Session API (OS-level controls)
- Keyboard shortcuts (Space, Arrow keys)
- Preloading next song
- Crossfade support (optional)
- Volume normalization (Web Audio API GainNode)

---

## 🔧 Phase 3: Technical Implementation

### 3.1 Component Registration System
**File:** `resources/views/themes/muzibu/layouts/component-loader.blade.php`

- [ ] Create Blade component loader
- [ ] Version switcher (URL param or localStorage)
- [ ] Dynamic component inclusion
- [ ] CSS/JS isolation per component

**Usage Example:**
```blade
{{-- Load header version from config/query param --}}
<x-muzibu-header version="{{ request('header', 'v2') }}" />

{{-- Load main content --}}
<x-muzibu-main version="{{ request('main', 'v5') }}" />

{{-- Load player --}}
<x-muzibu-player version="{{ request('player', 'v4') }}" />

{{-- Load footer --}}
<x-muzibu-footer version="{{ request('footer', 'v1') }}" />
```

### 3.2 CSS Architecture
**File Structure:**
```
resources/css/themes/muzibu/
├── base/
│   ├── reset.css
│   ├── typography.css
│   └── variables.css (colors, spacing, breakpoints)
├── components/
│   ├── headers/
│   │   ├── v1.css
│   │   ├── v2.css
│   │   └── v3.css
│   ├── footers/
│   │   ├── v1.css
│   │   ├── v2.css
│   │   └── v3.css
│   ├── mains/
│   │   ├── v1.css
│   │   ├── v2.css
│   │   ├── v3.css
│   │   ├── v4.css
│   │   └── v5.css
│   ├── players/
│   │   ├── v1.css
│   │   ├── v2.css
│   │   ├── v3.css
│   │   └── v4.css
│   └── shared/
│       ├── buttons.css
│       ├── cards.css
│       └── inputs.css
└── layout.css (grid system, container)
```

**CSS Guidelines:**
- BEM naming convention (`.block__element--modifier`)
- Scoped styles per component version
- CSS custom properties for theming
- No layout shift (fixed dimensions where needed)
- Smooth transitions (max 300ms)
- GPU-accelerated animations (transform, opacity only)

### 3.3 JavaScript Architecture
**File Structure:**
```
resources/js/themes/muzibu/
├── stores/
│   ├── router.js
│   ├── player.js
│   ├── user.js
│   └── ui.js
├── components/
│   ├── audio-player.js (HTML5 Audio wrapper)
│   ├── hls-player.js (HLS.js integration)
│   ├── eq-processor.js (Web Audio API EQ)
│   └── color-extractor.js (Vibrant.js wrapper)
├── utils/
│   ├── preloader.js (song preloading)
│   ├── keyboard.js (keyboard shortcuts)
│   └── media-session.js (OS controls)
└── app.js (main entry point)
```

### 3.4 HLS Integration
**File:** `resources/js/themes/muzibu/components/hls-player.js`

- [ ] Detect HLS support (check `muzibu_songs.hls_enabled`)
- [ ] Load HLS.js library (CDN or local)
- [ ] Fallback to native audio for non-HLS
- [ ] Buffer management (maxBufferLength: 30s)
- [ ] Quality switching (ABR - Adaptive Bitrate)
- [ ] Error handling (retry logic)

**Implementation:**
```javascript
class HLSPlayer {
    constructor(audioElement) {
        this.audio = audioElement;
        this.hls = null;
    }

    load(song) {
        if (song.hls_enabled && Hls.isSupported()) {
            this.loadHLS(song.hls_url);
        } else {
            this.loadNative(song.file_path);
        }
    }

    loadHLS(url) {
        if (this.hls) this.hls.destroy();

        this.hls = new Hls({
            maxBufferLength: 30,
            maxMaxBufferLength: 60,
            enableWorker: true,
        });

        this.hls.loadSource(url);
        this.hls.attachMedia(this.audio);

        this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
            this.audio.play();
        });
    }

    loadNative(url) {
        this.audio.src = url;
        this.audio.load();
        this.audio.play();
    }
}
```

### 3.5 Volume Normalization
**File:** `resources/js/themes/muzibu/components/eq-processor.js`

- [ ] Create AudioContext
- [ ] Create GainNode for volume normalization
- [ ] Create BiquadFilterNode for EQ (bass, mid, treble)
- [ ] Connect: source → gainNode → eq → destination
- [ ] Per-song gain value from database (`muzibu_songs.normalized_gain`)

**Implementation:**
```javascript
class EQProcessor {
    constructor() {
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.source = null;
        this.gainNode = this.audioContext.createGain();
        this.bassFilter = this.audioContext.createBiquadFilter();
        this.midFilter = this.audioContext.createBiquadFilter();
        this.trebleFilter = this.audioContext.createBiquadFilter();

        // Setup filters
        this.bassFilter.type = 'lowshelf';
        this.bassFilter.frequency.value = 200;

        this.midFilter.type = 'peaking';
        this.midFilter.frequency.value = 1000;

        this.trebleFilter.type = 'highshelf';
        this.trebleFilter.frequency.value = 3000;

        // Connect chain
        this.gainNode
            .connect(this.bassFilter)
            .connect(this.midFilter)
            .connect(this.trebleFilter)
            .connect(this.audioContext.destination);
    }

    connectAudio(audioElement) {
        this.source = this.audioContext.createMediaElementSource(audioElement);
        this.source.connect(this.gainNode);
    }

    setNormalizedGain(value) {
        this.gainNode.gain.value = value; // from database
    }

    setEQ(bass, mid, treble) {
        this.bassFilter.gain.value = bass; // -12 to 12 dB
        this.midFilter.gain.value = mid;
        this.trebleFilter.gain.value = treble;
    }
}
```

### 3.6 Preloading Strategy
**File:** `resources/js/themes/muzibu/utils/preloader.js`

- [ ] Preload first song on playlist open
- [ ] Preload next song when current song reaches 70% progress
- [ ] Use fetch API with cache
- [ ] For HLS: preload first 2 segments
- [ ] Cancel preload if user skips

**Implementation:**
```javascript
class SongPreloader {
    constructor() {
        this.preloadCache = new Map();
    }

    async preload(song) {
        if (this.preloadCache.has(song.song_id)) return;

        if (song.hls_enabled) {
            await this.preloadHLS(song.hls_url);
        } else {
            await this.preloadNative(song.file_path);
        }

        this.preloadCache.set(song.song_id, true);
    }

    async preloadHLS(url) {
        // Fetch m3u8 playlist
        const response = await fetch(url);
        const playlist = await response.text();

        // Parse first 2 segment URLs
        const segments = this.parseSegments(playlist).slice(0, 2);

        // Preload segments
        await Promise.all(segments.map(seg => fetch(seg)));
    }

    async preloadNative(url) {
        // Fetch with range header (first 100KB)
        await fetch(url, {
            headers: { 'Range': 'bytes=0-102400' }
        });
    }
}
```

---

## 🎨 Phase 4: Demo Page Builder

### 4.1 Component Showcase Page
**File:** `resources/views/themes/muzibu/demo/components.blade.php`

- [ ] Create demo page route (`/demo/components`)
- [ ] Version selector dropdowns (Header, Main, Footer, Player)
- [ ] Live preview
- [ ] URL state management (query params)
- [ ] Shareable URLs

**UI Layout:**
```
┌─────────────────────────────────────┐
│  Component Selector Panel (Sticky) │
│  Header: [v1] [v2] [v3]             │
│  Main:   [v1] [v2] [v3] [v4] [v5]   │
│  Footer: [v1] [v2] [v3]             │
│  Player: [v1] [v2] [v3] [v4]        │
│  [Apply] [Share URL]                │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│  Live Preview (Iframe)              │
│                                     │
│  (Selected components rendered)     │
│                                     │
└─────────────────────────────────────┘
```

### 4.2 URL Scheme
```
/demo/components?header=v2&main=v5&footer=v1&player=v4
```

### 4.3 localStorage Persistence
```javascript
// Save user's preferred combination
localStorage.setItem('muzibu_components', JSON.stringify({
    header: 'v2',
    main: 'v5',
    footer: 'v1',
    player: 'v4'
}));

// Load on app init
const saved = JSON.parse(localStorage.getItem('muzibu_components'));
Alpine.store('ui').components = saved;
```

---

## 📦 Phase 5: Database & Backend

### 5.1 Required Migrations

#### User Device Tracking
**File:** `database/migrations/tenant/YYYY_MM_DD_create_user_devices_table.php`

```php
Schema::create('user_devices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('device_name'); // "iPhone 12", "Chrome on Windows"
    $table->string('device_fingerprint')->unique(); // FingerprintJS hash
    $table->string('user_agent');
    $table->string('ip_address');
    $table->timestamp('last_active');
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['user_id', 'is_active']);
    $table->index('device_fingerprint');
});
```

#### Song Normalization Data
**File:** `database/migrations/YYYY_MM_DD_add_normalization_to_songs.php`

```php
Schema::table('muzibu_songs', function (Blueprint $table) {
    $table->decimal('normalized_gain', 5, 2)->default(1.0)->after('hls_enabled');
    $table->boolean('is_normalized')->default(false)->after('normalized_gain');
    $table->timestamp('normalized_at')->nullable()->after('is_normalized');

    $table->index('is_normalized');
});
```

### 5.2 Jobs

#### ConvertSongToHLS Job
**File:** `app/Jobs/ConvertSongToHLS.php`

- [ ] Triggered on first play (if not HLS)
- [ ] FFmpeg command execution
- [ ] Generate m3u8 playlist + segments
- [ ] Upload to storage
- [ ] Update `muzibu_songs.hls_enabled = true`
- [ ] Queue priority: low (background processing)

**FFmpeg Command:**
```bash
ffmpeg -i input.mp3 \
  -c:a aac -b:a 128k -map 0:a output_128k.m3u8 \
  -c:a aac -b:a 192k -map 0:a output_192k.m3u8 \
  -c:a aac -b:a 320k -map 0:a output_320k.m3u8 \
  -f hls -hls_time 10 -hls_playlist_type vod -master_pl_name master.m3u8
```

#### NormalizeSongVolume Job
**File:** `app/Jobs/NormalizeSongVolume.php`

- [ ] Analyze song loudness (FFmpeg loudnorm filter)
- [ ] Calculate optimal gain value
- [ ] Update `muzibu_songs.normalized_gain`
- [ ] Mark `is_normalized = true`
- [ ] Queue priority: low

**FFmpeg Loudness Analysis:**
```bash
ffmpeg -i input.mp3 -af loudnorm=print_format=json -f null -
```

### 5.3 API Endpoints

#### Play Song Endpoint
**Route:** `POST /api/muzibu/songs/{id}/play`

- [ ] Check subscription status
- [ ] Check device limit
- [ ] Increment play count
- [ ] Log to `muzibu_song_plays`
- [ ] Trigger HLS conversion job (if needed)
- [ ] Return song URL (HLS or direct)

**Response:**
```json
{
    "song": {
        "song_id": 123,
        "title": {"tr": "Şarkı Adı"},
        "artist": "Sanatçı",
        "album_art": "https://...",
        "url": "https://.../song.m3u8",
        "type": "hls",
        "duration": 240,
        "normalized_gain": 0.85
    },
    "queue": [
        // Next songs in playlist
    ]
}
```

#### Get Queue Endpoint
**Route:** `GET /api/muzibu/queue`

- [ ] Return current queue
- [ ] Apply shuffle if enabled
- [ ] Preload next 3 songs

---

## 🧪 Phase 6: Testing Checklist

### 6.1 SPA Functionality
- [ ] Navigate between pages without reload
- [ ] Browser back/forward buttons work
- [ ] URL updates correctly
- [ ] Deep linking works (direct URL access)

### 6.2 Player Persistence
- [ ] Music continues on page change
- [ ] Player state persists (play/pause)
- [ ] Queue persists across navigation
- [ ] Progress bar accurate

### 6.3 HLS Streaming
- [ ] HLS songs play correctly
- [ ] Fallback to MP3 works
- [ ] Quality switching (ABR)
- [ ] No buffering issues

### 6.4 Instant Playback
- [ ] Song starts within 500ms
- [ ] Preloading works
- [ ] Next song instant start
- [ ] No loading spinners

### 6.5 Volume Normalization
- [ ] All songs same loudness
- [ ] EQ adjustments work
- [ ] No audio distortion

### 6.6 Device Limit
- [ ] Only 1 device logged in
- [ ] Logout on 2nd device login
- [ ] User can see active devices
- [ ] Manual device removal works

### 6.7 UI/UX
- [ ] No layout shifts
- [ ] Smooth animations
- [ ] Responsive on mobile
- [ ] Touch gestures work
- [ ] Keyboard shortcuts work

### 6.8 Performance
- [ ] Page load < 2s
- [ ] First paint < 1s
- [ ] Lazy loading works
- [ ] Memory usage stable (no leaks)

---

## 📊 Success Metrics

- **Playback Start Time:** < 500ms
- **Page Navigation:** < 100ms (perceived, SPA)
- **Memory Usage:** < 150MB (after 1 hour playback)
- **Device Limit:** 100% enforcement
- **HLS Conversion Rate:** 80% of popular songs converted in 1 week

---

## 🔗 Dependencies

### Frontend
- Alpine.js (v3.x)
- Tailwind CSS (v3.x)
- HLS.js (v1.x)
- Vibrant.js (for color extraction)
- FingerprintJS (device identification)

### Backend
- Laravel (existing)
- FFmpeg (for HLS conversion & normalization)
- Redis (for queue management)

### CDN (Optional)
- Cloudflare / BunnyCDN (for song delivery)

---

## 📝 Notes

- All components must be tenant-aware (Tenant 1001 only)
- Use existing SEO module for meta tags
- Integrate with existing Favorite, Subscription, Payment modules
- Mobile-first responsive design
- WCAG AA accessibility compliance
- Browser support: Chrome, Firefox, Safari, Edge (latest 2 versions)

---

**Status:** Ready for implementation
**Next Step:** Create HTML design alternatives demo page
