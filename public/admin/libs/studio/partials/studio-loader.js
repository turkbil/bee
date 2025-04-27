/**
 * Studio Editor - Yükleme Ekranı Modülü
 */

window.StudioLoader = (function() {
    // Yükleme aşamaları
    const loadingStages = [
        { text: "Bileşenler yükleniyor...", icon: "fa-puzzle-piece" },
        { text: "Modüller hazırlanıyor...", icon: "fa-cube" },
        { text: "Görsel motoru başlatılıyor...", icon: "fa-paint-brush" },
        { text: "Blok sistemi oluşturuluyor...", icon: "fa-cubes" },
        { text: "İçerik analiz ediliyor...", icon: "fa-microscope" },
        { text: "Düzenleme araçları hazırlanıyor...", icon: "fa-tools" },
        { text: "Stil şablonları yükleniyor...", icon: "fa-palette" },
        { text: "Widget sistemi entegre ediliyor...", icon: "fa-cogs" },
        { text: "Kullanıcı arayüzü optimize ediliyor...", icon: "fa-sliders-h" },
        { text: "Son hazırlıklar tamamlanıyor...", icon: "fa-check-double" }
    ];
    
    // İlerleme durumu
    let currentProgress = 0;
    let loaderElement = null;
    
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
        loaderElement.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        loaderElement.style.display = 'flex';
        loaderElement.style.alignItems = 'center';
        loaderElement.style.justifyContent = 'center';
        loaderElement.style.zIndex = '10000';
        loaderElement.style.transition = 'opacity 0.3s ease';
        
        loaderElement.innerHTML = `
            <div class="studio-loader-content" style="text-align: center; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 500px; width: 90%;">
                <div style="margin-bottom: 30px;">
                    <div style="font-size: 28px; margin-bottom: 15px; color: #206bc4;">
                        <i class="fas fa-wand-magic-sparkles"></i> Studio Editor
                    </div>
                    <div id="loader-spinner" style="margin-bottom: 25px;">
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem;"></div>
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.2s;"></div>
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.4s;"></div>
                    </div>
                </div>
                
                <div id="loading-icon" style="margin-bottom: 20px; font-size: 36px; color: #206bc4;">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                
                <h4 id="loading-text" style="margin-bottom: 30px; color: #334155; font-weight: 500;">Bileşenler yükleniyor...</h4>
                
                <div style="background-color: #f0f5fa; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 15px;">
                    <div id="loading-progress" style="height: 100%; width: 0%; background-color: #206bc4; border-radius: 6px; transition: width 0.5s ease;"></div>
                </div>
                
                <div id="loading-status" style="font-size: 13px; color: #64748b;">
                    %0
                </div>
            </div>
        `;
        
        document.body.appendChild(loaderElement);
        
        // Aşamalı ilerlemeyi başlat
        startLoading();
        
        return loaderElement;
    }
    
    /**
     * Yükleme ekranını gizle
     */
    function hide() {
        if (!loaderElement) return;
        
        // Tamamlandı göster (%100)
        updateProgress(0, true);
        
        // Bekle ve animasyonu gizle
        setTimeout(() => {
            loaderElement.style.opacity = '0';
            setTimeout(() => {
                if (loaderElement && loaderElement.parentNode) {
                    loaderElement.parentNode.removeChild(loaderElement);
                    loaderElement = null;
                }
            }, 300);
        }, 800);
    }
    
    /**
     * İlerleme çubuğunu güncelle
     * @param {number} stage - Aşama numarası
     * @param {boolean} isCompleted - Tamamlandı mı
     */
    function updateProgress(stage, isCompleted = false) {
        const loadingText = document.getElementById('loading-text');
        const loadingIcon = document.getElementById('loading-icon');
        const loadingProgress = document.getElementById('loading-progress');
        const loadingStatus = document.getElementById('loading-status');
        
        if (!loadingText || !loadingIcon || !loadingProgress || !loadingStatus) return;
        
        if (isCompleted) {
            // Tamamlandı durumu
            loadingText.textContent = "Studio Editor Hazır!";
            loadingIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            loadingProgress.style.width = "100%";
            loadingStatus.textContent = "%100";
            return;
        }
        
        // Normal ilerleme
        if (stage < loadingStages.length) {
            loadingText.textContent = loadingStages[stage].text;
            loadingIcon.innerHTML = `<i class="fas ${loadingStages[stage].icon}"></i>`;
            currentProgress = Math.round((stage + 1) * (90 / loadingStages.length)); // %90'a kadar ilerle
            loadingProgress.style.width = `${currentProgress}%`;
            loadingStatus.textContent = `%${currentProgress}`;
        }
    }
    
    /**
     * Aşamalı ilerlemeyi başlat
     */
    function startLoading() {
        let currentStage = 0;
        
        function nextStage() {
            if (currentStage >= loadingStages.length) return;
            
            updateProgress(currentStage);
            currentStage++;
            
            if (currentStage < loadingStages.length) {
                setTimeout(nextStage, 700);
            }
        }
        
        // İlk aşamayı başlat
        nextStage();
    }
    
    return {
        show: show,
        hide: hide,
        updateProgress: updateProgress
    };
})();