/**
 * 🧪 Muzibu Infinite Queue Test Script
 * Browser console'da çalıştır: copy-paste ve Enter
 *
 * Test Senaryosu:
 * 1. Her şarkıyı 3 saniye çal (başlangıç + bitiş test)
 * 2. Otomatik next şarkıya geç
 * 3. Queue bitince transition kontrolü
 * 4. Infinite loop kontrolü
 */

(async function muzibuQueueTest() {
    console.log('🧪 MUZIBU INFINITE QUEUE TEST BAŞLADI');
    console.log('=' .repeat(60));

    const player = Alpine.$data(document.querySelector('[x-data*="muzibuPlayer"]'));

    if (!player) {
        console.error('❌ Player bulunamadı! Muzibu sayfasında mısınız?');
        return;
    }

    const store = Alpine.store('muzibu');
    const context = store.getPlayContext();

    console.log('📊 Test Bilgileri:');
    console.log('  Context Type:', context?.type || 'YOK');
    console.log('  Context ID:', context?.id || 'YOK');
    console.log('  Queue Length:', player.queue.length);
    console.log('  Queue Index:', player.queueIndex);
    console.log('=' .repeat(60));

    if (!context) {
        console.error('❌ Play context yok! Önce bir şarkı/playlist/album başlatın.');
        return;
    }

    // Test ayarları
    const config = {
        songsToTest: 20, // Kaç şarkı test edilecek
        playDuration: 3000, // Her şarkıyı 3 saniye çal (ms)
        skipToEnd: true, // Şarkı bitişini test et (son saniyeye atla)
        checkTransition: true, // Transition kontrolü
        logInterval: 1000 // Log interval (ms)
    };

    console.log('⚙️ Test Ayarları:');
    console.log('  Test Edilecek Şarkı:', config.songsToTest);
    console.log('  Çalma Süresi:', config.playDuration / 1000, 'saniye');
    console.log('  Bitiş Testi:', config.skipToEnd ? 'AÇIK' : 'KAPALI');
    console.log('=' .repeat(60));

    let testResults = {
        tested: 0,
        transitions: 0,
        errors: 0,
        startContext: context.type,
        contexts: [context.type]
    };

    // Test fonksiyonu
    async function testSong(index) {
        const song = player.queue[player.queueIndex];

        if (!song) {
            console.log('⚠️ Queue boş, refill bekleniyor...');
            await new Promise(resolve => setTimeout(resolve, 2000));
            return;
        }

        console.log('');
        console.log(`🎵 [${index + 1}/${config.songsToTest}] Test: ${song.song_title?.tr || song.song_title}`);
        console.log(`   Queue: ${player.queueIndex + 1}/${player.queue.length}`);
        console.log(`   Context: ${store.getPlayContext()?.type} (${store.getPlayContext()?.id})`);

        // Şarkıyı çal
        if (!player.isPlaying) {
            player.togglePlayPause();
            await new Promise(resolve => setTimeout(resolve, 500));
        }

        // Başlangıçı test et (3 saniye çal)
        console.log('   ▶️ Başlangıç testi (3sn)...');
        await new Promise(resolve => setTimeout(resolve, config.playDuration));

        // Bitişi test et (son saniyeye atla)
        if (config.skipToEnd) {
            const duration = player.duration;
            if (duration > 5) {
                console.log('   ⏩ Bitiş testi (son 2 saniye)...');

                // HLS veya Howler'a göre seek yap
                if (player.isHlsStream && player.hls) {
                    const audio = player.getActiveHlsAudio();
                    if (audio) {
                        audio.currentTime = duration - 2;
                    }
                } else if (player.howl) {
                    player.howl.seek(duration - 2);
                }

                await new Promise(resolve => setTimeout(resolve, 2500));
            }
        }

        // Sonraki şarkıya geç
        console.log('   ⏭️ Sonraki şarkıya geçiliyor...');
        player.playNext();

        testResults.tested++;

        // Transition kontrolü
        const newContext = store.getPlayContext();
        if (newContext && newContext.type !== testResults.contexts[testResults.contexts.length - 1]) {
            console.log('');
            console.log('🔄 TRANSITION TESPIT EDİLDİ!');
            console.log(`   ${testResults.contexts[testResults.contexts.length - 1]} → ${newContext.type}`);
            console.log(`   Context ID: ${newContext.id}`);
            console.log('');

            testResults.transitions++;
            testResults.contexts.push(newContext.type);
        }

        await new Promise(resolve => setTimeout(resolve, 1000));
    }

    // Testi başlat
    try {
        for (let i = 0; i < config.songsToTest; i++) {
            await testSong(i);

            // Her 5 şarkıda bir özet
            if ((i + 1) % 5 === 0) {
                console.log('');
                console.log('📊 Ara Özet:');
                console.log(`   Test Edilen: ${testResults.tested}`);
                console.log(`   Transition: ${testResults.transitions}`);
                console.log(`   Context Geçmişi: ${testResults.contexts.join(' → ')}`);
                console.log('');
            }
        }

        // Final rapor
        console.log('');
        console.log('=' .repeat(60));
        console.log('✅ TEST TAMAMLANDI!');
        console.log('=' .repeat(60));
        console.log('📊 Test Sonuçları:');
        console.log(`   Test Edilen Şarkı: ${testResults.tested}`);
        console.log(`   Transition Sayısı: ${testResults.transitions}`);
        console.log(`   Başlangıç Context: ${testResults.startContext}`);
        console.log(`   Son Context: ${testResults.contexts[testResults.contexts.length - 1]}`);
        console.log(`   Context Geçmişi: ${testResults.contexts.join(' → ')}`);
        console.log('');
        console.log('   Final Queue Length:', player.queue.length);
        console.log('   Final Queue Index:', player.queueIndex);
        console.log('=' .repeat(60));

        // Infinite loop kontrolü
        const finalContext = store.getPlayContext();
        if (finalContext?.type === 'genre') {
            console.log('');
            console.log('♾️ INFINITE LOOP AKTİF!');
            console.log('   Genre:', finalContext.id);
            console.log('   Müzik sonsuza kadar çalmaya devam edecek!');
        } else if (['sector', 'radio', 'recent'].includes(finalContext?.type)) {
            console.log('');
            console.log('♾️ SELF-LOOP AKTİF!');
            console.log('   Type:', finalContext.type);
            console.log('   Kendi içinde sonsuz döngü!');
        }

        console.log('');
        console.log('💡 Konsol loglarını inceleyin:');
        console.log('   - "🔄 Context Transition" mesajları');
        console.log('   - "🔍 Queue Check" mesajları');
        console.log('   - "♾️ Infinite Loop" mesajları');
        console.log('');

    } catch (error) {
        console.error('❌ Test hatası:', error);
        testResults.errors++;
    }
})();
