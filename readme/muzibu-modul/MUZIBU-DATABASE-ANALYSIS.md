# 🎵 MUZİBU VERİTABANI YAPI ANALİZİ

**Tarih:** 9 Kasım 2025
**Kaynak:** muzibu_mayis25-verisiz-bos.sql
**Hedef:** Laravel Muzibu Modülü

---

## 📊 TABLO YAPISI ÖZETİ

### Toplam: 12 Tablo

**Core Tablolar (5):**
1. `muzibu_albums` - Albümler
2. `muzibu_artists` - Sanatçılar
3. `muzibu_songs` - Şarkılar
4. `muzibu_genres` - Müzik Türleri
5. `muzibu_playlists` - Çalma Listeleri

**İlişki Tabloları (4):**
6. `muzibu_playlist_song` - Playlist ↔ Song (Many-to-Many)
7. `muzibu_playlist_sector` - Playlist ↔ Sector (Sektörel)
8. `muzibu_radio_sector` - Radio ↔ Sector
9. `muzibu_playlist_radio` - Playlist ↔ Radio

**Özellik Tabloları (3):**
10. `muzibu_sectors` - Sektörler (İşletme tipleri)
11. `muzibu_radios` - Radyo İstasyonları
12. `muzibu_song_plays` - Dinleme İstatistikleri

---

## ⚠️ UNIVERSAL SİSTEMLER

**Kaldırılan Tablolar (Universal modüllerle değiştirildi):**

### 1. Favorites System (Universal)
- ❌ `muzibu_favorites` - **Kaldırıldı**
- ❌ `muzibu_playlist_favorites` - **Kaldırıldı**
- ✅ **Universal Favorites modülü** kullanılacak
- Tüm içerik tipleri için tek sistem (Song, Playlist, Album, Artist, vb.)

### 2. Tag/Category System (Universal)
- ❌ `muzibu_moods` - **Kaldırıldı**
- ❌ `muzibu_song_mood` - **Kaldırıldı**
- ✅ **Universal Tag/Category modülü** kullanılacak
- Ruh halleri (Mutlu, Hüzünlü, Romantik) tag olarak eklenecek

---

## 🔄 ESKİ VS YENİ YAPI KARŞILAŞTIRMASI

### 1. Çoklu Dil Sistemi

**❌ Eski Yapı:**
```sql
title_tr VARCHAR(255)
title_en VARCHAR(255)
description_tr TEXT
description_en TEXT
```

**✅ Yeni Yapı (Laravel):**
```sql
title JSON  -- {"tr": "Başlık", "en": "Title"}
description JSON  -- {"tr": "Açıklama", "en": "Description"}
```

### 2. Zaman Damgaları

**❌ Eski Yapı:**
```sql
created DATETIME DEFAULT current_timestamp()
```

**✅ Yeni Yapı (Laravel):**
```sql
created_at TIMESTAMP NULL
updated_at TIMESTAMP NULL
```

### 3. Aktiflik Durumu

**❌ Eski Yapı:**
```sql
active TINYINT(1) DEFAULT 1
```

**✅ Yeni Yapı (Laravel):**
```sql
is_active BOOLEAN DEFAULT true
```

### 4. SEO Alanları

**❌ Eski Yapı (Her tabloda):**
```sql
meta_title VARCHAR(250)
meta_keywords VARCHAR(500)
meta_description VARCHAR(500)
```

**✅ Yeni Yapı (İlişkili):**
- SEO bilgileri `SeoManagement` modülü ile ilişkilendirilecek
- Global SEO sistemi kullanılacak

### 5. Foreign Keys

**❌ Eski Yapı:**
```sql
artist_id INT(11) -- Constraint yok
```

**✅ Yeni Yapı:**
```sql
artist_id BIGINT UNSIGNED
FOREIGN KEY (artist_id) REFERENCES muzibu_artists(id) ON DELETE CASCADE
```

---

## 📋 DETAYLI TABLO ANALİZİ

### 1. muzibu_albums (Albümler)

