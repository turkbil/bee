/**
 * Studio Editor - 3D Küp Animasyonlu Sevimli Loader
 */

window.StudioLoader = (function() {
    // Yükleme aşamaları - daha sempatik mesajlar
    const loadingStages = [
        { text: "Editör bileşenlerini topluyorum...", icon: "fa-shapes" },
        { text: "Yaratıcılık motorunu çalıştırıyorum...", icon: "fa-lightbulb" },
        { text: "Tasarım şablonlarını hazırlıyorum...", icon: "fa-paint-brush" },
        { text: "Görsel unsurları yerleştiriyorum...", icon: "fa-magic" },
        { text: "Bileşenleri birleştiriyorum...", icon: "fa-object-group" },
        { text: "Renk paletlerini ayarlıyorum...", icon: "fa-palette" },
        { text: "Editör araçlarını kurcalıyorum...", icon: "fa-tools" },
        { text: "Kullanıcı arayüzünü parlatıyorum...", icon: "fa-sparkles" },
        { text: "Son rötuşları yapıyorum...", icon: "fa-wand-magic-sparkles" },
        { text: "Her şey neredeyse hazır...", icon: "fa-check-double" }
    ];
    
    // İlerleme durumu
    let currentProgress = 0;
    let loaderElement = null;
    let progressInterval = null;
    let cubeFaces = null;
    let customDelay = 0;
    
    /**
     * Yükleme ekranını göster
     * @param {number} delay - Milisaniye cinsinden gecikme süresi (0: otomatik)
     */
    function show(delay = 0) {
        // Gecikme süresini ayarla
        customDelay = delay;
        
        // Mevcut yükleme göstergesini temizle
        const existingLoader = document.querySelector('.studio-loader');
        if (existingLoader) {
            existingLoader.remove();
        }
        
        // Yükleme göstergesi ekle
        loaderElement = document.createElement('div');
        loaderElement.className = 'studio-loader';
        loaderElement.style.position = 'fixed';
        loaderElement.style.top = '0';
        loaderElement.style.left = '0';
        loaderElement.style.width = '100%';
        loaderElement.style.height = '100%';
        loaderElement.style.backgroundColor = '#f8fafc';
        loaderElement.style.display = 'flex';
        loaderElement.style.alignItems = 'center';
        loaderElement.style.justifyContent = 'center';
        loaderElement.style.zIndex = '10000';
        loaderElement.style.transition = 'opacity 0.5s ease';
        loaderElement.style.perspective = '1200px';
        loaderElement.style.overflow = 'hidden';
        
        // Arka plan elementleri - kutucuklar
        let backgroundTiles = '';
        const tileCount = 24;
        const tileColors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f97316', '#10b981', '#6366f1'];
        
        for (let i = 0; i < tileCount; i++) {
            const size = Math.floor(Math.random() * 50) + 30;
            const x = Math.floor(Math.random() * 100);
            const y = Math.floor(Math.random() * 100);
            const color = tileColors[Math.floor(Math.random() * tileColors.length)];
            const rotateZ = Math.floor(Math.random() * 360);
            const opacity = Math.random() * 0.1 + 0.02;
            
            backgroundTiles += `
                <div class="bg-tile" style="
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    border-radius: 12px;
                    top: ${y}%;
                    left: ${x}%;
                    background-color: ${color};
                    opacity: ${opacity};
                    transform: rotate(${rotateZ}deg);
                    filter: blur(2px);
                    animation: floating 15s infinite ease-in-out ${Math.random() * 5}s;
                "></div>
            `;
        }
        
        // Sevimli emoji yüzleri
        const faces = [
            { emoji: '😊', color: '#3b82f6' }, // Mavi gülümseyen yüz
            { emoji: '🤩', color: '#8b5cf6' }, // Mor yıldızlı gözler
            { emoji: '🎨', color: '#ec4899' }, // Pembe palet
            { emoji: '🧩', color: '#f97316' }, // Turuncu yapboz
            { emoji: '✨', color: '#10b981' }, // Yeşil yıldızlar
            { emoji: '💡', color: '#6366f1' }  // Mavi/mor ampul
        ];
        
        loaderElement.innerHTML = `
            <div class="studio-3d-background" style="position: absolute; width: 100%; height: 100%; z-index: 1;">
                ${backgroundTiles}
            </div>
            
            <div class="studio-loader-content" style="text-align: center; background-color: white; padding: 40px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); max-width: 550px; width: 90%; position: relative; z-index: 10; transform-style: preserve-3d; transform: translateZ(50px);">
                <div style="margin-bottom: 30px; position: relative;">
                    <div style="font-size: 32px; position: relative; z-index: 2; color: #0f172a; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 10px;">
                        <span style="display: inline-block; margin-right: 8px; animation: wave 1.8s ease-in-out infinite;">👋</span> 
                        Studio Editor
                    </div>
                    <div id="loader-subtitle" style="color: #64748b; font-size: 16px; letter-spacing: 0.5px;">
                        Hayallerinizi gerçeğe dönüştüren editör
                    </div>
                </div>
                
                <div style="position: relative; margin-bottom: 40px; perspective: 600px;">
                    <div class="scene" style="width: 150px; height: 150px; margin: 0 auto; position: relative; perspective: 600px; transform-style: preserve-3d;">
                        <div id="cube" style="width: 100%; height: 100%; position: relative; transform-style: preserve-3d; transform: translateZ(-75px); animation: cute-rotate 12s infinite ease-in-out;">
                            <div class="cube-face front" style="position: absolute; width: 100%; height: 100%; background-color: ${faces[0].color}; transform: rotateY(0deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 50px; border-radius: 16px;">
                                ${faces[0].emoji}
                            </div>
                            <div class="cube-face back" style="position: absolute; width: 100%; height: 100%; background-color: ${faces[1].color}; transform: rotateY(180deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 50px; border-radius: 16px;">
                                ${faces[1].emoji}
                            </div>
                            <div class="cube-face right" style="position: absolute; width: 100%; height: 100%; background-color: ${faces[2].color}; transform: rotateY(90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 50px; border-radius: 16px;">
                                ${faces[2].emoji}
                            </div>
                            <div class="cube-face left" style="position: absolute; width: 100%; height: 100%; background-color: ${faces[3].color}; transform: rotateY(-90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 50px; border-radius: 16px;">
                                ${faces[3].emoji}
                            </div>
                            <div class="cube-face top" style="position: absolute; width: 100%; height: 100%; background-color: ${faces[4].color}; transform: rotateX(90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 50px; border-radius: 16px;">
                                ${faces[4].emoji}
                            </div>
                            <div class="cube-face bottom" style="position: absolute; width: 100%; height: 100%; background-color: ${faces[5].color}; transform: rotateX(-90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 50px; border-radius: 16px;">
                                ${faces[5].emoji}
                            </div>
                        </div>
                    </div>
                    
                    <div class="cube-shadow" style="
                        width: 150px;
                        height: 20px;
                        background: radial-gradient(ellipse at center, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0) 70%);
                        border-radius: 50%;
                        margin: 20px auto 0;
                        animation: shadow-pulse 1.5s infinite alternate;
                    "></div>
                </div>
                
                <div id="loading-icon" style="margin-bottom: 20px; font-size: 32px; color: #3b82f6; filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.3));">
                    <i class="fas fa-shapes"></i>
                </div>
                
                <h4 id="loading-text" style="margin-bottom: 30px; color: #334155; font-weight: 600; font-size: 17px;">
                    Editör bileşenlerini topluyorum...
                </h4>
                
                <div style="position: relative; height: 10px; background: #f1f5f9; border-radius: 5px; overflow: hidden; margin-bottom: 20px; transform: translateZ(10px);">
                    <div id="loading-progress" style="height: 100%; width: 0%; border-radius: 5px; transition: width 0.3s ease;">
                        <div style="width: 100%; height: 100%; background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899, #f97316); background-size: 300% 100%; animation: progress-gradient 2s ease infinite;"></div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; color: #64748b; font-size: 14px; font-weight: 500;">
                    <div id="progress-step">Adım 1/10</div>
                    <div id="loading-status" style="font-weight: 600; color: #0f172a;">
                        0%
                    </div>
                </div>
                
                <style>
                    @keyframes cute-rotate {
                        0%, 100% { transform: translateZ(-75px) rotateY(0deg) rotateX(0deg); }
                        25% { transform: translateZ(-75px) rotateY(90deg) rotateX(90deg); }
                        50% { transform: translateZ(-75px) rotateY(180deg) rotateX(0deg); }
                        75% { transform: translateZ(-75px) rotateY(270deg) rotateX(-90deg); }
                    }
                    
                    @keyframes floating {
                        0%, 100% { transform: translate(0, 0) rotate(var(--rotate, 0deg)); }
                        50% { transform: translate(10px, -10px) rotate(calc(var(--rotate, 0deg) + 5deg)); }
                    }
                    
                    @keyframes progress-gradient {
                        0% { background-position: 0% 0%; }
                        50% { background-position: 100% 0%; }
                        100% { background-position: 0% 0%; }
                    }
                    
                    @keyframes shadow-pulse {
                        from { opacity: 0.2; transform: scale(0.85); }
                        to { opacity: 0.4; transform: scale(1); }
                    }
                    
                    @keyframes wave {
                        0% { transform: rotate(0deg); }
                        10% { transform: rotate(14deg); }
                        20% { transform: rotate(-8deg); }
                        30% { transform: rotate(14deg); }
                        40% { transform: rotate(-4deg); }
                        50% { transform: rotate(10deg); }
                        60% { transform: rotate(0deg); }
                        100% { transform: rotate(0deg); }
                    }
                    
                    .bg-tile {
                        --rotate: 0deg;
                    }
                    
                    .cube-face {
                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                        backface-visibility: visible;
                        border: 3px solid white;
                    }
                </style>
            </div>
        `;
        
        document.body.appendChild(loaderElement);
        
        // Tüm bg-tile elementlerine CSS rotation değişkeni ekle
        document.querySelectorAll('.bg-tile').forEach(tile => {
            const rotation = tile.style.transform.match(/rotate\(([^)]+)\)/)[1];
            tile.style.setProperty('--rotate', rotation);
        });
        
        // Küp yüzleri
        cubeFaces = {
            front: document.querySelector('.cube-face.front'),
            back: document.querySelector('.cube-face.back'),
            right: document.querySelector('.cube-face.right'),
            left: document.querySelector('.cube-face.left'),
            top: document.querySelector('.cube-face.top'),
            bottom: document.querySelector('.cube-face.bottom')
        };
        
        // Aşamalı ilerlemeyi başlat
        startLoading();
        
        return loaderElement;
    }
    
    /**
     * Yükleme ekranını gizle
     */
    function hide() {
        if (!loaderElement) return;
        
        // İlerleme interval'ini temizle
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }
        
        // Tamamlandı göster (%100)
        updateProgress(100, true);
        
        // Delay kontrolü - 0 ise hemen kapan, değilse bekle
        const hideDelay = (customDelay > 0) ? customDelay : 1200;
        
        // Bekle ve animasyonu gizle
        setTimeout(() => {
            // Küp animasyonunu hızlandır ve zafer dansı yaptır
            const cube = document.getElementById('cube');
            if (cube) {
                cube.style.animation = 'cute-rotate 2s infinite ease-in-out';
            }
            
            // Başarı mesajını göster
            setTimeout(() => {
                loaderElement.style.opacity = '0';
                setTimeout(() => {
                    if (loaderElement && loaderElement.parentNode) {
                        loaderElement.parentNode.removeChild(loaderElement);
                        loaderElement = null;
                        cubeFaces = null;
                    }
                }, 500);
            }, 800);
        }, hideDelay);
    }
    
    /**
     * İlerleme çubuğunu güncelle
     * @param {number} progress - İlerleme yüzdesi
     * @param {boolean} isCompleted - Tamamlandı mı
     */
    function updateProgress(progress, isCompleted = false) {
        const loadingText = document.getElementById('loading-text');
        const loadingIcon = document.getElementById('loading-icon');
        const loadingProgress = document.getElementById('loading-progress');
        const loadingStatus = document.getElementById('loading-status');
        const progressStep = document.getElementById('progress-step');
        
        if (!loadingText || !loadingIcon || !loadingProgress || !loadingStatus) return;
        
        // İlerleme çubuğunu güncelle
        loadingProgress.style.width = `${progress}%`;
        loadingStatus.textContent = `${progress}%`;
        
        const stageIndex = Math.min(Math.floor(progress / 10), loadingStages.length - 1);
        
        if (progressStep) {
            progressStep.textContent = `Adım ${stageIndex + 1}/10`;
        }
        
        if (isCompleted) {
            // Tamamlandı durumu
            loadingText.textContent = "Tamamlandı! Başlıyoruz... 🚀";
            loadingText.style.color = "#10b981";
            loadingIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            loadingIcon.style.color = '#10b981';
            loadingIcon.style.transform = 'scale(1.2)';
            loadingIcon.style.transition = 'transform 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28)';
            
            // Konfeti efekti ekle
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 9999;
                overflow: hidden;
            `;
            
            // 30 konfeti parçası oluştur
            for (let i = 0; i < 30; i++) {
                const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f97316', '#10b981', '#eab308'];
                const color = colors[Math.floor(Math.random() * colors.length)];
                
                const confettiPiece = document.createElement('div');
                confettiPiece.style.cssText = `
                    position: absolute;
                    width: ${Math.random() * 10 + 5}px;
                    height: ${Math.random() * 10 + 5}px;
                    background-color: ${color};
                    top: -10px;
                    left: ${Math.random() * 100}%;
                    border-radius: ${Math.random() > 0.5 ? '50%' : '0'};
                    animation: confetti ${Math.random() * 2 + 1}s ease-in forwards;
                    opacity: ${Math.random() * 0.8 + 0.2};
                `;
                
                confetti.appendChild(confettiPiece);
            }
            
            // Animation keyframes ekle
            const style = document.createElement('style');
            style.textContent = `
                @keyframes confetti {
                    from {
                        transform: translateY(0) rotate(0deg);
                    }
                    to {
                        transform: translateY(500px) rotate(${Math.random() * 360}deg);
                    }
                }
            `;
            
            document.head.appendChild(style);
            loaderElement.appendChild(confetti);
            
            return;
        }
        
        // Aşamaları göster
        loadingText.textContent = loadingStages[stageIndex].text;
        loadingIcon.innerHTML = `<i class="fas ${loadingStages[stageIndex].icon}"></i>`;
    }
    
    /**
     * Aşamalı ilerlemeyi başlat
     */
    function startLoading() {
        currentProgress = 0;
        
        if (progressInterval) {
            clearInterval(progressInterval);
        }
        
        progressInterval = setInterval(() => {
            // İlerleme hızını hesapla - daha çok gerçek bir yükleme hissi verecek şekilde
            let increment = 0.5;
            
            // Özellikle bazı noktalarda duraklama/hızlanma olduğu hissi ver
            if (currentProgress < 20) {
                increment = 1.2; // Başta hızlı git
            } else if (currentProgress > 20 && currentProgress < 25) {
                increment = 0.1; // 20-25 arası yavaşla (sanki bir şey yüklüyor)
            } else if (currentProgress >= 25 && currentProgress < 35) {
                increment = 0.8; // 25-35 arası hızlan (yüklenen şey tamamlandı)
            } else if (currentProgress >= 35 && currentProgress < 60) {
                increment = 0.5; // Normal hız
            } else if (currentProgress >= 60 && currentProgress < 65) {
                increment = 0.1; // 60-65 arası tekrar yavaşla
            } else if (currentProgress >= 65 && currentProgress < 75) {
                increment = 0.7; // 65-75 arası biraz hızlan
            } else if (currentProgress >= 75 && currentProgress < 85) {
                increment = 0.3; // 75-85 arası yavaşla (son işlemler)
            } else if (currentProgress >= 85 && currentProgress < 95) {
                increment = 0.2; // 85-95 arası çok yavaş (neredeyse bitti)
            } else {
                increment = 0.1; // Son kısım çok yavaş (bitiriş)
            }
            
            // Rastgele dalgalanmalar
            if (Math.random() > 0.75) {
                increment *= Math.random() * 0.5 + 0.75; // Bazen rastgele hızda ilerle
            }
            
            currentProgress += increment;
            
            if (currentProgress >= 100) {
                currentProgress = 100;
                clearInterval(progressInterval);
            }
            
            updateProgress(Math.floor(currentProgress));
        }, 100);
    }
    
    return {
        show: show,
        hide: hide,
        updateProgress: updateProgress
    };
})();