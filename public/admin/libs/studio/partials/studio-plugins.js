/**
 * Studio Editor - Eklentiler Modülü
 * GrapesJS eklentilerini yükler ve yapılandırır
 */
window.StudioPlugins = (function() {
    /**
     * Desteklenen eklentiler
     */
    const supportedPlugins = {
        'blocks-basic': {
            // Temel bloklar eklentisi
            init: function(editor) {
                editor.BlockManager.add('custom-heading', {
                    category: 'Temel',
                    label: 'Başlık',
                    content: '<h3>Yeni başlık</h3>',
                    attributes: { class: 'fa fa-heading' }
                });
                
                editor.BlockManager.add('custom-paragraph', {
                    category: 'Temel',
                    label: 'Paragraf',
                    content: '<p>Metin içeriği buraya gelecek. Bu metin örnek bir içeriktir.</p>',
                    attributes: { class: 'fa fa-paragraph' }
                });
            }
        },
        'preset-webpage': {
            // Web sayfası ön ayarları
            init: function(editor) {
                // Sayfaya dışarıdan HTML içeri aktarma butonunu ekle
                editor.Panels.addButton('options', {
                    id: 'import-html',
                    className: 'fa fa-upload',
                    command: 'gjs-open-import-webpage',
                    attributes: {
                        title: 'HTML İçeri Aktar',
                        'data-tooltip-pos': 'bottom'
                    }
                });
                
                // Sayfayı dışa aktarma butonunu ekle
                editor.Panels.addButton('options', {
                    id: 'export-html',
                    className: 'fa fa-download',
                    command: 'export-template',
                    attributes: {
                        title: 'HTML Dışa Aktar',
                        'data-tooltip-pos': 'bottom'
                    }
                });
            }
        },
        'style-bg': {
            // Arkaplan stili eklentisi
            init: function(editor) {
                // Arkaplan değiştirme butonu ekle
                editor.Panels.addButton('options', {
                    id: 'change-bg',
                    className: 'fa fa-image',
                    command: 'open-bg-settings',
                    attributes: {
                        title: 'Arkaplan Değiştir',
                        'data-tooltip-pos': 'bottom'
                    }
                });
            }
        },
        'plugin-forms': {
            // Form eklentisi
            init: function(editor) {
                // Formlar sekmesini ekle
                editor.BlockManager.add('form-simple', {
                    category: 'Formlar',
                    label: 'Basit Form',
                    content: `
                        <form class="form">
                            <div class="mb-3">
                                <label for="example-input" class="form-label">Etiket</label>
                                <input id="example-input" type="text" class="form-control" placeholder="Placeholder">
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Gönder</button>
                            </div>
                        </form>
                    `,
                    attributes: { class: 'fa fa-wpforms' }
                });
            }
        },
        'custom-code': {
            // Özel kod eklentisi
            init: function(editor) {
                // Özel kod butonu ekle
                editor.Panels.addButton('options', {
                    id: 'open-code',
                    className: 'fa fa-code',
                    command: 'open-code',
                    attributes: {
                        title: 'Özel Kod Ekle',
                        'data-tooltip-pos': 'bottom'
                    }
                });
            }
        },
        'touch': {
            // Dokunmatik eklentisi
            init: function(editor) {
                // Dokunmatik etkileşim için özel yapılandırma gerekmez
            }
        },
        'components-countdown': {
            // Sayaç bileşeni
            init: function(editor) {
                editor.BlockManager.add('countdown-block', {
                    category: 'Bileşenler',
                    label: 'Geri Sayım',
                    attributes: { class: 'fa fa-clock' },
                    content: `
                        <div class="countdown-timer" data-date="2025/12/31">
                            <div class="countdown-item">
                                <span class="days">00</span>
                                <p class="countdown-label">Gün</p>
                            </div>
                            <div class="countdown-item">
                                <span class="hours">00</span>
                                <p class="countdown-label">Saat</p>
                            </div>
                            <div class="countdown-item">
                                <span class="minutes">00</span>
                                <p class="countdown-label">Dakika</p>
                            </div>
                            <div class="countdown-item">
                                <span class="seconds">00</span>
                                <p class="countdown-label">Saniye</p>
                            </div>
                        </div>
                    `
                });
            }
        },
        'tabs': {
            // Sekmeler bileşeni
            init: function(editor) {
                editor.BlockManager.add('tabs-block', {
                    category: 'Bileşenler',
                    label: 'Sekmeler',
                    attributes: { class: 'fa fa-folder' },
                    content: `
                        <div class="tabs-container">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">Sekme 1</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Sekme 2</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active p-3" id="home" role="tabpanel">
                                    <p>Sekme 1 içeriği buraya gelecek.</p>
                                </div>
                                <div class="tab-pane fade p-3" id="profile" role="tabpanel">
                                    <p>Sekme 2 içeriği buraya gelecek.</p>
                                </div>
                            </div>
                        </div>
                    `
                });
            }
        },
        'typed': {
            // Yazım efekti bileşeni
            init: function(editor) {
                editor.BlockManager.add('typed-block', {
                    category: 'Bileşenler',
                    label: 'Yazım Efekti',
                    attributes: { class: 'fa fa-i-cursor' },
                    content: `
                        <div class="typed-container">
                            <span class="typed-text">Yazım efekti: </span>
                            <span class="typed-element" data-strings="['Metin 1', 'Metin 2', 'Metin 3']"></span>
                        </div>
                    `
                });
            }
        }
    };
    
    /**
     * Eklentileri yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadPlugins(editor) {
        // Desteklenen tüm eklentileri başlat
        Object.keys(supportedPlugins).forEach(pluginName => {
            try {
                const plugin = supportedPlugins[pluginName];
                if (plugin && typeof plugin.init === 'function') {
                    plugin.init(editor);
                    console.log(`Eklenti başlatıldı: ${pluginName}`);
                }
            } catch (error) {
                console.error(`Eklenti başlatılırken hata oluştu: ${pluginName}`, error);
            }
        });
        
        // Plugin-loader varsa çalıştır
        if (typeof window.loadGrapesJSPlugins === 'function') {
            window.loadGrapesJSPlugins(editor);
        }
    }
    
    /**
     * Özel bileşenleri kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerCustomComponents(editor) {
        // Sayaç bileşeni
        editor.DomComponents.addType('countdown', {
            model: {
                defaults: {
                    script: function() {
                        const countdownTarget = this.getAttribute('data-date') || '2025/12/31';
                        const countdownElement = this;
                        
                        function updateCountdown() {
                            const now = new Date().getTime();
                            const target = new Date(countdownTarget).getTime();
                            const difference = target - now;
                            
                            if (difference <= 0) {
                                clearInterval(interval);
                                countdownElement.innerHTML = '<div class="countdown-finished">Süre Doldu!</div>';
                                return;
                            }
                            
                            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((difference % (1000 * 60)) / 1000);
                            
                            const daysElement = countdownElement.querySelector('.days');
                            const hoursElement = countdownElement.querySelector('.hours');
                            const minutesElement = countdownElement.querySelector('.minutes');
                            const secondsElement = countdownElement.querySelector('.seconds');
                            
                            if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
                            if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
                            if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
                            if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
                        }
                        
                        updateCountdown();
                        const interval = setInterval(updateCountdown, 1000);
                    },
                    attributes: {
                        class: 'countdown-timer',
                        'data-date': '2025/12/31'
                    },
                    traits: [
                        {
                            type: 'date',
                            name: 'data-date',
                            label: 'Hedef Tarih'
                        }
                    ]
                }
            }
        });
        
        // Yazım efekti bileşeni
        editor.DomComponents.addType('typed', {
            model: {
                defaults: {
                    script: function() {
                        const element = this.querySelector('.typed-element');
                        const stringsAttr = element.getAttribute('data-strings');
                        let strings = ['Metin 1', 'Metin 2', 'Metin 3'];
                        
                        try {
                            strings = JSON.parse(stringsAttr);
                        } catch (e) {
                            console.error('Typed strings parse error', e);
                        }
                        
                        let currentTextIndex = 0;
                        let currentCharIndex = 0;
                        let isDeleting = false;
                        let typingSpeed = 100;
                        
                        function type() {
                            const currentText = strings[currentTextIndex];
                            
                            if (isDeleting) {
                                element.textContent = currentText.substring(0, currentCharIndex - 1);
                                currentCharIndex--;
                                typingSpeed = 50;
                            } else {
                                element.textContent = currentText.substring(0, currentCharIndex + 1);
                                currentCharIndex++;
                                typingSpeed = 150;
                            }
                            
                            if (!isDeleting && currentCharIndex === currentText.length) {
                                isDeleting = true;
                                typingSpeed = 1000; // Pause at end
                            } else if (isDeleting && currentCharIndex === 0) {
                                isDeleting = false;
                                currentTextIndex = (currentTextIndex + 1) % strings.length;
                                typingSpeed = 500; // Pause before starting new word
                            }
                            
                            setTimeout(type, typingSpeed);
                        }
                        
                        setTimeout(type, 1000);
                    },
                    traits: [
                        {
                            type: 'text',
                            name: 'data-strings',
                            label: 'Metin Dizisi'
                        }
                    ]
                }
            }
        });
    }
    
    /**
     * Özel komutları ekle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function addCustomCommands(editor) {
        // HTML & CSS temizle komutu
        editor.Commands.add('clean-html', {
            run: function(editor) {
                const html = editor.getHtml();
                const css = editor.getCss();
                
                // Burada HTML ve CSS temizleme mantığı
                const cleanHtml = html.replace(/\s+/g, ' ')
                                      .replace(/>\s+</g, '><')
                                      .trim();
                                      
                const cleanCss = css.replace(/\s+/g, ' ')
                                    .replace(/{\s+/g, '{')
                                    .replace(/}\s+/g, '}')
                                    .replace(/:\s+/g, ':')
                                    .replace(/;\s+/g, ';')
                                    .trim();
                
                editor.setComponents(cleanHtml);
                editor.setStyle(cleanCss);
                
                StudioUtils.showNotification(
                    "Başarılı", 
                    "HTML ve CSS temizlendi.", 
                    "success"
                );
            }
        });
        
        // Arka plan resmi komutu
        editor.Commands.add('open-bg-settings', {
            run: function(editor) {
                const selectedComponent = editor.getSelected();
                if (!selectedComponent) {
                    StudioUtils.showNotification(
                        "Uyarı", 
                        "Lütfen önce bir bileşen seçin.", 
                        "warning"
                    );
                    return;
                }
                
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'bg-settings-modal';
                modal.setAttribute('tabindex', '-1');
                modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Arkaplan Ayarları</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Arkaplan Resmi URL</label>
                                <input type="text" class="form-control" id="bg-image-url" placeholder="https://...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Arkaplan Rengi</label>
                                <input type="color" class="form-control form-control-color" id="bg-color" value="#ffffff">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="apply-bg-settings">Uygula</button>
                        </div>
                    </div>
                </div>
                `;
                
                document.body.appendChild(modal);
                
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                    
                    document.getElementById('apply-bg-settings').addEventListener('click', function() {
                        const bgUrl = document.getElementById('bg-image-url').value;
                        const bgColor = document.getElementById('bg-color').value;
                        
                        const styles = {};
                        
                        if (bgUrl) {
                            styles['background-image'] = `url('${bgUrl}')`;
                            styles['background-position'] = 'center center';
                            styles['background-size'] = 'cover';
                        }
                        
                        if (bgColor) {
                            styles['background-color'] = bgColor;
                        }
                        
                        selectedComponent.addStyle(styles);
                        modalInstance.hide();
                        
                        StudioUtils.showNotification(
                            "Başarılı", 
                            "Arkaplan ayarları uygulandı.", 
                            "success"
                        );
                    });
                    
                    modal.addEventListener('hidden.bs.modal', function() {
                        modal.remove();
                    });
                } else {
                    // Fallback basit modal gösterimi
                    modal.style.display = 'block';
                    // diğer işlemler...
                }
            }
        });
    }
    
    return {
        loadPlugins: loadPlugins,
        registerCustomComponents: registerCustomComponents,
        addCustomCommands: addCustomCommands
    };
})();