**Eski Yapı:**
- `id` INT(11)
- `title_tr` VARCHAR(255)
- `slug` VARCHAR(255)
- `artist_id` INT(11)
- `description_tr` TEXT
- `thumb` VARCHAR(255)
- `created` DATETIME
- `active` TINYINT(1)
- `meta_title`, `meta_keywords`, `meta_description`

**Yeni Yapı:**
```php
Schema::create('muzibu_albums', function (Blueprint $table) {
    $table->id('album_id');
    $table->json('title'); // {"tr": "", "en": ""}
    $table->json('slug'); // {"tr": "", "en": ""}
    $table->foreignId('artist_id')->nullable()
        ->constrained('muzibu_artists', 'artist_id')
        ->nullOnDelete();
    $table->json('description')->nullable();
    $table->string('thumb')->nullable(); // Veya Media ilişkisi
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->index(['is_active', 'created_at']);
});
```

---

### 2. muzibu_artists (Sanatçılar)

**Eski Yapı:**
- `id` INT(11)
- `title_tr` VARCHAR(255) - Sanatçı adı
- `slug` VARCHAR(255)
- `bio_tr` TEXT - Biyografi
- `thumb` VARCHAR(255)
- `created`, `active`, SEO fields

**Yeni Yapı:**
```php
Schema::create('muzibu_artists', function (Blueprint $table) {
    $table->id('artist_id');
    $table->json('name'); // {"tr": "Sanatçı", "en": "Artist"}
    $table->json('slug');
    $table->json('bio')->nullable(); // Biyografi
    $table->string('thumb')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->index('is_active');
});
```

---

### 3. muzibu_songs (Şarkılar)

**Eski Yapı:**
```sql
id, title_tr, slug, artist_id, album_id, genre_id,
description_tr, lyrics_tr, audio, duration, thumb,
created, active, SEO fields
```

**Yeni Yapı:**
```php
Schema::create('muzibu_songs', function (Blueprint $table) {
    $table->id('song_id');
    $table->json('title');
    $table->json('slug');

    // Foreign Keys
    $table->foreignId('artist_id')->nullable()
        ->constrained('muzibu_artists', 'artist_id')
        ->nullOnDelete();
    $table->foreignId('album_id')->nullable()
        ->constrained('muzibu_albums', 'album_id')
        ->nullOnDelete();
    $table->foreignId('genre_id')->nullable()
        ->constrained('muzibu_genres', 'genre_id')
        ->nullOnDelete();

    // Content
    $table->json('description')->nullable();
    $table->json('lyrics')->nullable(); // Şarkı sözleri
    $table->string('audio')->nullable(); // Ses dosyası yolu
    $table->integer('duration')->nullable(); // Saniye cinsinden
    $table->string('thumb')->nullable();

    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->index(['artist_id', 'is_active']);
    $table->index(['album_id', 'is_active']);
    $table->index(['genre_id', 'is_active']);
});
```

---

### 4. muzibu_genres (Müzik Türleri)

**Örnek:** Pop, Rock, Jazz, Klasik

```php
Schema::create('muzibu_genres', function (Blueprint $table) {
    $table->id('genre_id');
    $table->json('title'); // {"tr": "Pop", "en": "Pop"}
    $table->json('slug');
    $table->json('description')->nullable();
    $table->string('thumb')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('is_active');
});
```

---

### 5. muzibu_playlists (Çalma Listeleri)

**Eski Yapı:**
- `user_id` INT(11) - Oluşturan kullanıcı
- `system` TINYINT(1) - Sistem playlist mi?
- `is_public` TINYINT(1) - Herkese açık mı?
- `radio` TINYINT(1) - Radyo modunda mı?

