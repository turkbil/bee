/**
 * Studio Editor - 3D Perspective Loader Modülü
 */

window.StudioLoader = (function() {
    // Yükleme aşamaları
    const loadingStages = [
        { text: "Render motorunu hazırlıyor...", icon: "fa-cube" },
        { text: "Komponent sistemi başlatılıyor...", icon: "fa-layer-group" },
        { text: "Görsel elementler yükleniyor...", icon: "fa-th" },
        { text: "3D motorunu yapılandırıyor...", icon: "fa-cubes" },
        { text: "Materyal kütüphanesi entegre ediliyor...", icon: "fa-palette" },
        { text: "Editör panelleri oluşturuluyor...", icon: "fa-columns" },
        { text: "Widget bileşenleri hazırlanıyor...", icon: "fa-puzzle-piece" },
        { text: "Stilşablonları yükleniyor...", icon: "fa-paint-brush" },
        { text: "Kullanıcı arayüzü optimize ediliyor...", icon: "fa-desktop" },
        { text: "Son dokunuşlar yapılıyor...", icon: "fa-magic" }
    ];
    
    // İlerleme durumu
    let currentProgress = 0;
    let loaderElement = null;
    let progressInterval = null;
    let cubeFaces = null;
    
    /**
     * Yükleme ekranını göster
     */
    function show() {
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
        loaderElement.style.backgroundColor = '#000';
        loaderElement.style.display = 'flex';
        loaderElement.style.alignItems = 'center';
        loaderElement.style.justifyContent = 'center';
        loaderElement.style.zIndex = '10000';
        loaderElement.style.transition = 'opacity 0.5s ease';
        loaderElement.style.perspective = '1200px';
        loaderElement.style.overflow = 'hidden';
        
        // 3D background tiles oluştur
        let backgroundTiles = '';
        const tileCount = 30;
        for (let i = 0; i < tileCount; i++) {
            const size = Math.floor(Math.random() * 150) + 50;
            const x = Math.floor(Math.random() * 100);
            const y = Math.floor(Math.random() * 100);
            const z = Math.floor(Math.random() * 500) - 250;
            const rotateX = Math.floor(Math.random() * 360);
            const rotateY = Math.floor(Math.random() * 360);
            const rotateZ = Math.floor(Math.random() * 360);
            const duration = Math.random() * 30 + 15;
            
            backgroundTiles += `
                <div class="bg-tile" style="
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    top: ${y}%;
                    left: ${x}%;
                    transform: translateZ(${z}px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) rotateZ(${rotateZ}deg);
                    background-color: rgba(30, 64, 175, 0.1);
                    border: 1px solid rgba(30, 64, 175, 0.2);
                    animation: rotate3D ${duration}s infinite linear;
                    box-shadow: 0 0 20px rgba(30, 64, 175, 0.1);
                "></div>
            `;
        }
        
        const colors = ['#3b82f6', '#8b5cf6', '#0ea5e9', '#6366f1', '#0891b2'];
        
        loaderElement.innerHTML = `
            <div class="studio-3d-background" style="position: absolute; width: 100%; height: 100%; z-index: 1;">
                ${backgroundTiles}
            </div>
            
            <div class="studio-loader-content" style="text-align: center; background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); padding: 40px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3); max-width: 550px; width: 90%; position: relative; z-index: 10; border: 1px solid rgba(30, 64, 175, 0.15); transform-style: preserve-3d; transform: translateZ(50px);">
                <div style="margin-bottom: 40px; position: relative;">
                    <div style="font-size: 36px; position: relative; z-index: 2; color: #fff; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 10px; text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);">
                        <i class="fas fa-cube"></i> Studio Editor
                    </div>
                    <div id="loader-subtitle" style="color: rgba(255,255,255,0.7); font-size: 16px; letter-spacing: 1px;">
                        3D Content Creation Platform
                    </div>
                </div>
                
                <div style="position: relative; margin-bottom: 40px; perspective: 600px;">
                    <div class="scene" style="width: 150px; height: 150px; margin: 0 auto; position: relative; perspective: 600px; transform-style: preserve-3d;">
                        <div id="cube" style="width: 100%; height: 100%; position: relative; transform-style: preserve-3d; transform: translateZ(-75px); animation: cube-rotate 20s infinite linear;">
                            <div class="cube-face front" style="position: absolute; width: 100%; height: 100%; background: linear-gradient(135deg, ${colors[0]} 0%, ${colors[1]} 100%); transform: rotateY(0deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 30px; color: white;">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="cube-face back" style="position: absolute; width: 100%; height: 100%; background: linear-gradient(135deg, ${colors[1]} 0%, ${colors[2]} 100%); transform: rotateY(180deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 30px; color: white;">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="cube-face right" style="position: absolute; width: 100%; height: 100%; background: linear-gradient(135deg, ${colors[2]} 0%, ${colors[3]} 100%); transform: rotateY(90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 30px; color: white;">
                                <i class="fas fa-paint-brush"></i>
                            </div>
                            <div class="cube-face left" style="position: absolute; width: 100%; height: 100%; background: linear-gradient(135deg, ${colors[3]} 0%, ${colors[4]} 100%); transform: rotateY(-90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 30px; color: white;">
                                <i class="fas fa-code"></i>
                            </div>
                            <div class="cube-face top" style="position: absolute; width: 100%; height: 100%; background: linear-gradient(135deg, ${colors[4]} 0%, ${colors[0]} 100%); transform: rotateX(90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 30px; color: white;">
                                <i class="fas fa-puzzle-piece"></i>
                            </div>
                            <div class="cube-face bottom" style="position: absolute; width: 100%; height: 100%; background: linear-gradient(135deg, ${colors[0]} 0%, ${colors[4]} 100%); transform: rotateX(-90deg) translateZ(75px); display: flex; align-items: center; justify-content: center; font-size: 30px; color: white;">
                                <i class="fas fa-cog"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="loading-icon" style="margin-bottom: 20px; font-size: 36px; color: #3b82f6; filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.5));">
                    <i class="fas fa-cube"></i>
                </div>
                
                <h4 id="loading-text" style="margin-bottom: 30px; color: #fff; font-weight: 500; font-size: 18px;">
                    Render motorunu hazırlıyor...
                </h4>
                
                <div style="position: relative; height: 8px; background: rgba(30, 58, 138, 0.3); border-radius: 4px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2) inset; transform: translateZ(10px);">
                    <div id="loading-progress" style="height: 100%; width: 0%; position: relative; border-radius: 4px; transform: translateZ(5px);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6); background-size: 200% 100%; animation: gradient-move 2s infinite linear;"></div>
                    </div>
                </div>
                
                <div id="loading-status" style="font-size: 14px; color: rgba(255,255,255,0.8); font-weight: 500; transform: translateZ(10px);">
                    0%
                </div>
                
                <style>
                    @keyframes cube-rotate {
                        0% { transform: translateZ(-75px) rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
                        100% { transform: translateZ(-75px) rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
                    }
                    
                    @keyframes gradient-move {
                        0% { background-position: 0% 0%; }
                        100% { background-position: 200% 0%; }
                    }
                    
                    @keyframes rotate3D {
                        0% { transform: translateZ(var(--z)) rotateX(var(--rx)) rotateY(var(--ry)) rotateZ(var(--rz)); }
                        100% { transform: translateZ(var(--z)) rotateX(calc(var(--rx) + 360deg)) rotateY(calc(var(--ry) + 360deg)) rotateZ(calc(var(--rz) + 360deg)); }
                    }
                    
                    .bg-tile {
                        --z: 0px;
                        --rx: 0deg;
                        --ry: 0deg;
                        --rz: 0deg;
                    }
                    
                    .cube-face {
                        border-radius: 8px;
                        box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
                        backface-visibility: visible;
                        opacity: 0.8;
                    }
                </style>
            </div>
        `;
        
        document.body.appendChild(loaderElement);
        
        // Tüm bg-tile elementlerine CSS variables ekle
        document.querySelectorAll('.bg-tile').forEach(tile => {
            const z = tile.style.transform.match(/translateZ\(([^)]+)\)/)[1];
            const rx = tile.style.transform.match(/rotateX\(([^)]+)\)/)[1];
            const ry = tile.style.transform.match(/rotateY\(([^)]+)\)/)[1];
            const rz = tile.style.transform.match(/rotateZ\(([^)]+)\)/)[1];
            
            tile.style.setProperty('--z', z);
            tile.style.setProperty('--rx', rx);
            tile.style.setProperty('--ry', ry);
            tile.style.setProperty('--rz', rz);
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
        
        // Küp animasyonunu hızlandır
        const cube = document.getElementById('cube');
        if (cube) {
            cube.style.animation = 'cube-rotate 5s infinite linear';
        }
        
        // Bekle ve animasyonu gizle
        setTimeout(() => {
            loaderElement.style.opacity = '0';
            setTimeout(() => {
                if (loaderElement && loaderElement.parentNode) {
                    loaderElement.parentNode.removeChild(loaderElement);
                    loaderElement = null;
                    cubeFaces = null;
                }
            }, 500);
        }, 1000);
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
        
        if (!loadingText || !loadingIcon || !loadingProgress || !loadingStatus) return;
        
        // İlerleme çubuğunu güncelle
        loadingProgress.style.width = `${progress}%`;
        loadingStatus.textContent = `${progress}%`;
        
        // Küp yüzlerini güncelle
        const stageIndex = Math.min(Math.floor(progress / 10), loadingStages.length - 1);
        const icon = loadingStages[stageIndex].icon;
        
        if (cubeFaces) {
            const faceList = [cubeFaces.front, cubeFaces.back, cubeFaces.right, cubeFaces.left, cubeFaces.top, cubeFaces.bottom];
            
            // İkon değişikliklerini küpe yansıt
            if (progress % 10 === 0 && progress > 0) {
                const activeFace = faceList[stageIndex % faceList.length];
                activeFace.innerHTML = `<i class="fas ${icon}"></i>`;
            }
        }
        
        if (isCompleted) {
            // Tamamlandı durumu
            loadingText.textContent = "Studio Editor Hazır!";
            loadingIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            loadingIcon.style.color = '#10b981';
            loadingIcon.style.filter = 'drop-shadow(0 0 10px rgba(16, 185, 129, 0.5))';
            return;
        }
        
        // Aşamaları göster
        loadingText.textContent = loadingStages[stageIndex].text;
        loadingIcon.innerHTML = `<i class="fas ${icon}"></i>`;
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
            // İlerleme hızını hesapla
            let increment = 0.5;
            
            // Aşamalara göre hız değişimi
            if (currentProgress < 30) {
                increment = 0.7 + (Math.random() * 0.3); // Başta biraz hızlı
            } else if (currentProgress < 60) {
                increment = 0.4 + (Math.random() * 0.3); // Normal hız
            } else if (currentProgress < 80) {
                increment = 0.2 + (Math.random() * 0.2); // Yavaşlama
            } else {
                increment = 0.1 + (Math.random() * 0.1); // Son aşamada çok yavaş
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