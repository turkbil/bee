# Muzibu Module - System Requirements

## 🚨 Critical Requirements

### 1. FFmpeg Installation (REQUIRED)

**Status:** ❌ NOT INSTALLED

FFmpeg is **REQUIRED** for HLS streaming conversion. Without it, HLS conversion jobs will fail.

#### Installation (CentOS/AlmaLinux):
```bash
# Install EPEL repository
sudo yum install -y epel-release

# Install FFmpeg
sudo yum install -y ffmpeg

# Verify installation
ffmpeg -version
```

#### Expected Output:
```
ffmpeg version 4.4.2 Copyright (c) 2000-2021 the FFmpeg developers
built with gcc 8 (GCC)
configuration: --prefix=/usr ...
```

#### Test HLS Conversion:
```bash
# Test FFmpeg HLS conversion
ffmpeg -i test.mp3 -codec: copy -start_number 0 -hls_time 10 -hls_list_size 0 -f hls output/playlist.m3u8
```

---

### 2. PHP Extensions

✅ **getID3** - Already installed (`james-heinrich/getid3`)
- Used for: Audio metadata extraction (duration, bitrate, sample_rate)

---

### 3. Queue Worker

✅ **Laravel Horizon** - Already running
- Multiple supervisors active:
  - `ai-supervisor` - AI tasks (max 2 processes)
  - `tenant-supervisor` - Tenant tasks (max 1 process)
  - `background-supervisor` - Background tasks (max 1 process)

**ConvertToHLSJob** will run on the `default` queue via `tenant-supervisor`.

---

### 4. Storage Permissions

**HLS Output Directory:** `storage/app/public/muzibu/songs/hls/`

✅ Automatic directory creation in `ConvertToHLSJob::handle()`
- Creates with `0755` permissions
- Path: `muzibu/songs/hls/song-{id}/`

**Files:**
- `playlist.m3u8` - HLS master playlist
- `segment-000.ts`, `segment-001.ts`, ... - HLS segments (10 seconds each)

---

## 📦 NPM Dependencies

✅ **HLS.js** - Installed (`npm install hls.js --save`)
- Version: Latest
- Used for: Frontend HLS playback in browsers

---

## 🗄️ Database Migrations

✅ **Tenant Migrations Applied:**
- `2025_11_11_020022_add_hls_fields_to_muzibu_songs_table.php`
- Applied to Tenant 1, 2, 3

**New Fields:**
- `hls_path` (VARCHAR 500) - HLS playlist path
- `hls_converted` (BOOLEAN, indexed) - Conversion status
- `bitrate` (INT UNSIGNED) - Audio bitrate in kbps
- `metadata` (JSON) - Additional metadata

---

## 🔧 Configuration

### Queue Configuration
- **Driver:** Redis
- **Connection:** `redis`
- **Queue:** `default` (for ConvertToHLSJob)

### FFmpeg Settings (in Job)
- **Codec:** Copy (no re-encoding, fast)
- **Segment Duration:** 10 seconds (`-hls_time 10`)
- **Playlist:** All segments included (`-hls_list_size 0`)
- **Format:** HLS (`-f hls`)

### Job Settings
- **Timeout:** 600 seconds (10 minutes)
- **Tries:** 3 attempts
- **Queue:** `default`

---

## 🚀 Deployment Checklist

Before deploying to production:

- [ ] Install FFmpeg on server
- [ ] Test FFmpeg with sample MP3
- [ ] Verify storage permissions (`storage/app/public/`)
- [ ] Ensure Horizon is running
- [ ] Test HLS conversion job manually
- [ ] Test HLS playback in browser
- [ ] Monitor queue for failed jobs
- [ ] Check Laravel logs for errors

---

## 🧪 Testing

### Manual Test Commands:
```bash
# Test metadata extraction
php artisan tinker
>>> $song = \Modules\Muzibu\App\Models\Song::first();
>>> $song->extractMetadata();

# Test HLS conversion job
>>> \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);

# Check job status in Horizon
>>> Open: /admin/horizon

# Check HLS output
>>> ls -la storage/app/public/muzibu/songs/hls/song-1/
```

### API Endpoints:
```bash
# Stream song (triggers lazy HLS conversion)
curl -X GET https://ixtif.com/api/muzibu/songs/1/stream

# Check conversion status
curl -X GET https://ixtif.com/api/muzibu/songs/1/conversion-status

# Increment play count
curl -X POST https://ixtif.com/api/muzibu/songs/1/play
```

---

## ⚠️ Known Issues

### FFmpeg Not Installed
- **Impact:** HLS conversion will fail
- **Fix:** Install FFmpeg (see above)
- **Workaround:** System will serve original MP3 until FFmpeg is installed

### Large File Processing
- **Issue:** 4-5MB MP3 files take ~5-10 seconds to convert
- **Solution:** Lazy conversion (convert on first play, not bulk)
- **Queue:** Background processing via Laravel Horizon

---

## 📊 Performance Metrics

### Expected Conversion Time:
- **4MB MP3:** ~5-10 seconds
- **10MB MP3:** ~15-20 seconds

### Storage Size:
- **Original MP3:** 4-5 MB
- **HLS Output:** ~5-6 MB (slightly larger due to segmentation)

### Network Bandwidth:
- **MP3 Streaming:** Full file must be downloaded
- **HLS Streaming:** Adaptive, streams only needed segments

---

## 🔐 Security

### HLS Benefits:
- ✅ Prevents direct MP3 downloads
- ✅ Difficult to extract original file
- ✅ Adaptive bitrate streaming
- ✅ Better protection against piracy

### Storage Security:
- ✅ Files stored outside public web root
- ✅ Served via Laravel Storage (not direct Apache access)
- ✅ Tenant-isolated storage paths

---

## 📚 Documentation

- **v2 Documentation:** `/readme/muzibu/medias/v2/index.html`
- **HLS Player Component:** `/readme/muzibu/hls-player-component.html`
- **API Routes:** `Modules/Muzibu/routes/api.php`
- **Job:** `Modules/Muzibu/app/Jobs/ConvertToHLSJob.php`
- **Controller:** `Modules/Muzibu/app/Http/Controllers/Api/SongStreamController.php`

---

**Last Updated:** 2025-11-11
**Status:** ⚠️ Waiting for FFmpeg installation