```php
Schema::create('muzibu_playlists', function (Blueprint $table) {
    $table->id('playlist_id');
    $table->json('title');
    $table->json('slug');

    $table->foreignId('user_id')->nullable()
        ->constrained('users', 'id')
        ->nullOnDelete();

    $table->boolean('is_system')->default(false); // Sistem playlist
    $table->boolean('is_public')->default(true); // Herkese açık
    $table->boolean('is_radio')->default(false); // Radyo modu

    $table->json('description')->nullable();
    $table->string('thumb')->nullable();
    $table->boolean('is_active')->default(true);

    $table->timestamps();
    $table->softDeletes();

    $table->index(['user_id', 'is_active']);
    $table->index(['is_system', 'is_public']);
});
```

---

### 6. muzibu_moods (Ruh Halleri)

**Örnek:** Mutlu, Hüzünlü, Romantik, Enerjik

```php
Schema::create('muzibu_moods', function (Blueprint $table) {
    $table->id('mood_id');
    $table->json('title'); // {"tr": "Mutlu", "en": "Happy"}
    $table->string('icon')->nullable(); // Font Awesome icon
    $table->string('color')->nullable(); // Hex renk kodu
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('is_active');
});
```

---

### 7. muzibu_sectors (Sektörler)

**Örnek:** Restoran, Kafe, Spor Salonu, Ofis

```php
Schema::create('muzibu_sectors', function (Blueprint $table) {
    $table->id('sector_id');
    $table->json('title'); // {"tr": "Restoran", "en": "Restaurant"}
    $table->json('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('is_active');
});
```

---

### 8. muzibu_radios (Radyo İstasyonları)

```php
Schema::create('muzibu_radios', function (Blueprint $table) {
    $table->id('radio_id');
    $table->json('title'); // {"tr": "Radyo 1", "en": "Radio 1"}
    $table->string('stream_url')->nullable(); // Canlı yayın URL
    $table->string('thumb')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('is_active');
});
```

---

## 🔗 İLİŞKİ TABLOLARI (PIVOT)

### 1. muzibu_playlist_song

**Many-to-Many:** Playlist ↔ Song

```php
Schema::create('muzibu_playlist_song', function (Blueprint $table) {
    $table->id();
    $table->foreignId('playlist_id')
        ->constrained('muzibu_playlists', 'playlist_id')
        ->cascadeOnDelete();
    $table->foreignId('song_id')
        ->constrained('muzibu_songs', 'song_id')
        ->cascadeOnDelete();
    $table->integer('sort_order')->default(0); // Sıralama
    $table->timestamps();

    $table->unique(['playlist_id', 'song_id']);
    $table->index('sort_order');
});
```

### 2. muzibu_song_mood

**Many-to-Many:** Song ↔ Mood

```php
Schema::create('muzibu_song_mood', function (Blueprint $table) {
    $table->id();
    $table->foreignId('song_id')
        ->constrained('muzibu_songs', 'song_id')
        ->cascadeOnDelete();
    $table->foreignId('mood_id')
        ->constrained('muzibu_moods', 'mood_id')
        ->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['song_id', 'mood_id']);
});
```

### 3. muzibu_favorites

**User Favorileri**

```php
Schema::create('muzibu_favorites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')
        ->constrained('users', 'id')
        ->cascadeOnDelete();
    $table->foreignId('song_id')
        ->constrained('muzibu_songs', 'song_id')
        ->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['user_id', 'song_id']);
    $table->index('user_id');
});
```

### 4. muzibu_playlist_favorites

```php
Schema::create('muzibu_playlist_favorites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')
        ->constrained('users', 'id')
        ->cascadeOnDelete();
    $table->foreignId('playlist_id')
        ->constrained('muzibu_playlists', 'playlist_id')
        ->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['user_id', 'playlist_id']);
});
```

### 5. muzibu_song_plays

**Dinleme İstatistikleri**

```php
Schema::create('muzibu_song_plays', function (Blueprint $table) {
    $table->id();
    $table->foreignId('song_id')
        ->constrained('muzibu_songs', 'song_id')
        ->cascadeOnDelete();
    $table->foreignId('user_id')->nullable()
        ->constrained('users', 'id')
        ->nullOnDelete();
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent')->nullable();
    $table->integer('duration_played')->nullable(); // Kaç saniye dinlendi
    $table->timestamp('played_at');

    $table->index(['song_id', 'played_at']);
    $table->index('user_id');
});
```

