/**
 * 🧪 CONTEXT QUICK TEST
 * Context-Based Infinite Queue sistemini hızlıca test eder
 *
 * Kullanım: Tarayıcı console'unda:
 * fetch('/testler/context-quick-test.js').then(r=>r.text()).then(eval)
 */

(async function contextQuickTest() {
    console.log('🧪 CONTEXT QUICK TEST BAŞLIYOR...\n');

    const player = Alpine.store('player');
    const muzibu = Alpine.store('muzibu');

    if (!player) {
        console.error('❌ Player store bulunamadı!');
        return;
    }

    const results = {
        passed: 0,
        failed: 0,
        tests: []
    };

    function test(name, condition, details = '') {
        if (condition) {
            results.passed++;
            results.tests.push({ name, status: '✅', details });
            console.log(`✅ ${name}`, details ? `(${details})` : '');
        } else {
            results.failed++;
            results.tests.push({ name, status: '❌', details });
            console.log(`❌ ${name}`, details ? `(${details})` : '');
        }
    }

    // ═══════════════════════════════════════
    // TEST 1: Store'lar mevcut mu?
    // ═══════════════════════════════════════
    console.log('\n📋 TEST 1: Store Kontrolü');
    test('Player store mevcut', !!player);
    test('Muzibu store mevcut', !!muzibu);
    test('setPlayContext fonksiyonu var', typeof muzibu?.setPlayContext === 'function');

    // ═══════════════════════════════════════
    // TEST 2: ValidTypes 'song' içeriyor mu?
    // ═══════════════════════════════════════
    console.log('\n📋 TEST 2: ValidTypes Kontrolü');

    // setPlayContext'i test et
    let songContextAccepted = false;
    const originalWarn = console.warn;
    console.warn = function(...args) {
        if (args[0]?.includes?.('Invalid context type')) {
            songContextAccepted = false;
        }
    };

    muzibu?.setPlayContext({ type: 'song', id: 1, name: 'Test Song' });
    songContextAccepted = muzibu?.playContext?.type === 'song';
    console.warn = originalWarn;

    test("'song' tipi kabul ediliyor", songContextAccepted);

    // ═══════════════════════════════════════
    // TEST 3: playContent fonksiyonları var mı?
    // ═══════════════════════════════════════
    console.log('\n📋 TEST 3: playContent Fonksiyonları');
    test('window.playContent mevcut', typeof window.playContent === 'function');

    // ═══════════════════════════════════════
    // TEST 4: API Endpoint'leri çalışıyor mu?
    // ═══════════════════════════════════════
    console.log('\n📋 TEST 4: API Endpoint Testleri');

    // Queue refill - song type
    try {
        const songRefill = await fetch('/api/muzibu/queue/refill', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ type: 'song', id: 1, limit: 5 })
        });
        const songData = await songRefill.json();
        test('Queue refill (song type) çalışıyor', songRefill.ok, `Status: ${songRefill.status}`);
        test('Song refill şarkı döndürüyor', Array.isArray(songData.songs), `${songData.songs?.length || 0} şarkı`);
    } catch (e) {
        test('Queue refill (song type) çalışıyor', false, e.message);
    }

    // Artist songs endpoint
    try {
        const artistResp = await fetch('/api/muzibu/artists/1/songs?limit=5', {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        test('Artist songs endpoint çalışıyor', artistResp.ok, `Status: ${artistResp.status}`);
    } catch (e) {
        test('Artist songs endpoint çalışıyor', false, e.message);
    }

    // ═══════════════════════════════════════
    // TEST 5: Hızlı şarkı geçiş testi (10 şarkı)
    // ═══════════════════════════════════════
    console.log('\n📋 TEST 5: Hızlı Şarkı Geçiş Testi (10 şarkı)');

    // Önce queue'yu yükle
    if (!player.queue || player.queue.length < 5) {
        console.log('📥 Queue yükleniyor...');
        try {
            const resp = await fetch('/api/muzibu/queue/initial');
            const data = await resp.json();
            if (data.songs?.length > 0) {
                player.queue = data.songs;
                player.queueIndex = 0;
                console.log(`✅ ${data.songs.length} şarkı yüklendi`);
            }
        } catch (e) {
            console.error('Queue yüklenemedi:', e);
        }
    }

    let transitionCount = 0;
    let errorCount = 0;
    const startIndex = player.queueIndex;

    for (let i = 0; i < 10; i++) {
        try {
            await player.nextTrack(true);
            transitionCount++;
            await new Promise(r => setTimeout(r, 100));
        } catch (e) {
            errorCount++;
        }
    }

    test('10 şarkı geçişi başarılı', transitionCount >= 8, `${transitionCount}/10 başarılı`);
    test('Hata sayısı düşük', errorCount <= 2, `${errorCount} hata`);

    // ═══════════════════════════════════════
    // TEST 6: Context değişim testi
    // ═══════════════════════════════════════
    console.log('\n📋 TEST 6: Context Değişim Testi');

    const contexts = [
        { type: 'album', id: 1, name: 'Test Album' },
        { type: 'genre', id: 1, name: 'Test Genre' },
        { type: 'artist', id: 1, name: 'Test Artist' },
        { type: 'playlist', id: 1, name: 'Test Playlist' },
        { type: 'favorites', id: null, name: 'Favoriler' },
        { type: 'song', id: 1, name: 'Test Song' }
    ];

    for (const ctx of contexts) {
        muzibu?.setPlayContext(ctx);
        await new Promise(r => setTimeout(r, 50));
        const current = muzibu?.playContext;
        test(`Context '${ctx.type}' ayarlanıyor`, current?.type === ctx.type);
    }

    // ═══════════════════════════════════════
    // SONUÇLAR
    // ═══════════════════════════════════════
    console.log('\n');
    console.log('═══════════════════════════════════════════════');
    console.log('📊 CONTEXT QUICK TEST SONUÇLARI');
    console.log('═══════════════════════════════════════════════');
    console.log(`✅ Başarılı: ${results.passed}`);
    console.log(`❌ Başarısız: ${results.failed}`);
    console.log(`📈 Başarı Oranı: ${((results.passed / (results.passed + results.failed)) * 100).toFixed(1)}%`);

    if (results.failed === 0) {
        console.log('\n🎉 TÜM TESTLER BAŞARILI!');
    } else {
        console.log('\n⚠️ Bazı testler başarısız. Yukarıdaki detayları inceleyin.');
    }
    console.log('═══════════════════════════════════════════════');

    return results;
})();
