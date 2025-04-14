/**
 * Studio Editor - Media Blocks
 * Medya bloklarını tanımlar ve kaydeder
 */
const StudioMediaBlocks = (function() {
    /**
     * Medya bloklarını kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerMediaBlocks(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return;
        }
        
        console.log('Medya blokları kaydediliyor...');
        
        // Görsel
        editor.BlockManager.add('image', {
            label: 'Görsel',
            category: 'medya',
            attributes: { class: 'fa fa-image' },
            content: {
                type: 'image',
                attributes: { class: 'img-fluid', src: 'https://via.placeholder.com/350x150', alt: 'Görsel' }
            }
        });

        // Video
        editor.BlockManager.add('video', {
            label: 'Video',
            category: 'medya',
            attributes: { class: 'fa fa-film' },
            content: {
                type: 'video',
                attributes: { class: 'embed-responsive embed-responsive-16by9' },
                content: '<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0" allowfullscreen></iframe>'
            }
        });

        // Harita
        editor.BlockManager.add('map', {
            label: 'Harita',
            category: 'medya',
            attributes: { class: 'fa fa-map' },
            content: {
                type: 'map',
                attributes: { class: 'embed-responsive embed-responsive-16by9' },
                content: '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12084.595137908992!2d28.977877!3d41.037128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7650656bd63%3A0x8ca058b28c20b6c3!2zVGFrc2ltIE1leWRhbsSxLCBHw7xtw7zFn3N1eXUsIDM0NDM1IEJleW_En2x1L8Swc3RhbmJ1bA!5e0!3m2!1str!2str!4v1617968345678!5m2!1str!2str" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'
            }
        });
        
        // Görsel Galerisi
        editor.BlockManager.add('image-gallery', {
            label: 'Görsel Galerisi',
            category: 'medya',
            attributes: { class: 'fa fa-images' },
            content: `
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <img src="https://via.placeholder.com/350x150" class="img-fluid img-thumbnail" alt="Görsel 1">
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="https://via.placeholder.com/350x150" class="img-fluid img-thumbnail" alt="Görsel 2">
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="https://via.placeholder.com/350x150" class="img-fluid img-thumbnail" alt="Görsel 3">
                    </div>
                </div>
            `
        });
        
        // İkon
        editor.BlockManager.add('icon', {
            label: 'İkon',
            category: 'medya',
            attributes: { class: 'fa fa-icons' },
            content: '<i class="fa fa-star fa-3x"></i>'
        });
        
        console.log('Medya blokları kaydedildi');
    }
    
    /**
     * Medya bileşenlerini kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerMediaComponents(editor) {
        // Görsel bileşeni
        editor.DomComponents.addType('image', {
            isComponent: el => el.tagName === 'IMG',
            model: {
                defaults: {
                    tagName: 'img',
                    attributes: { class: 'img-fluid', src: 'https://via.placeholder.com/350x150', alt: 'Görsel' },
                    traits: [
                        {
                            name: 'src',
                            label: 'Kaynak URL'
                        },
                        {
                            name: 'alt',
                            label: 'Alternatif Metin'
                        },
                        {
                            type: 'select',
                            name: 'class',
                            label: 'Stil',
                            options: [
                                { value: 'img-fluid', name: 'Normal' },
                                { value: 'img-fluid rounded', name: 'Yuvarlak Köşeli' },
                                { value: 'img-fluid img-thumbnail', name: 'Çerçeveli' },
                                { value: 'img-fluid rounded-circle', name: 'Daire' }
                            ]
                        }
                    ]
                }
            }
        });
        
        // Video bileşeni
        editor.DomComponents.addType('video', {
            isComponent: el => el.tagName === 'IFRAME',
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
                            label: 'Kaynak URL'
                        },
                        {
                            type: 'checkbox',
                            name: 'allowfullscreen',
                            label: 'Tam Ekran İzin'
                        }
                    ]
                }
            }
        });
        
        // İkon bileşeni
        editor.DomComponents.addType('icon', {
            isComponent: el => el.tagName === 'I' && (el.className.includes('fa-') || el.className.includes('fas ')),
            model: {
                defaults: {
                    tagName: 'i',
                    attributes: { class: 'fa fa-star' },
                    traits: [
                        {
                            type: 'select',
                            name: 'icon',
                            label: 'İkon Tipi',
                            options: [
                                { value: 'fa-star', name: 'Yıldız' },
                                { value: 'fa-heart', name: 'Kalp' },
                                { value: 'fa-check', name: 'Onay' },
                                { value: 'fa-times', name: 'Çarpı' },
                                { value: 'fa-user', name: 'Kullanıcı' },
                                { value: 'fa-home', name: 'Ev' },
                                { value: 'fa-envelope', name: 'Zarf' },
                                { value: 'fa-phone', name: 'Telefon' },
                                { value: 'fa-search', name: 'Arama' }
                            ]
                        },
                        {
                            type: 'select',
                            name: 'size',
                            label: 'Boyut',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'fa-lg', name: 'Büyük' },
                                { value: 'fa-2x', name: '2x' },
                                { value: 'fa-3x', name: '3x' },
                                { value: 'fa-4x', name: '4x' },
                                { value: 'fa-5x', name: '5x' }
                            ]
                        },
                        {
                            type: 'select',
                            name: 'color',
                            label: 'Renk',
                            options: [
                                { value: '', name: 'Varsayılan' },
                                { value: 'text-primary', name: 'Birincil' },
                                { value: 'text-secondary', name: 'İkincil' },
                                { value: 'text-success', name: 'Başarılı' },
                                { value: 'text-danger', name: 'Tehlike' },
                                { value: 'text-warning', name: 'Uyarı' },
                                { value: 'text-info', name: 'Bilgi' }
                            ]
                        }
                    ]
                }
            }
        });
        
        console.log('Medya bileşenleri kaydedildi');
    }
    
    /**
     * Medya blokları için varsayılan ayarları döndürür
     * @returns {Object} - Varsayılan ayarlar
     */
    function getMediaBlockDefaults() {
        return {
            image: {
                tagName: 'img',
                attributes: { class: 'img-fluid', src: 'https://via.placeholder.com/350x150', alt: 'Görsel' }
            },
            video: {
                tagName: 'iframe',
                attributes: { 
                    class: 'embed-responsive-item', 
                    src: 'https://www.youtube.com/embed/zpOULjyy-n8?rel=0',
                    allowfullscreen: true,
                    width: '100%',
                    height: '315'
                }
            },
            map: {
                tagName: 'iframe',
                attributes: { 
                    src: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12084.595137908992!2d28.977877!3d41.037128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7650656bd63%3A0x8ca058b28c20b6c3!2zVGFrc2ltIE1leWRhbsSxLCBHw7xtw7zFn3N1eXUsIDM0NDM1IEJleW_En2x1L8Swc3RhbmJ1bA!5e0!3m2!1str!2str!4v1617968345678!5m2!1str!2str',
                    width: '100%',
                    height: '300',
                    style: 'border:0;',
                    allowfullscreen: true,
                    loading: 'lazy'
                }
            },
            icon: {
                tagName: 'i',
                attributes: { class: 'fa fa-star fa-3x' }
            }
        };
    }
    
    // Dışa aktarılan API
    return {
        registerMediaBlocks,
        registerMediaComponents,
        getMediaBlockDefaults
    };
})();

// Global olarak kullanılabilir yap
window.StudioMediaBlocks = StudioMediaBlocks;