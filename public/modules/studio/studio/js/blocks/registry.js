/**
 * Studio Blocks Registry - Blok Sistemi
 * Tüm blokları ve kategorileri yöneten merkezi kayıt sistemi
 */
const StudioBlocks = (function() {
    // Blok kategorileri
    const categories = {
        'temel': 'Temel Bileşenler',
        'mizanpaj': 'Mizanpaj Bileşenleri',
        'medya': 'Medya Bileşenleri',
        'bootstrap': 'Bootstrap Bileşenleri',
        'widget': 'Widgetlar',
        'özel': 'Özel Bileşenler'
    };

    // Kayıtlı bloklar
    let registeredBlocks = {};

    /**
     * Blokları editöre kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBlocks(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return;
        }

        // Önce mevcut kategorileri ve blokları temizle
        resetBlocks(editor);

        // Kategorileri kaydet
        registerCategories(editor);

        // Temel blokları yükle
        registerBasicBlocks(editor);

        // Mizanpaj bloklarını yükle
        registerLayoutBlocks(editor);

        // Medya bloklarını yükle
        registerMediaBlocks(editor);

        // Bootstrap bloklarını yükle
        registerBootstrapBlocks(editor);

        // Özel bloklar ve widgetlar için kancalar
        loadCustomBlocks(editor);
        loadWidgetBlocks(editor);

        console.log('Tüm bloklar başarıyla kaydedildi');
    }

    /**
     * Blok kategorilerini kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerCategories(editor) {
        // GrapesJS kategorilerini sıfırla
        editor.BlockManager.getCategories().reset();

        // Kategorileri ekle
        Object.entries(categories).forEach(([id, label]) => {
            editor.BlockManager.getCategories().add({ id, label });
        });

        console.log('Blok kategorileri kaydedildi');
    }

    /**
     * Mevcut blokları ve kategorileri temizle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function resetBlocks(editor) {
        // Blokları sıfırla
        editor.BlockManager.getAll().reset();
        registeredBlocks = {};

        console.log('Blok kaydı temizlendi');
    }

    /**
     * Temel blokları kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBasicBlocks(editor) {
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

        registeredBlocks['temel'] = ['heading', 'paragraph', 'button', 'link', 'list'];
        console.log('Temel bloklar kaydedildi');
    }

    /**
     * Mizanpaj bloklarını kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerLayoutBlocks(editor) {
        // Konteyner
        editor.BlockManager.add('container', {
            label: 'Konteyner',
            category: 'mizanpaj',
            attributes: { class: 'fa fa-square-full' },
            content: {
                type: 'container',
                attributes: { class: 'container py-3' },
                content: '<div class="row"><div class="col-12"><p>Konteyner içeriği</p></div></div>'
            }
        });

        // 1 Sütun Satır
        editor.BlockManager.add('row-1col', {
            label: '1 Sütun',
            category: 'mizanpaj',
            attributes: { class: 'fa fa-columns' },
            content: {
                type: 'row',
                attributes: { class: 'row' },
                content: '<div class="col-12"><p>1 Sütun</p></div>'
            }
        });

        // 2 Sütun Satır
        editor.BlockManager.add('row-2col', {
            label: '2 Sütun',
            category: 'mizanpaj',
            attributes: { class: 'fa fa-columns' },
            content: {
                type: 'row',
                attributes: { class: 'row' },
                content: '<div class="col-md-6"><p>1. Sütun</p></div><div class="col-md-6"><p>2. Sütun</p></div>'
            }
        });

        // 3 Sütun Satır
        editor.BlockManager.add('row-3col', {
            label: '3 Sütun',
            category: 'mizanpaj',
            attributes: { class: 'fa fa-columns' },
            content: {
                type: 'row',
                attributes: { class: 'row' },
                content: '<div class="col-md-4"><p>1. Sütun</p></div><div class="col-md-4"><p>2. Sütun</p></div><div class="col-md-4"><p>3. Sütun</p></div>'
            }
        });

        // 4 Sütun Satır
        editor.BlockManager.add('row-4col', {
            label: '4 Sütun',
            category: 'mizanpaj',
            attributes: { class: 'fa fa-columns' },
            content: {
                type: 'row',
                attributes: { class: 'row' },
                content: '<div class="col-md-3"><p>1. Sütun</p></div><div class="col-md-3"><p>2. Sütun</p></div><div class="col-md-3"><p>3. Sütun</p></div><div class="col-md-3"><p>4. Sütun</p></div>'
            }
        });

        registeredBlocks['mizanpaj'] = ['container', 'row-1col', 'row-2col', 'row-3col', 'row-4col'];
        console.log('Mizanpaj blokları kaydedildi');
    }

    /**
     * Medya bloklarını kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerMediaBlocks(editor) {
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

        registeredBlocks['medya'] = ['image', 'video', 'map'];
        console.log('Medya blokları kaydedildi');
    }

    /**
     * Bootstrap bloklarını kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBootstrapBlocks(editor) {
        // Kart
        editor.BlockManager.add('card', {
            label: 'Kart',
            category: 'bootstrap',
            attributes: { class: 'fa fa-credit-card' },
            content: `
                <div class="card">
                    <img src="https://via.placeholder.com/800x400" class="card-img-top" alt="Kart görseli">
                    <div class="card-body">
                        <h5 class="card-title">Kart başlığı</h5>
                        <p class="card-text">Kart içeriği buraya gelecek. Kısa bir açıklama metni yazabilirsiniz.</p>
                        <a href="#" class="btn btn-primary">Detaylar</a>
                    </div>
                </div>
            `
        });

        // Jumbotron
        editor.BlockManager.add('jumbotron', {
            label: 'Jumbotron',
            category: 'bootstrap',
            attributes: { class: 'fa fa-bullhorn' },
            content: `
                <div class="p-5 mb-4 bg-light rounded-3">
                    <div class="container-fluid py-5">
                        <h1 class="display-5 fw-bold">Özel başlık</h1>
                        <p class="col-md-8 fs-4">Bu alanda daha büyük içerik veya tanıtım metni ekleyebilirsiniz.</p>
                        <button class="btn btn-primary btn-lg" type="button">Örnek buton</button>
                    </div>
                </div>
            `
        });

        // Navbar
        editor.BlockManager.add('navbar', {
            label: 'Navbar',
            category: 'bootstrap',
            attributes: { class: 'fa fa-bars' },
            content: `
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#">Navbar</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="#">Ana Sayfa</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Hakkımızda</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Hizmetler
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="#">Hizmet 1</a></li>
                                        <li><a class="dropdown-item" href="#">Hizmet 2</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#">Diğer Hizmetler</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">İletişim</a>
                                </li>
                            </ul>
                            <form class="d-flex">
                                <input class="form-control me-2" type="search" placeholder="Ara" aria-label="Search">
                                <button class="btn btn-outline-success" type="submit">Ara</button>
                            </form>
                        </div>
                    </div>
                </nav>
            `
        });

        // Form
        editor.BlockManager.add('form', {
            label: 'Form',
            category: 'bootstrap',
            attributes: { class: 'fa fa-wpforms' },
            content: `
                <form>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Email adresi</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                        <div id="emailHelp" class="form-text">Email adresinizi asla paylaşmayacağız.</div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Şifre</label>
                        <input type="password" class="form-control" id="exampleInputPassword1">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Beni hatırla</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Gönder</button>
                </form>
            `
        });

        registeredBlocks['bootstrap'] = ['card', 'jumbotron', 'navbar', 'form'];
        console.log('Bootstrap blokları kaydedildi');
    }

    /**
     * Özel blokları yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadCustomBlocks(editor) {
        // API'den özel blokları yükle
        fetch('/admin/studio/api/custom-blocks')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Özel blokları kaydet
                    registerCustomBlocks(editor, data.blocks);
                }
            })
            .catch(error => {
                console.error('Özel bloklar yüklenirken hata:', error);
            });
    }

    /**
     * Özel blokları kaydet
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Array} blocks - Özel blok listesi
     */
    function registerCustomBlocks(editor, blocks) {
        if (!Array.isArray(blocks)) {
            return;
        }

        blocks.forEach(block => {
            editor.BlockManager.add(block.id, {
                label: block.label,
                category: block.category || 'özel',
                attributes: block.attributes || { class: 'fa fa-cube' },
                content: block.content
            });
        });

        console.log(`${blocks.length} özel blok kaydedildi`);
    }

    /**
     * Widget bloklarını yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadWidgetBlocks(editor) {
        // API'den widget bloklarını yükle
        fetch('/admin/studio/api/widgets')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Widget bloklarını kaydet
                    registerWidgetBlocks(editor, data.widgets);
                }
            })
            .catch(error => {
                console.error('Widget blokları yüklenirken hata:', error);
            });
    }

    /**
     * Widget bloklarını kaydet
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Array} widgets - Widget listesi
     */
    function registerWidgetBlocks(editor, widgets) {
        if (!Array.isArray(widgets)) {
            return;
        }

        // Widgetları blok olarak kaydet
        widgets.forEach(widget => {
            editor.BlockManager.add(`widget-${widget.id}`, {
                label: widget.label,
                category: 'widget',
                attributes: {
                    class: 'fa fa-puzzle-piece',
                    'data-widget-id': widget.id
                },
                content: widget.content.html || `<div class="widget-placeholder">Widget: ${widget.label}</div>`
            });
        });

        console.log(`${widgets.length} widget blok kaydedildi`);
    }

    // Dışa aktarılan fonksiyonlar
    return {
        registerBlocks: registerBlocks,
        getCategories: function() { return {...categories}; },
        getRegisteredBlocks: function() { return {...registeredBlocks}; }
    };
})();

// Global olarak kullanılabilir yap
window.StudioBlocks = StudioBlocks;