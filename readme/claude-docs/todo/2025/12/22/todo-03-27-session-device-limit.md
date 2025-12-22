# Session Device Limit & HLS Dayanıklılığı - TODO (v4)

## Yapıldı
- [x] tenant_muzibu_1528d0.user_active_sessions temizlendi (0 kayıt; central tablo da boş).

## Backend
- [ ] DeviceService::sessionExists ve SongStreamController::stream/serveHls içine ayrıntılı reason logları ekle (user_id, login_token prefix, session_id, guard, cookie var/yok, Redis hit) ki yanlış “başka cihaz” uyarıları ayıklansın.
- [ ] HLS imza süresini parça uzunluğu + tampon (>= şarkı süresi + 120s) olarak hesapla; SignedUrlService@createHlsUrl + SongStreamController@stream/serveHls güncelle.
- [ ] Logout/limit ihlalinde tüm cihazları kapatan ortak helper kullan (DeviceService::terminateSessions) ve UI’ya reason döndür; kontrol tick’leri checkSession polling ile eşle.
- [ ] Favorites API /api/favorites/list 500 hatasını loglardan yakala (storage/tenant1001/logs, stack channel) ve ilgili controller/service’te (Modules/Muzibu/app/Http/Controllers/Api/…) hata kaynağını düzelt.

## Frontend
- [ ] player-core.js: manifestLoadError/timeout için MP3 fallback veya sıradaki şarkıya geçiş ekle; crossfade sırasında yeni imzalı playlist URL’sini aktif player’a swap et.
- [ ] checkSessionValidity/handleSessionTerminated akışında reason kodunu kullanıcıya göster (başka cihaz / süresi doldu / logout) ve modal + redirect’i tekle.
- [ ] HLS refresh süresini TTL’e göre dinamik yap; uzun şarkıda süresi bitmeden yeni URL’yi kullanmaya başlat (preload + swap).

## Test
- [ ] Senaryo: tek tarayıcı 30dk yenilemeden çalma (uzun parça dahil), 401/timeout/log kontrolü.
- [ ] Senaryo: A (Chrome) açıkken B (Firefox) login → A 401/uyarı/redirect almalı, stream durmalı.
- [ ] Senaryo: aynı cihaz + farklı tarayıcıda (Chrome+Firefox) cookie kaybı olmadan “başka cihaz” uyarısı çıkmamalı; reason logları doğrulanmalı.
- [ ] Senaryo: logout tetikleyince tüm tarayıcılar aynı anda düşmeli; yeni şarkı başlatılamamalı.
