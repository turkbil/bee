/**
 * Studio Media Blocks
 * Medya bloklarını yöneten modül
 */
const StudioMediaBlocks = (function() {
    /**
     * Medya bileşenlerini kaydet
     * @param {Object} editor GrapesJS editor örneği
     */
    function registerMediaComponents(editor) {
        // Görsel bileşeni
        editor.DomComponents.addType('media-image', {
            isComponent: el => el.tagName === 'IMG',
            model: {
                defaults: {
                    tagName: 'img',
                    attributes: { 
                        class: 'img-fluid', 
                        src: '/storage/lipsum/a1.jpg', 
                        alt: 'Görsel açıklaması'
                    },
                    traits: [
                        {
                            name: 'src',
                            label: 'Kaynak'
                        },
                        {
                            name: 'alt',
                            label: 'Alternatif Metin'
                        },
                        {
                            type: 'select',
                            name: 'img-style',
                            label: 'Stil',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'rounded', name: 'Yuvarlatılmış' },
                                { value: 'rounded-circle', name: 'Daire' },
                                { value: 'img-thumbnail', name: 'Thumbnail' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'img-align',
                            label: 'Hizalama',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'float-start', name: 'Sola' },
                                { value: 'float-end', name: 'Sağa' },
                                { value: 'mx-auto d-block', name: 'Orta' }
                            ],
                            changeProp: true
                        }
                    ],
                    'img-style': '',
                    'img-align': ''
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:img-style change:img-align', this.updateImage);
                },
                updateImage() {
                    const model = this.model;
                    const imgStyle = model.get('img-style');
                    const imgAlign = model.get('img-align');
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Stil ve hizalama sınıflarını kaldır
                    const filteredClasses = classes.filter(cls => 
                        cls !== 'rounded' && 
                        cls !== 'rounded-circle' && 
                        cls !== 'img-thumbnail' &&
                        cls !== 'float-start' &&
                        cls !== 'float-end' &&
                        cls !== 'mx-auto' &&
                        cls !== 'd-block'
                    );
                    
                    // Temel sınıf
                    filteredClasses.push('img-fluid');
                    
                    // Stil sınıfı
                    if (imgStyle) {
                        filteredClasses.push(imgStyle);
                    }
                    
                    // Hizalama sınıfları
                    if (imgAlign) {
                        imgAlign.split(' ').forEach(cls => {
                            filteredClasses.push(cls);
                        });
                    }
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                }
            }
        });
        
        // Video bileşeni
        editor.DomComponents.addType('media-video', {
            isComponent: el => el.tagName === 'VIDEO' || (el.tagName === 'IFRAME' && el.src && (el.src.includes('youtube') || el.src.includes('vimeo'))),
            model: {
                defaults: {
                    tagName: 'iframe',
                    attributes: { 
                        class: 'embed-responsive-item',
                        src: 'https://www.youtube.com/embed/zpOULjyy-n8?rel=0',
                        allowfullscreen: true
                    },
                    traits: [
                        {
                            name: 'src',
                            label: 'Kaynak'
                        },
                        {
                            type: 'checkbox',
                            name: 'allowfullscreen',
                            label: 'Tam Ekran'
                        },
                        {
                            type: 'select',
                            name: 'video-ratio',
                            label: 'En-Boy Oranı',
                            options: [
                                { value: 'ratio-16x9', name: '16:9' },
                                { value: 'ratio-4x3', name: '4:3' },
                                { value: 'ratio-1x1', name: '1:1' },
                                { value: 'ratio-21x9', name: '21:9' }
                            ],
                            changeProp: true
                        }
                    ],
                    'video-ratio': 'ratio-16x9'
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:video-ratio', this.updateVideo);
                },
                updateVideo() {
                    const model = this.model;
                    const videoRatio = model.get('video-ratio');
                    
                    // İframe'i responsive div içine al
                    if (model.parent() && !model.parent().getAttributes().class?.includes('ratio')) {
                        const iframeAttrs = model.getAttributes();
                        const wrapper = editor.DomComponents.addComponent({
                            tagName: 'div',
                            attributes: { 
                                class: `ratio ${videoRatio}`
                            },
                            components: [{
                                tagName: 'iframe',
                                attributes: iframeAttrs
                            }]
                        });
                        
                        // Eski iframe'i wrapper ile değiştir
                        model.replaceWith(wrapper);
                    } else if (model.parent() && model.parent().getAttributes().class?.includes('ratio')) {
                        // Mevcut wrapper'ın sınıfını güncelle
                        const parent = model.parent();
                        const parentClasses = parent.getClasses().filter(cls => !cls.startsWith('ratio-'));
                        parentClasses.push('ratio');
                        parentClasses.push(videoRatio);
                        parent.setClass(parentClasses);
                    }
                }
            }
        });
        
        // Harita bileşeni
        editor.DomComponents.addType('media-map', {
            isComponent: el => el.tagName === 'IFRAME' && el.src && el.src.includes('maps.google'),
            model: {
                defaults: {
                    tagName: 'iframe',
                    attributes: { 
                        class: 'border-0 w-100',
                        src: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12084.595137908992!2d28.977877!3d41.037128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7650656bd63%3A0x8ca058b28c20b6c3!2zVGFrc2ltIE1leWRhbsSxLCBHw7xtw7zFn3N1eXUsIDM0NDM1IEJleW_En2x1L8Swc3RhbmJ1bA!5e0!3m2!1str!2str!4v1617968345678!5m2!1str!2str',
                        width: '100%',
                        height: '300',
                        allowfullscreen: '',
                        loading: 'lazy'
                    },
                    traits: [
                        {
                            name: 'src',
                            label: 'Harita URL'
                        },
                        {
                            name: 'height',
                            label: 'Yükseklik'
                        },
                        {
                            type: 'select',
                            name: 'map-border',
                            label: 'Kenarlık',
                            options: [
                                { value: 'border-0', name: 'Yok' },
                                { value: 'border', name: 'Normal' },
                                { value: 'border border-primary', name: 'Primary' },
                                { value: 'border border-secondary', name: 'Secondary' },
                                { value: 'border border-success', name: 'Success' },
                                { value: 'border border-danger', name: 'Danger' },
                                { value: 'border border-warning', name: 'Warning' },
                                { value: 'border border-info', name: 'Info' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'map-shadow',
                            label: 'Gölge',
                            options: [
                                { value: '', name: 'Yok' },
                                { value: 'shadow-sm', name: 'Küçük' },
                                { value: 'shadow', name: 'Normal' },
                                { value: 'shadow-lg', name: 'Büyük' }
                            ],
                            changeProp: true
                        }
                    ],
                    'map-border': 'border-0',
                    'map-shadow': ''
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:map-border change:map-shadow', this.updateMap);
                },
                updateMap() {
                    const model = this.model;
                    const mapBorder = model.get('map-border');
                    const mapShadow = model.get('map-shadow');
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Border ve shadow sınıflarını kaldır
                    const filteredClasses = classes.filter(cls => 
                        !cls.startsWith('border') && !cls.startsWith('shadow')
                    );
                    
                    // Temel sınıf
                    filteredClasses.push('w-100');
                    
                    // Kenarlık sınıfı
                    if (mapBorder) {
                        mapBorder.split(' ').forEach(cls => {
                            filteredClasses.push(cls);
                        });
                    }
                    
                    // Gölge sınıfı
                    if (mapShadow) {
                        filteredClasses.push(mapShadow);
                    }
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                }
            }
        });
        
        // Carousel bileşeni
        editor.DomComponents.addType('media-carousel', {
            isComponent: el => el.classList && el.classList.contains('carousel'),
            model: {
                defaults: {
                    tagName: 'div',
                    attributes: { 
                        class: 'carousel slide', 
                        'data-bs-ride': 'carousel'
                    },
                    traits: [
                        {
                            type: 'checkbox',
                            name: 'indicators',
                            label: 'Göstergeler',
                            changeProp: true
                        },
                        {
                            type: 'checkbox',
                            name: 'controls',
                            label: 'Kontroller',
                            changeProp: true
                        },
                        {
                            type: 'checkbox',
                            name: 'crossfade',
                            label: 'Crossfade Efekti',
                            changeProp: true
                        },
                        {
                            type: 'number',
                            name: 'slide-count',
                            label: 'Slayt Sayısı',
                            min: 1,
                            max: 10,
                            changeProp: true
                        }
                    ],
                    indicators: true,
                    controls: true,
                    crossfade: false,
                    'slide-count': 3
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:indicators change:controls change:crossfade change:slide-count', this.updateCarousel);
                },
                updateCarousel() {
                    const model = this.model;
                    const indicators = model.get('indicators');
                    const controls = model.get('controls');
                    const crossfade = model.get('crossfade');
                    const slideCount = model.get('slide-count') || 3;
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Crossfade sınıfını kaldır/ekle
                    const filteredClasses = crossfade ? 
                        [...classes.filter(cls => cls !== 'slide'), 'carousel-fade'] : 
                        [...classes.filter(cls => cls !== 'carousel-fade'), 'slide'];
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                    
                    // Benzersiz ID oluştur
                    const carouselId = 'carousel-' + Math.floor(Math.random() * 1000);
                    model.addAttributes({ 'id': carouselId });
                    
                    // İçeriği oluştur
                    let content = '';
                    
                    // Göstergeler
                    if (indicators) {
                        content += `<div class="carousel-indicators">`;
                        for (let i = 0; i < slideCount; i++) {
                            content += `
                                <button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${i}" 
                                    ${i === 0 ? 'class="active" aria-current="true"' : ''} 
                                    aria-label="Slide ${i+1}">
                                </button>
                            `;
                        }
                        content += `</div>`;
                    }
                    
                    // Slaytlar
                    content += `<div class="carousel-inner">`;
                    for (let i = 0; i < slideCount; i++) {
                        content += `
                        <div class="carousel-item ${i === 0 ? 'active' : ''}">
                            <img src="/storage/lipsum/a${(i % 9) + 1}.jpg" class="d-block w-100" alt="Slide ${i+1}">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Slayt ${i+1} Başlığı</h5>
                                <p>Slayt açıklaması buraya gelecek.</p>
                            </div>
                        </div>
                    `;
                    }
                    content += `</div>`;
                    
                    // Kontroller
                    if (controls) {
                        content += `
                            <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Önceki</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Sonraki</span>
                            </button>
                        `;
                    }
                    
                    // İçeriği güncelle
                    model.components(content);
                }
            }
        });
        
        // İkon bileşeni
        editor.DomComponents.addType('media-icon', {
            isComponent: el => el.tagName === 'I' && (el.classList.contains('fa') || el.classList.contains('fas') || el.classList.contains('far') || el.classList.contains('fab')),
            model: {
                defaults: {
                    tagName: 'i',
                    attributes: { class: 'fas fa-star' },
                    traits: [
                        {
                            type: 'select',
                            name: 'icon-prefix',
                            label: 'İkon Tipi',
                            options: [
                                { value: 'fas', name: 'Solid' },
                                { value: 'far', name: 'Regular' },
                                { value: 'fab', name: 'Brand' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'text',
                            name: 'icon-name',
                            label: 'İkon Adı',
                            placeholder: 'star, home, user...',
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'icon-size',
                            label: 'Boyut',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'fa-xs', name: 'Extra Small' },
                                { value: 'fa-sm', name: 'Small' },
                                { value: 'fa-lg', name: 'Large' },
                                { value: 'fa-2x', name: '2x' },
                                { value: 'fa-3x', name: '3x' },
                                { value: 'fa-5x', name: '5x' },
                                { value: 'fa-7x', name: '7x' },
                                { value: 'fa-10x', name: '10x' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'color',
                            name: 'icon-color',
                            label: 'Renk',
                            changeProp: true
                        }
                    ],
                    'icon-prefix': 'fas',
                    'icon-name': 'star',
                    'icon-size': '',
                    'icon-color': ''
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:icon-prefix change:icon-name change:icon-size change:icon-color', this.updateIcon);
                },
                updateIcon() {
                    const model = this.model;
                    const iconPrefix = model.get('icon-prefix');
                    const iconName = model.get('icon-name');
                    const iconSize = model.get('icon-size');
                    const iconColor = model.get('icon-color');
                    
                    // Mevcut sınıfları al
                    const classes = [...model.getClasses()];
                    
                    // Tüm önceki Font Awesome sınıflarını kaldır
                    const filteredClasses = classes.filter(cls => 
                        !cls.startsWith('fa-') && 
                        cls !== 'fas' && 
                        cls !== 'far' && 
                        cls !== 'fab'
                    );
                    
                    // Yeni sınıfları ekle
                    filteredClasses.push(iconPrefix);
                    filteredClasses.push(`fa-${iconName}`);
                    
                    if (iconSize) {
                        filteredClasses.push(iconSize);
                    }
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                    
                    // Renk stilini güncelle
                    if (iconColor) {
                        model.addStyle({ 'color': iconColor });
                    } else {
                        model.removeStyle('color');
                    }
                }
            }
        });
        
        console.log('Medya bileşenleri kaydedildi');
    }
    
    /**
     * Medya bloklarını kaydet
     * @param {Object} editor GrapesJS editor örneği
     */
    function registerMediaBlocks(editor) {
        // Önce bileşenleri kaydet
        registerMediaComponents(editor);
        
        // Blok yöneticisini al
        const blockManager = editor.BlockManager;
        
        // Görsel bloğu
        blockManager.add('image', {
            label: 'Görsel',
            category: 'medya',
            attributes: { class: 'fa fa-image' },
            content: {
                type: 'media-image'
            }
        });
        
        // Video bloğu
        blockManager.add('video', {
            label: 'Video',
            category: 'medya',
            attributes: { class: 'fa fa-film' },
            content: `
                <div class="ratio ratio-16x9">
                    <iframe type="media-video" class="embed-responsive-item" src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0" allowfullscreen></iframe>
                </div>
            `
        });
        
        // Harita bloğu
        blockManager.add('map', {
            label: 'Harita',
            category: 'medya',
            attributes: { class: 'fa fa-map' },
            content: {
                type: 'media-map'
            }
        });
        
        // Carousel bloğu
        blockManager.add('carousel', {
            label: 'Carousel',
            category: 'medya',
            attributes: { class: 'fa fa-images' },
            content: {
                type: 'media-carousel'
            }
        });
        
        // İkon bloğu
        blockManager.add('icon', {
            label: 'İkon',
            category: 'medya',
            attributes: { class: 'fa fa-icons' },
            content: {
                type: 'media-icon'
            }
        });
        
        // Medya kartı
        blockManager.add('media-card', {
            label: 'Medya Kartı',
            category: 'medya',
            attributes: { class: 'fa fa-id-card' },
            content: `
                <div class="card mb-3">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="https://via.placeholder.com/400x300" class="img-fluid rounded-start" alt="Görsel açıklaması">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">Medya Kartı Başlığı</h5>
                                <p class="card-text">Bu bir medya kartı örneğidir. Bu alanı kendi içeriğinizle düzenleyebilirsiniz.</p>
                                <p class="card-text"><small class="text-muted">Son güncelleme 3 dakika önce</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            `
        });
        
        // Galeri bloğu
        blockManager.add('gallery', {
            label: 'Galeri',
            category: 'medya',
            attributes: { class: 'fa fa-th' },
            content: `
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <img src="/storage/lipsum/a1.jpg" class="img-fluid rounded" alt="Görsel 1">
                    </div>
                    <div class="col-md-4">
                        <img src="/storage/lipsum/a2.jpg" class="img-fluid rounded" alt="Görsel 2">
                    </div>
                    <div class="col-md-4">
                        <img src="/storage/lipsum/a3.jpg" class="img-fluid rounded" alt="Görsel 3">
                    </div>
                    <div class="col-md-4">
                        <img src="/storage/lipsum/a4.jpg" class="img-fluid rounded" alt="Görsel 4">
                    </div>
                    <div class="col-md-4">
                        <img src="/storage/lipsum/a5.jpg" class="img-fluid rounded" alt="Görsel 5">
                    </div>
                    <div class="col-md-4">
                        <img src="/storage/lipsum/a6.jpg" class="img-fluid rounded" alt="Görsel 6">
                    </div>
                </div>
            `
        });
        
        console.log('Medya blokları kaydedildi');
    }
    
    /**
     * Medya blokları için varsayılan ayarları al
     * @returns {Object} Varsayılan ayarlar
     */
    function getMediaBlockDefaults() {
        return {
            'media-image': {
                'img-style': '',
                'img-align': ''
            },
            'media-video': {
                'video-ratio': 'ratio-16x9'
            },
            'media-map': {
                'map-border': 'border-0',
                'map-shadow': ''
            },
            'media-carousel': {
                indicators: true,
                controls: true,
                crossfade: false,
                'slide-count': 3
            },
            'media-icon': {
                'icon-prefix': 'fas',
                'icon-name': 'star',
                'icon-size': '',
                'icon-color': ''
            }
        };
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        registerMediaComponents: registerMediaComponents,
        registerMediaBlocks: registerMediaBlocks,
        getMediaBlockDefaults: getMediaBlockDefaults
    };
})();

// Global olarak kullanılabilir yap
window.StudioMediaBlocks = StudioMediaBlocks;