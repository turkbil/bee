/**
 * Studio Editor - Basic Blocks
 * Temel blokları tanımlar ve kaydeder
 */
const StudioBasicBlocks = (function() {
    /**
     * Temel blokları kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBasicBlocks(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return;
        }
        
        console.log('Temel bloklar kaydediliyor...');
        
        // Başlık
        editor.BlockManager.add('heading', {
            label: 'Başlık',
            category: 'temel',
            attributes: { class: 'fa fa-heading' },
            content: {
                type: 'heading',
                content: 'Başlık',
                attributes: { class: 'my-3' }
            }
        });

        // Paragraf
        editor.BlockManager.add('paragraph', {
            label: 'Paragraf',
            category: 'temel',
            attributes: { class: 'fa fa-paragraph' },
            content: {
                type: 'paragraph',
                content: 'Bu bir paragraf metnidir. Çift tıklayarak düzenleyebilirsiniz.',
                attributes: { class: 'my-3' }
            }
        });

        // Buton
        editor.BlockManager.add('button', {
            label: 'Buton',
            category: 'temel',
            attributes: { class: 'fa fa-square' },
            content: {
                type: 'button',
                content: 'Buton',
                attributes: { class: 'btn btn-primary' }
            }
        });

        // Link
        editor.BlockManager.add('link', {
            label: 'Link',
            category: 'temel',
            attributes: { class: 'fa fa-link' },
            content: {
                type: 'link',
                content: 'Link',
                attributes: { class: 'text-decoration-none', href: '#' }
            }
        });

        // Liste
        editor.BlockManager.add('list', {
            label: 'Liste',
            category: 'temel',
            attributes: { class: 'fa fa-list' },
            content: {
                type: 'list',
                content: '<li>Liste Öğesi 1</li><li>Liste Öğesi 2</li><li>Liste Öğesi 3</li>',
                attributes: { class: 'my-3' }
            }
        });
        
        // Ayrıcı
        editor.BlockManager.add('divider', {
            label: 'Ayrıcı',
            category: 'temel',
            attributes: { class: 'fa fa-minus' },
            content: '<hr class="my-4">'
        });
        
        console.log('Temel bloklar kaydedildi');
    }
    
    /**
     * Temel bileşenleri kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBasicComponents(editor) {
        // Başlık bileşeni
        editor.DomComponents.addType('heading', {
            isComponent: el => el.tagName === 'H1' || el.tagName === 'H2' || el.tagName === 'H3',
            model: {
                defaults: {
                    tagName: 'h2',
                    attributes: { class: 'my-3' },
                    traits: [
                        {
                            type: 'select',
                            name: 'tagName',
                            label: 'Boyut',
                            options: [
                                { value: 'h1', name: 'Büyük (H1)' },
                                { value: 'h2', name: 'Orta (H2)' },
                                { value: 'h3', name: 'Küçük (H3)' },
                                { value: 'h4', name: 'Çok Küçük (H4)' },
                                { value: 'h5', name: 'Mini (H5)' },
                                { value: 'h6', name: 'Extra Mini (H6)' }
                            ]
                        },
                        {
                            type: 'select',
                            name: 'class',
                            label: 'Stil',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'text-primary', name: 'Birincil Renk' },
                                { value: 'text-secondary', name: 'İkincil Renk' },
                                { value: 'text-success', name: 'Başarılı' },
                                { value: 'text-danger', name: 'Tehlike' },
                                { value: 'text-warning', name: 'Uyarı' },
                                { value: 'text-info', name: 'Bilgi' },
                                { value: 'text-center', name: 'Ortalanmış' },
                                { value: 'text-end', name: 'Sağa Yaslı' }
                            ]
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
                    attributes: { class: 'my-3' },
                    traits: [
                        {
                            type: 'select',
                            name: 'class',
                            label: 'Stil',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'lead', name: 'Büyük Yazı' },
                                { value: 'text-muted', name: 'Soluk' },
                                { value: 'text-primary', name: 'Birincil Renk' },
                                { value: 'text-secondary', name: 'İkincil Renk' },
                                { value: 'text-center', name: 'Ortalanmış' },
                                { value: 'text-end', name: 'Sağa Yaslı' }
                            ]
                        }
                    ]
                }
            }
        });
        
        // Buton bileşeni
        editor.DomComponents.addType('button', {
            isComponent: el => el.tagName === 'BUTTON',
            model: {
                defaults: {
                    tagName: 'button',
                    attributes: { class: 'btn btn-primary' },
                    traits: [
                        {
                            type: 'select',
                            name: 'class',
                            label: 'Stil',
                            options: [
                                { value: 'btn btn-primary', name: 'Birincil' },
                                { value: 'btn btn-secondary', name: 'İkincil' },
                                { value: 'btn btn-success', name: 'Başarılı' },
                                { value: 'btn btn-danger', name: 'Tehlike' },
                                { value: 'btn btn-warning', name: 'Uyarı' },
                                { value: 'btn btn-info', name: 'Bilgi' },
                                { value: 'btn btn-light', name: 'Açık' },
                                { value: 'btn btn-dark', name: 'Koyu' },
                                { value: 'btn btn-link', name: 'Link' },
                                { value: 'btn btn-outline-primary', name: 'Birincil (Kenarlıklı)' },
                                { value: 'btn btn-outline-secondary', name: 'İkincil (Kenarlıklı)' }
                            ]
                        },
                        {
                            type: 'select',
                            name: 'size',
                            label: 'Boyut',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'btn-sm', name: 'Küçük' },
                                { value: 'btn-lg', name: 'Büyük' }
                            ]
                        }
                    ]
                }
            }
        });
        
        console.log('Temel bileşenler kaydedildi');
    }
    
    /**
     * Temel bloklar için varsayılan ayarları döndürür
     * @returns {Object} - Varsayılan ayarlar
     */
    function getBasicBlockDefaults() {
        return {
            heading: {
                tagName: 'h2',
                attributes: { class: 'my-3' },
                content: 'Başlık'
            },
            paragraph: {
                tagName: 'p',
                attributes: { class: 'my-3' },
                content: 'Bu bir paragraf metnidir. Çift tıklayarak düzenleyebilirsiniz.'
            },
            button: {
                tagName: 'button',
                attributes: { class: 'btn btn-primary' },
                content: 'Buton'
            },
            link: {
                tagName: 'a',
                attributes: { class: 'text-decoration-none', href: '#' },
                content: 'Link'
            },
            list: {
                tagName: 'ul',
                attributes: { class: 'my-3' },
                content: '<li>Liste Öğesi 1</li><li>Liste Öğesi 2</li><li>Liste Öğesi 3</li>'
            },
            divider: {
                tagName: 'hr',
                attributes: { class: 'my-4' }
            }
        };
    }
    
    // Dışa aktarılan API
    return {
        registerBasicBlocks,
        registerBasicComponents,
        getBasicBlockDefaults
    };
})();

// Global olarak kullanılabilir yap
window.StudioBasicBlocks = StudioBasicBlocks;