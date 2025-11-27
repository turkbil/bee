/**
 * Simple Canvas Waveform Player
 * WaveSurfer.js yerine basit, stabil Canvas implementasyonu
 */

(function() {
    'use strict';

    // Canvas & Context
    let canvas = null;
    let ctx = null;
    let bars = [];
    let currentProgress = 0;
    let animationFrame = null;

    // Config
    const config = {
        barCount: 100,           // Bar sayısı
        barWidth: 3,             // Bar genişliği
        barGap: 1,               // Bar arası boşluk
        barMinHeight: 0.15,      // Minimum bar yüksekliği
        barMaxHeight: 0.95,      // Maximum bar yüksekliği
        colorPlayed: '#1DB954',  // Oynatılan kısım (Spotify green)
        colorUnplayed: '#4b5563' // Oynanmamış kısım (gray)
    };

    /**
     * Canvas'ı başlat
     */
    function initCanvas() {
        canvas = document.getElementById('waveform');
        if (!canvas) {
            console.warn('⚠️ Canvas #waveform bulunamadı');
            return false;
        }

        ctx = canvas.getContext('2d');

        // Canvas boyutunu ayarla
        resizeCanvas();

        // Web Audio API başlat
        initAudioContext();

        // İlk çizim
        drawWaveform();

        // Click event
        canvas.addEventListener('click', handleClick);

        // Resize event
        window.addEventListener('resize', handleResize);

        console.log('✅ Canvas Waveform initialized with real-time frequency analysis');
        return true;
    }

    /**
     * Canvas boyutunu parent'a göre ayarla
     */
    function resizeCanvas() {
        if (!canvas) return;

        const container = canvas.parentElement;
        const rect = container.getBoundingClientRect();

        // Retina için pixel ratio
        const dpr = window.devicePixelRatio || 1;

        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;

        canvas.style.width = rect.width + 'px';
        canvas.style.height = rect.height + 'px';

        ctx.scale(dpr, dpr);
    }

    /**
     * Resize event handler
     */
    function handleResize() {
        resizeCanvas();
        drawWaveform();
    }

    /**
     * Web Audio API başlat - Gerçek frekans analizi için
     */
    function initAudioContext() {
        if (audioContext) return; // Zaten başlatılmış

        try {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            analyser = audioContext.createAnalyser();
            analyser.fftSize = 256; // Daha az bar için optimize
            analyser.smoothingTimeConstant = 0.8; // Yumuşak geçişler
            bufferLength = analyser.frequencyBinCount;
            dataArray = new Uint8Array(bufferLength);

            console.log('🎵 Web Audio API initialized for real-time frequency analysis');
        } catch (e) {
            console.error('Web Audio API not supported:', e);
        }
    }

    /**
     * Audio source'u analyser'a bağla
     */
    function connectAudioSource() {
        if (!audioContext || !analyser) {
            initAudioContext();
        }

        if (!audioContext) return;

        // AudioContext resume et (autoplay policy için)
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }

        // HLS audio element'i bul ve bağla
        const hlsAudio = document.getElementById('hlsAudio');
        const hlsAudioNext = document.getElementById('hlsAudioNext');

        console.log('🔍 Audio elements:', {
            hlsAudio: hlsAudio ? 'found' : 'NOT FOUND',
            hlsAudioNext: hlsAudioNext ? 'found' : 'NOT FOUND',
            hlsAudioSourceNode: hlsAudio?.sourceNode ? 'exists' : 'none',
            hlsAudioNextSourceNode: hlsAudioNext?.sourceNode ? 'exists' : 'none'
        });

        // Priority: hlsAudio -> hlsAudioNext (paused olsa bile bağla)
        let audioElement = hlsAudio || hlsAudioNext;

        if (!audioElement) {
            console.warn('⚠️ No audio element found!');
            return;
        }

        if (!audioElement.sourceNode) {
            try {
                const source = audioContext.createMediaElementSource(audioElement);
                source.connect(analyser);
                analyser.connect(audioContext.destination);
                audioElement.sourceNode = source;
                console.log('🔌 Audio source connected to analyser:', audioElement.id);
            } catch (e) {
                console.error('❌ Failed to connect audio source:', e.message);
            }
        } else {
            console.log('✅ Audio source already connected');
        }
    }

    /**
     * Gerçek zamanlı frekans verilerinden bar yükseklikleri oluştur
     */
    function getBarsFromFrequencyData() {
        if (!analyser || !dataArray) {
            // Analyser yoksa düz orta seviye bar'lar göster
            return Array(config.barCount).fill(0.3);
        }

        // Gerçek frekans verilerini al
        analyser.getByteFrequencyData(dataArray);

        const bars = [];
        const step = Math.floor(bufferLength / config.barCount);

        for (let i = 0; i < config.barCount; i++) {
            const index = i * step;
            const value = dataArray[index] || 0;

            // 0-255 aralığını 0-1 aralığına normalize et
            let normalizedHeight = value / 255;

            // Boost uygula (daha görünür yap)
            normalizedHeight = Math.pow(normalizedHeight, 0.7);

            // Minimum yükseklik ekle
            normalizedHeight = Math.max(config.barMinHeight, normalizedHeight);

            bars.push(normalizedHeight);
        }

        return bars;
    }

    /**
     * Waveform çiz - Gerçek zamanlı frekans verileriyle
     */
    function drawWaveform() {
        if (!canvas || !ctx) return;

        const width = canvas.width / (window.devicePixelRatio || 1);
        const height = canvas.height / (window.devicePixelRatio || 1);

        // Clear canvas
        ctx.clearRect(0, 0, width, height);

        // Gerçek zamanlı frekans verilerini al
        const currentBars = getBarsFromFrequencyData();

        // Bar width calculation
        const totalBarWidth = config.barWidth + config.barGap;
        const startX = (width - (currentBars.length * totalBarWidth)) / 2; // Center align

        // Draw bars
        currentBars.forEach((barHeight, index) => {
            const x = startX + (index * totalBarWidth);
            const barActualHeight = height * barHeight;
            const y = (height - barActualHeight) / 2; // Vertical center

            // Progress oranına göre renk
            const barProgress = index / currentBars.length;
            const color = barProgress <= currentProgress ? config.colorPlayed : config.colorUnplayed;

            ctx.fillStyle = color;
            ctx.fillRect(x, y, config.barWidth, barActualHeight);
        });
    }

    /**
     * Click event handler - Seek (tüm audio source'lar)
     */
    function handleClick(event) {
        if (!canvas) return;

        const rect = canvas.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const clickProgress = x / rect.width;

        console.log('🎯 Waveform clicked at:', clickProgress);

        const source = getActiveAudioSource();
        if (!source) {
            console.warn('⚠️ No active audio source for seeking');
            return;
        }

        const seekTime = clickProgress * source.duration;

        // Seek based on source type
        if (source.type === 'howler') {
            if (window.Howler && window.Howler._howls.length > 0) {
                window.Howler._howls[0].seek(seekTime);
                console.log('⏩ Howler seeking to:', seekTime, 'seconds');
            }
        } else if (source.element) {
            // HLS (primary or next)
            source.element.currentTime = seekTime;
            console.log('⏩ HLS (' + source.type + ') seeking to:', seekTime, 'seconds');
        }

        // Hemen progress güncelle
        currentProgress = clickProgress;
        drawWaveform();
    }

    /**
     * Aktif audio source'u bul (Howler, HLS primary, HLS next)
     */
    function getActiveAudioSource() {
        // 1. Howler.js kontrol et
        if (window.Howler && window.Howler._howls.length > 0) {
            const sound = window.Howler._howls[0];
            if (sound.playing()) {
                return {
                    type: 'howler',
                    seek: sound.seek() || 0,
                    duration: sound.duration() || 1,
                    isPlaying: true
                };
            }
        }

        // 2. HLS primary audio kontrol et
        const hlsAudio = document.getElementById('hlsAudio');
        if (hlsAudio && !hlsAudio.paused) {
            const duration = hlsAudio.duration || 1;
            // Infinity veya NaN kontrolü
            const validDuration = isFinite(duration) ? duration : 1;

            return {
                type: 'hls',
                element: hlsAudio,
                seek: hlsAudio.currentTime || 0,
                duration: validDuration,
                isPlaying: !hlsAudio.paused && !hlsAudio.ended
            };
        }

        // 3. HLS next audio kontrol et (crossfade için)
        const hlsAudioNext = document.getElementById('hlsAudioNext');
        if (hlsAudioNext && !hlsAudioNext.paused) {
            const duration = hlsAudioNext.duration || 1;
            const validDuration = isFinite(duration) ? duration : 1;

            return {
                type: 'hls-next',
                element: hlsAudioNext,
                seek: hlsAudioNext.currentTime || 0,
                duration: validDuration,
                isPlaying: !hlsAudioNext.paused && !hlsAudioNext.ended
            };
        }

        // Hiçbir source çalmıyorsa
        return null;
    }

    /**
     * Progress güncelle - Tüm audio source'lar ile sync
     */
    function updateProgress() {
        const source = getActiveAudioSource();

        if (!source) {
            // Hiçbir şey çalmıyorsa loop'u durdur
            return;
        }

        // Progress hesapla
        const progress = source.seek / source.duration;

        // Progress değişti mi?
        if (Math.abs(currentProgress - progress) > 0.001) {
            currentProgress = progress;
            drawWaveform();
        }

        // Şarkı çalıyorsa loop devam et
        if (source.isPlaying) {
            animationFrame = requestAnimationFrame(updateProgress);
        }
    }

    /**
     * Çalma başladığında
     */
    function onPlayStart() {
        console.log('▶️ Play started');

        // Audio source'u analyser'a bağla
        connectAudioSource();

        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
        }
        animationFrame = requestAnimationFrame(updateProgress);
    }

    /**
     * Durdurulduğunda
     */
    function onPlayStop() {
        console.log('⏸️ Play stopped');
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
    }

    /**
     * Reset - Yeni şarkı için
     */
    function reset() {
        console.log('🔄 Waveform reset');
        currentProgress = 0;
        drawWaveform();

        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
    }

    /**
     * Destroy - Cleanup
     */
    function destroy() {
        console.log('🗑️ Waveform destroyed');

        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }

        if (canvas) {
            canvas.removeEventListener('click', handleClick);
        }

        window.removeEventListener('resize', handleResize);

        currentProgress = 0;
        bars = [];
    }

    /**
     * Yeni şarkı yükle
     */
    function loadSong(url) {
        console.log('🎵 Loading song:', url);

        // İlk yüklemede canvas'ı init et
        if (!canvas) {
            initCanvas();
        } else {
            reset();
        }
    }

    /**
     * Global API
     */
    window.WaveSurferPlayer = {
        init: initCanvas,
        load: loadSong,
        onPlay: onPlayStart,
        onPause: onPlayStop,
        onStop: onPlayStop,
        destroy: destroy,
        reset: reset
    };

    console.log('✅ Simple Canvas Waveform loaded');

})();
