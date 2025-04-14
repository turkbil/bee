/**
 * Studio Basic Blocks
 * Temel blokları yöneten modül
 */
const StudioBasicBlocks = (function() {
    /**
     * Temel bileşenleri kaydet
     * @param {Object} editor GrapesJS editor örneği
     */
    function registerBasicComponents(editor) {
        // Başlık bileşeni
        editor.DomComponents.addType('heading', {
            isComponent: el => el.tagName === 'H1' || el.tagName === 'H2' || 
                              el.tagName === 'H3' || el.tagName === 'H4' || 
                              el.tagName === 'H5' || el.tagName === 'H6',
            model: {
                defaults: {
                    tagName: 'h2',
                    traits: [
                        {
                            type: 'select',
                            name: 'tagName',
                            label: 'Seviye',
                            options: [
                                { value: 'h1', name: 'Başlık 1' },
                                { value: 'h2', name: 'Başlık 2' },
                                { value: 'h3', name: 'Başlık 3' },
                                { value: 'h4', name: 'Başlık 4' },
                                { value: 'h5', name: 'Başlık 5' },
                                { value: 'h6', name: 'Başlık 6' }
                            ]
                        },
                        {
                            type: 'select',
                            name: 'text-align',
                            label: 'Hizalama',
                            options: [
                                { value: 'left', name: 'Sola' },
                                { value: 'center', name: 'Ortaya' },
                                { value: 'right', name: 'Sağa' }
                            ]
                        },
                        {
                            type: 'color',
                            name: 'color',
                            label: 'Renk'
                        }
                    ]
                }
            }
        });
        
        // Paragraf bileşeni
        editor.DomComponents.addType('paragraph', {
            isComponent: el => el.tagName === 'P',
            model: {
                defaults: {
                    tagName: 'p',
                    traits: [
                        {
                            type: 'select',
                            name: 'text-align',
                            label: 'Hizalama',
                            options: [
                                { value: 'left', name: 'Sola' },
                                { value: 'center', name: 'Ortaya' },
                                { value: 'right', name: 'Sağa' },
                                { value: 'justify', name: 'İki Yana' }
                            ]
                        },
                        {
                            type: 'color',
                            name: 'color',
                            label: 'Renk'
                        }
                    ]
                }
            }
        });
        
        // Buton bileşeni
        editor.DomComponents.addType('button', {
            isComponent: el => el.tagName === 'BUTTON' || 
                              (el.tagName === 'A' && el.classList.contains('btn')),
            model: {
                defaults: {
                    tagName: 'button',
                    attributes: { class: 'btn btn-primary' },
                    traits: [
                        {
                            type: 'select',
                            name: 'type',
                            label: 'Tip',
                            options: [
                                { value: 'button', name: 'Button' },
                                { value: 'submit', name: 'Submit' },
                                { value: 'reset', name: 'Reset' }
                            ]
                        },
                        {
                            type: 'select',
                            name: 'btn-style',
                            label: 'Stil',
                            options: [
                                { value: 'btn-primary', name: 'Primary' },
                                { value: 'btn-secondary', name: 'Secondary' },
                                { value: 'btn-success', name: 'Success' },
                                { value: 'btn-danger', name: 'Danger' },
                                { value: 'btn-warning', name: 'Warning' },
                                { value: 'btn-info', name: 'Info' },
                                { value: 'btn-light', name: 'Light' },
                                { value: 'btn-dark', name: 'Dark' },
                                { value: 'btn-link', name: 'Link' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'btn-size',
                            label: 'Boyut',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'btn-lg', name: 'Büyük' },
                                { value: 'btn-sm', name: 'Küçük' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'checkbox',
                            name: 'btn-block',
                            label: 'Genişlik %100',
                            changeProp: true
                        }
                    ],
                    'btn-style': 'btn-primary',
                    'btn-size': '',
                    'btn-block': false,
                }
            },
            view: {
                events: {
                    change: 'updateButton'
                },
                updateButton() {
                    const model = this.model;
                    const btnStyle = model.get('btn-style');
                    const btnSize = model.get('btn-size');
                    const btnBlock = model.get('btn-block');
                    
                    // Mevcut sınıflar
                    const classes = model.getClasses();
                    
                    // btn- ile başlayan sınıfları kaldır
                    const filteredClasses = classes.filter(cls => 
                        !cls.startsWith('btn-') || cls === 'btn-block'
                    );
                    
                    // Yeni sınıfları ekle
                    filteredClasses.push(btnStyle);
                    if (btnSize) filteredClasses.push(btnSize);
                    if (btnBlock) filteredClasses.push('d-block w-100');
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                }
            }
        });
        
        // Link bileşeni
        editor.DomComponents.addType('link', {
            isComponent: el => el.tagName === 'A',
            model: {
                defaults: {
                    tagName: 'a',
                    attributes: { href: '#' },
                    traits: [
                        {
                            name: 'href',
                            label: 'URL'
                        },
                        {
                            type: 'select',
                            name: 'target',
                            label: 'Hedef',
                            options: [
                                { value: '_self', name: 'Aynı Pencere' },
                                { value: '_blank', name: 'Yeni Pencere' },
                                { value: '_parent', name: 'Üst Pencere' },
                                { value: '_top', name: 'Tam Pencere' }
                            ]
                        },
                        {
                            type: 'text',
                            name: 'title',
                            label: 'Başlık'
                        }
                    ]
                }
            }
        });
        
        // Liste bileşeni
        editor.DomComponents.addType('list', {
            isComponent: el => el.tagName === 'UL' || el.tagName === 'OL',
            model: {
                defaults: {
                    tagName: 'ul',
                    traits: [
                        {
                            type: 'select',
                            name: 'tagName',
                            label: 'Tip',
                            options: [
                                { value: 'ul', name: 'Sırasız Liste' },
                                { value: 'ol', name: 'Sıralı Liste' }
                            ]
                        }
                    ]
                }
            }
        });
        
        console.log('Temel bileşenler kaydedildi');
    }
    
    /**
     * Temel blokları kaydet
     * @param {Object} editor GrapesJS editor örneği
     */
    function registerBasicBlocks(editor) {
        // Önce bileşenleri kaydet
        registerBasicComponents(editor);
        
        // Blok yöneticisini al
        const blockManager = editor.BlockManager;
        
        // Başlık bloğu
        blockManager.add('heading', {
            label: 'Başlık',
            category: 'temel',
            attributes: { class: 'fa fa-heading' },
            content: {
                type: 'heading',
                content: 'Başlık',
                attributes: { class: 'my-3' }
            }
        });
        
        // Paragraf bloğu
        blockManager.add('paragraph', {
            label: 'Paragraf',
            category: 'temel',
            attributes: { class: 'fa fa-paragraph' },
            content: {
                type: 'paragraph',
                content: 'Bu bir paragraf metnidir. Çift tıklayarak düzenleyebilirsiniz.',
                attributes: { class: 'my-3' }
            }
        });
        
        // Buton bloğu
        blockManager.add('button', {
            label: 'Buton',
            category: 'temel',
            attributes: { class: 'fa fa-square' },
            content: {
                type: 'button',
                content: 'Buton',
                attributes: { class: 'btn btn-primary' }
            }
        });
        
        // Link bloğu
        blockManager.add('link', {
            label: 'Link',
            category: 'temel',
            attributes: { class: 'fa fa-link' },
            content: {
                type: 'link',
                content: 'Link',
                attributes: { class: 'text-decoration-none', href: '#' }
            }
        });
        
        // Liste bloğu
        blockManager.add('list', {
            label: 'Liste',
            category: 'temel',
            attributes: { class: 'fa fa-list' },
            content: {
                type: 'list',
                content: '<li>Liste Öğesi 1</li><li>Liste Öğesi 2</li><li>Liste Öğesi 3</li>',
                attributes: { class: 'my-3' }
            }
        });
        
        // Yatay çizgi bloğu
        blockManager.add('divider', {
            label: 'Ayraç',
            category: 'temel',
            attributes: { class: 'fa fa-minus' },
            content: '<hr class="my-4">',
        });
        
        // Metin bloğu (içerik blokları)
        blockManager.add('text-block', {
            label: 'Metin Bloğu',
            category: 'temel',
            attributes: { class: 'fa fa-font' },
            content: `
                <div class="text-block my-4">
                    <h3>Metin Bloğu Başlığı</h3>
                    <p class="lead">Bu bir öne çıkan metindir.</p>
                    <p>Bu içerik bloğunu düzenleyebilir ve genişletebilirsiniz. İçerik bloğu, başlık ve birden fazla paragraf içerebilir.</p>
                </div>
            `,
        });
        
        console.log('Temel bloklar kaydedildi');
    }
    
    /**
     * Temel bloklar için varsayılan ayarları al
     * @returns {Object} Varsayılan ayarlar
     */
    function getBasicBlockDefaults() {
        return {
            heading: {
                tagName: 'h2',
                className: 'my-3',
                content: 'Başlık'
            },
            paragraph: {
                className: 'my-3',
                content: 'Bu bir paragraf metnidir. Çift tıklayarak düzenleyebilirsiniz.'
            },
            button: {
                className: 'btn btn-primary',
                content: 'Buton'
            },
            link: {
                className: 'text-decoration-none',
                href: '#',
                content: 'Link'
            },
            list: {
                className: 'my-3',
                content: '<li>Liste Öğesi 1</li><li>Liste Öğesi 2</li><li>Liste Öğesi 3</li>'
            }
        };
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        registerBasicComponents: registerBasicComponents,
        registerBasicBlocks: registerBasicBlocks,
        getBasicBlockDefaults: getBasicBlockDefaults
    };
})();

// Global olarak kullanılabilir yap
window.StudioBasicBlocks = StudioBasicBlocks;