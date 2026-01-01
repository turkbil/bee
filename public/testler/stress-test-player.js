/**
 * 🧪 PLAYER STRESS TEST v2
 * 500 şarkıyı hızlıca geçerek player'ı test eder
 * Queue bitince auto-refill'i de test eder
 *
 * Kullanım: Tarayıcı console'unda:
 * fetch('/testler/stress-test-player.js').then(r=>r.text()).then(eval)
 */

(async function stressTestPlayer() {
    const player = Alpine.store('player');
    if (!player) {
        console.error('❌ Player store bulunamadı!');
        return;
    }

    console.log('🧪 STRESS TEST v2 BAŞLIYOR...');
    console.log('🎯 Hedef: 500 şarkı (auto-refill dahil)');
    console.log('📊 Mevcut queue:', player.queue?.length || 0, 'şarkı');

    // İstatistikler
    const stats = {
        startTime: Date.now(),
        songsPlayed: 0,
        errors: [],
        blockedCalls: 0,
        successfulTransitions: 0,
        refillCount: 0,
        queueEnds: 0
    };

    // Original console.log'u yakala
    const originalLog = console.log;
    const originalWarn = console.warn;

    console.log = function(...args) {
        const msg = args.join(' ');
        if (msg.includes('nextTrack BLOCKED')) {
            stats.blockedCalls++;
        }
        if (msg.includes('nextTrack: has next song')) {
            stats.successfulTransitions++;
        }
        if (msg.includes('NO next song') || msg.includes('auto-refill')) {
            stats.queueEnds++;
        }
        if (msg.includes('refill') || msg.includes('Refill')) {
            stats.refillCount++;
        }
        originalLog.apply(console, args);
    };

    // İlk queue'yu yükle (eğer boşsa)
    if (!player.queue || player.queue.length === 0) {
        console.log('📥 İlk queue yükleniyor...');
        try {
            const response = await fetch('/api/muzibu/queue/initial');
            const data = await response.json();
            if (data.songs && data.songs.length > 0) {
                player.queue = data.songs;
                player.queueIndex = 0;
                console.log(`✅ ${data.songs.length} şarkı yüklendi`);
            }
        } catch (e) {
            console.error('❌ İlk queue yüklenemedi:', e);
        }
    }

    // 500 şarkı test et
    const TARGET = 500;
    const DELAY_MS = 50; // Her şarkı arası bekleme (ms)

    console.log(`\n🚀 ${TARGET} şarkı testi başlıyor (${DELAY_MS}ms aralık)...\n`);

    let lastQueueLength = player.queue?.length || 0;
    let stuckCount = 0;
    let lastIndex = -1;

    for (let i = 0; i < TARGET; i++) {
        try {
            // Progress göster (her 50 şarkıda bir)
            if (i % 50 === 0) {
                const elapsed = ((Date.now() - stats.startTime) / 1000).toFixed(1);
                console.log(`\n📊 Progress: ${i}/${TARGET} (${elapsed}s) - Queue: ${player.queue?.length || 0}, Index: ${player.queueIndex}`);
            }

            // Sıkışma kontrolü - aynı index'te takılı mı?
            if (player.queueIndex === lastIndex) {
                stuckCount++;
                if (stuckCount > 5) {
                    console.warn(`⚠️ Takıldı! Index: ${player.queueIndex}, zorla ilerletiliyor...`);
                    player.queueIndex++;
                    stuckCount = 0;
                }
            } else {
                stuckCount = 0;
                lastIndex = player.queueIndex;
            }

            // Queue bitti mi kontrol et
            if (player.queueIndex >= player.queue.length - 1) {
                console.log(`🔄 Queue sonuna gelindi (${player.queueIndex}/${player.queue.length}), refill bekleniyor...`);
            }

            // nextTrack çağır
            await player.nextTrack(true);
            stats.songsPlayed++;

            // Queue büyüdü mü? (auto-refill oldu)
            if (player.queue?.length > lastQueueLength) {
                console.log(`✨ Auto-refill: ${lastQueueLength} → ${player.queue.length} şarkı`);
                lastQueueLength = player.queue.length;
            }

            // Kısa bekleme
            await new Promise(r => setTimeout(r, DELAY_MS));

        } catch (e) {
            stats.errors.push({ index: i, error: e.message });
            console.error(`❌ Hata (${i}):`, e.message);

            // Hatada durma, devam et
            await new Promise(r => setTimeout(r, 200));
        }
    }

    // Sonuçları göster
    const duration = ((Date.now() - stats.startTime) / 1000).toFixed(1);

    console.log('\n');
    console.log('═══════════════════════════════════════════════');
    console.log('📊 STRESS TEST v2 SONUÇLARI');
    console.log('═══════════════════════════════════════════════');
    console.log(`⏱️  Toplam Süre: ${duration} saniye`);
    console.log(`🎵 Hedef Şarkı: ${TARGET}`);
    console.log(`🎵 Geçilen Şarkı: ${stats.songsPlayed}`);
    console.log(`✅ Başarılı Geçiş: ${stats.successfulTransitions}`);
    console.log(`🛡️  Engellenen Çağrı: ${stats.blockedCalls}`);
    console.log(`🔄 Queue Sonu: ${stats.queueEnds} kez`);
    console.log(`📥 Refill Log: ${stats.refillCount} kez`);
    console.log(`❌ Hata Sayısı: ${stats.errors.length}`);
    console.log(`📍 Son Queue: ${player.queue?.length || 0} şarkı`);
    console.log(`📍 Son Index: ${player.queueIndex}`);
    console.log(`🎵 Son Şarkı: ${player.currentSong?.title || player.currentSong?.song_title || 'N/A'}`);

    // Başarı oranı
    const successRate = ((stats.successfulTransitions / stats.songsPlayed) * 100).toFixed(1);
    console.log(`\n📈 Başarı Oranı: ${successRate}%`);

    if (stats.errors.length > 0) {
        console.log('\n❌ HATALAR (ilk 10):');
        stats.errors.slice(0, 10).forEach(e => console.log(`  - Şarkı ${e.index}: ${e.error}`));
    }

    if (stats.blockedCalls > 0) {
        console.log(`\n🛡️ ${stats.blockedCalls} çift tetikleme engellendi (guard çalışıyor)`);
    }

    console.log('═══════════════════════════════════════════════');

    // Console'u geri yükle
    console.log = originalLog;
    console.warn = originalWarn;

    return stats;
})();