---

## 📦 MODEL İLİŞKİLERİ

### Artist Model

```php
class Artist extends Model
{
    // Bir sanatçının birden fazla albümü var
    public function albums()
    {
        return $this->hasMany(Album::class, 'artist_id', 'artist_id');
    }

    // Bir sanatçının birden fazla şarkısı var
    public function songs()
    {
        return $this->hasMany(Song::class, 'artist_id', 'artist_id');
    }
}
```

### Song Model

```php
class Song extends Model
{
    // Bir şarkının bir sanatçısı var
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'artist_id');
    }

    // Bir şarkının bir albümü var
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }

    // Bir şarkının bir türü var
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id', 'genre_id');
    }

    // Bir şarkı birden fazla ruh haline sahip (Many-to-Many)
    public function moods()
    {
        return $this->belongsToMany(Mood::class, 'muzibu_song_mood', 'song_id', 'mood_id');
    }

    // Bir şarkı birden fazla playlist'te (Many-to-Many)
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'muzibu_playlist_song', 'song_id', 'playlist_id')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    // Şarkıyı favori olarak ekleyen kullanıcılar
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'muzibu_favorites', 'song_id', 'user_id');
    }

    // Dinleme istatistikleri
    public function plays()
    {
        return $this->hasMany(SongPlay::class, 'song_id', 'song_id');
    }
}
```

### Playlist Model

```php
class Playlist extends Model
{
    // Playlist'teki şarkılar (Many-to-Many)
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'muzibu_playlist_song', 'playlist_id', 'song_id')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('muzibu_playlist_song.sort_order');
    }

    // Playlist'i oluşturan kullanıcı
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Playlist'i favori olarak ekleyen kullanıcılar
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'muzibu_playlist_favorites', 'playlist_id', 'user_id');
    }

    // Playlist'in sektörleri
    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'muzibu_playlist_sector', 'playlist_id', 'sector_id');
    }
}
```

---

## 🎯 MİGRATION SIRALAMA

**ÖNEM:** Foreign key bağımlılıkları nedeniyle doğru sırayla oluşturulmalı!

### Sıralama (Tarih prefix): 12 Migration

1. `2024_11_09_001_create_muzibu_artists_table.php`
2. `2024_11_09_002_create_muzibu_albums_table.php`
3. `2024_11_09_003_create_muzibu_genres_table.php`
4. `2024_11_09_004_create_muzibu_sectors_table.php`
5. `2024_11_09_005_create_muzibu_radios_table.php`
6. `2024_11_09_006_create_muzibu_songs_table.php` (Artist, Album, Genre'ye bağımlı)
7. `2024_11_09_007_create_muzibu_playlists_table.php`
8. `2024_11_09_008_create_muzibu_playlist_song_table.php` (Pivot)
9. `2024_11_09_009_create_muzibu_playlist_sector_table.php` (Pivot)
10. `2024_11_09_010_create_muzibu_radio_sector_table.php` (Pivot)
11. `2024_11_09_011_create_muzibu_playlist_radio_table.php` (Pivot)
12. `2024_11_09_012_create_muzibu_song_plays_table.php`

### ❌ Kaldırılan Migration'lar (Universal sistemler):
- ~~`muzibu_moods_table`~~ → Universal Tag System
- ~~`muzibu_song_mood_table`~~ → Universal Tag System
- ~~`muzibu_favorites_table`~~ → Universal Favorites Module
- ~~`muzibu_playlist_favorites_table`~~ → Universal Favorites Module

---

## ✅ SONRAKI ADIMLAR

1. ✅ SQL analizi tamamlandı
2. ⏳ Migration dosyalarını oluştur
3. ⏳ Model'leri oluştur
4. ⏳ Seeder'ları hazırla
5. ⏳ Test et

---

**📅 Oluşturulma:** 9 Kasım 2025
**🤖 Generated with:** Claude Code
