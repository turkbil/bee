/**
 * Studio Editor - Bloklar Modülü
 * GrapesJS editörü için blok tanımlamaları
 */
window.StudioBlocks = (function() {
    /**
     * Varsayılan blok kategorilerini ekle
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupBlockCategories(editor) {
        // Kategorileri sıfırla
        editor.BlockManager.getCategories().reset();
        
        // Kategorileri ekle
        editor.BlockManager.getCategories().add(window.StudioConfig.blockCategories);
        
        console.log('Blok kategorileri eklendi.');
    }
    
    /**
     * Temel blokları ekle
     * @param {Object} editor - GrapesJS editör örneği
     */
    function registerBasicBlocks(editor) {
        // Önce varsayılan blokları temizle
        editor.BlockManager.getAll().reset();
        
        // Blok kategorilerini ayarla
        setupBlockCategories(editor);
        
        // Düzen blokları
        registerLayoutBlocks(editor);
        
        // Temel bloklar
        registerCoreBlocks(editor);
        
        // Medya blokları
        registerMediaBlocks(editor);
        
        // Bootstrap blokları
        registerBootstrapBlocks(editor);
        
        // Form blokları
        registerFormBlocks(editor);
        
        console.log('Temel bloklar kaydedildi.');
    }
    
    /**
     * Düzen blokları
     * @param {Object} editor - GrapesJS editör örneği
     */
    function registerLayoutBlocks(editor) {
        // 1 Sütunlu Bölüm
        editor.BlockManager.add('section-1col', {
            label: '1 Sütun',
            category: 'düzen',
            attributes: { class: 'fa fa-square' },
            content: `<section class="container py-5">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Başlık Buraya</h2>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>`
        });

        // 2 Sütunlu Bölüm
        editor.BlockManager.add('section-2col', {
            label: '2 Sütun',
            category: 'düzen',
            attributes: { class: 'fa fa-columns' },
            content: `<section class="container py-5">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                    <div class="col-md-6">
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>`
        });

        // 3 Sütunlu Bölüm
        editor.BlockManager.add('section-3col', {
            label: '3 Sütun',
            category: 'düzen',
            attributes: { class: 'fa fa-grip-horizontal' },
            content: `<section class="container py-5">
                <div class="row">
                    <div class="col-md-4">
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class="col-md-4">
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class="col-md-4">
                        <h3>Başlık 3</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                </div>
            </section>`
        });
        
        // Hero Bölüm
        editor.BlockManager.add('hero-section', {
            label: 'Hero Bölüm',
            category: 'düzen',
            attributes: { class: 'fa fa-star' },
            content: `<section class="py-5 text-center container">
                <div class="row py-lg-5">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <h1 class="fw-bold">Ana Başlık</h1>
                        <p class="lead text-muted">Buraya kısa bir açıklama yazabilirsiniz. Misyon, vizyon veya hizmetleriniz hakkında kısa bir tanıtım metni ekleyebilirsiniz.</p>
                        <div class="mt-4">
                            <a href="#" class="btn btn-primary my-2 me-2">Daha Fazla</a>
                            <a href="#" class="btn btn-secondary my-2">İletişim</a>
                        </div>
                    </div>
                </div>
            </section>`
        });
    }
    
    /**
     * Temel bloklar
     * @param {Object} editor - GrapesJS editör örneği
     */
    function registerCoreBlocks(editor) {
        // Başlık
        editor.BlockManager.add('header-block', {
            label: 'Başlık',
            category: 'temel',
            attributes: { class: 'fa fa-heading' },
            content: {
                type: 'text',
                tagName: 'h2',
                content: 'Başlık Buraya',
                style: { padding: '10px' }
            }
        });
        
        // Paragraf
        editor.BlockManager.add('paragraph', {
            label: 'Paragraf',
            category: 'temel',
            attributes: { class: 'fa fa-paragraph' },
            content: {
                type: 'text',
                tagName: 'p',
                content: 'Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                style: { padding: '10px' }
            }
        });
        
        // Düğme
        editor.BlockManager.add('button', {
            label: 'Düğme',
            category: 'temel',
            attributes: { class: 'fa fa-square' },
            content: {
                type: 'link',
                tagName: 'a',
                content: 'Tıkla',
                classes: ['btn', 'btn-primary'],
                attributes: { href: '#' },
                style: { margin: '10px 0' }
            }
        });
        
        // Link
        editor.BlockManager.add('link', {
            label: 'Link',
            category: 'temel',
            attributes: { class: 'fa fa-link' },
            content: {
                type: 'link',
                content: 'Link Metni',
                attributes: { href: '#' }
            }
        });
        
        // Divider
        editor.BlockManager.add('divider', {
            label: 'Ayırıcı',
            category: 'temel',
            attributes: { class: 'fa fa-minus' },
            content: {
                type: 'divider',
                tagName: 'hr',
                style: { margin: '15px 0' }
            }
        });
    }
    
    /**
     * Medya blokları
     * @param {Object} editor - GrapesJS editör örneği
     */
    function registerMediaBlocks(editor) {
        // Resim
        editor.BlockManager.add('image', {
            label: 'Resim',
            category: 'medya',
            attributes: { class: 'fa fa-image' },
            content: {
                type: 'image',
                style: { padding: '10px', 'max-width': '100%' },
                classes: ['img-fluid'],
                attributes: { src: 'https://via.placeholder.com/800x400', alt: 'Görsel açıklaması' }
            }
        });
        
        // Video
        editor.BlockManager.add('video', {
            label: 'Video',
            category: 'medya',
            attributes: { class: 'fa fa-video' },
            content: `<div class="ratio ratio-16x9 my-3">
                <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0" 
                    title="Video başlığı" allowfullscreen></iframe>
            </div>`
        });
        
        // Resim + Metin
        editor.BlockManager.add('image-text', {
            label: 'Resim & Metin',
            category: 'medya',
            attributes: { class: 'fa fa-newspaper' },
            content: `<div class="row my-4 align-items-center">
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/600x400" class="img-fluid rounded" alt="Görsel açıklaması">
                </div>
                <div class="col-md-6">
                    <h3>Başlık Buraya</h3>
                    <p>Buraya açıklama metni gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    <a href="#" class="btn btn-primary">Detaylar</a>
                </div>
            </div>`
        });
        
        // Galeri
        editor.BlockManager.add('gallery', {
            label: 'Galeri',
            category: 'medya',
            attributes: { class: 'fa fa-images' },
            content: `<div class="row g-3 my-4">
                <div class="col-md-4">
                    <img src="https://via.placeholder.com/400x300" class="img-fluid rounded" alt="Galeri görsel 1">
                </div>
                <div class="col-md-4">
                    <img src="https://via.placeholder.com/400x300" class="img-fluid rounded" alt="Galeri görsel 2">
                </div>
                <div class="col-md-4">
                    <img src="https://via.placeholder.com/400x300" class="img-fluid rounded" alt="Galeri görsel 3">
                </div>
            </div>`
        });
        
        // Harita
        editor.BlockManager.add('map', {
            label: 'Harita',
            category: 'medya',
            attributes: { class: 'fa fa-map-marker-alt' },
            content: `<div class="ratio ratio-16x9 my-3">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d96412.44555098224!2d28.872096978699353!3d41.00546451320416!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab9bde0c66ac9%3A0x89f4d62a08bf2249!2zS8O2cHLDvCwgVW52YW4sIDM0NDI1IEJlxZ9pa3RhxZ8vxLBzdGFuYnVs!5e0!3m2!1str!2str!4v1649950310126!5m2!1str!2str" 
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>`
        });
        
        // Ses (Audio)
        editor.BlockManager.add('audio', {
            label: 'Ses',
            category: 'medya',
            attributes: { class: 'fa fa-music' },
            content: `<div class="my-3">
                <audio controls class="w-100">
                    <source src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" type="audio/mpeg">
                    Tarayıcınız audio elementini desteklemiyor.
                </audio>
            </div>`
        });
    }
    
    /**
     * Bootstrap bileşenleri
     * @param {Object} editor - GrapesJS editör örneği
     */
    function registerBootstrapBlocks(editor) {
        // Navbar
        editor.BlockManager.add('navbar', {
            label: 'Navbar',
            category: 'bootstrap',
            attributes: { class: 'fa fa-bars' },
            content: `<nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Logo</a>
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
            </nav>`
        });
        
        // Kart
        editor.BlockManager.add('card', {
            label: 'Kart',
            category: 'bootstrap',
            attributes: { class: 'fa fa-credit-card' },
            content: `<div class="card">
                <img src="https://via.placeholder.com/800x400" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Kart başlığı</h5>
                    <p class="card-text">Kart içeriği buraya gelecek. Kısa bir açıklama metni yazabilirsiniz.</p>
                    <a href="#" class="btn btn-primary">Detaylar</a>
                </div>
            </div>`
        });
        
        // Jumbotron
        editor.BlockManager.add('jumbotron', {
            label: 'Jumbotron',
            category: 'bootstrap',
            attributes: { class: 'fa fa-bullhorn' },
            content: `<div class="p-5 mb-4 bg-light rounded-3">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold">Özel başlık</h1>
                    <p class="col-md-8 fs-4">Bu alanda daha büyük içerik veya tanıtım metni ekleyebilirsiniz. Büyük yazı tipi ve geniş boşluklar kullanarak daha çekici bir görünüm sağlar.</p>
                    <button class="btn btn-primary btn-lg" type="button">Örnek buton</button>
                </div>
            </div>`
        });
        
        // Carousel
        editor.BlockManager.add('carousel', {
            label: 'Carousel',
            category: 'bootstrap',
            attributes: { class: 'fa fa-image' },
            content: `<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://via.placeholder.com/800x400/3498db/ffffff" class="d-block w-100" alt="Slide 1">
                    </div>
                    <div class="carousel-item">
                        <img src="https://via.placeholder.com/800x400/e74c3c/ffffff" class="d-block w-100" alt="Slide 2">
                    </div>
                    <div class="carousel-item">
                        <img src="https://via.placeholder.com/800x400/2ecc71/ffffff" class="d-block w-100" alt="Slide 3">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>`
        });
        
        // Uyarı
        editor.BlockManager.add('alert', {
            label: 'Uyarı',
            category: 'bootstrap',
            attributes: { class: 'fa fa-exclamation-triangle' },
            content: `<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Dikkat!</strong> Önemli bir bildirim için bu uyarı bloğunu kullanabilirsiniz.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`
        });
        
        // Kartlı Grup
        editor.BlockManager.add('card-group', {
            label: 'Kart Grubu',
            category: 'bootstrap',
            attributes: { class: 'fa fa-table-cells' },
            content: `<div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200/3498db/ffffff" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Kart 1</h5>
                            <p class="card-text">Kısa açıklama metni.</p>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="btn btn-primary btn-sm">Detaylar</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200/e74c3c/ffffff" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Kart 2</h5>
                            <p class="card-text">Kısa açıklama metni.</p>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="btn btn-primary btn-sm">Detaylar</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200/2ecc71/ffffff" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Kart 3</h5>
                            <p class="card-text">Kısa açıklama metni.</p>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="btn btn-primary btn-sm">Detaylar</a>
                        </div>
                    </div>
                </div>
            </div>`
        });
    }
    
    /**
     * Form blokları
     * @param {Object} editor - GrapesJS editör örneği
     */
    function registerFormBlocks(editor) {
        // İletişim Formu
        editor.BlockManager.add('contact-form', {
            label: 'İletişim Formu',
            category: 'formlar',
            attributes: { class: 'fa fa-envelope' },
            content: `<form class="my-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Adınız Soyadınız</label>
                    <input type="text" class="form-control" id="name" placeholder="Adınız Soyadınız">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email adresiniz</label>
                    <input type="email" class="form-control" id="email" placeholder="ornek@domain.com">
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Mesajınız</label>
                    <textarea class="form-control" id="message" rows="5"></textarea>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary" type="submit">Gönder</button>
                </div>
            </form>`
        });
        
        // Input Grubu
        editor.BlockManager.add('form-input', {
            label: 'Form Input',
            category: 'formlar',
            attributes: { class: 'fa fa-i-cursor' },
            content: `<div class="mb-3">
                <label for="exampleInput" class="form-label">Input Etiketi</label>
                <input type="text" class="form-control" id="exampleInput" placeholder="Placeholder metni">
                <div class="form-text">Yardımcı açıklama metni.</div>
            </div>`
        });
        
        // Textarea
        editor.BlockManager.add('form-textarea', {
            label: 'Form Textarea',
            category: 'formlar',
            attributes: { class: 'fa fa-align-left' },
            content: `<div class="mb-3">
                <label for="exampleTextarea" class="form-label">Textarea Etiketi</label>
                <textarea class="form-control" id="exampleTextarea" rows="3"></textarea>
            </div>`
        });
        
        // Select
        editor.BlockManager.add('form-select', {
            label: 'Form Select',
            category: 'formlar',
            attributes: { class: 'fa fa-caret-square-down' },
            content: `<div class="mb-3">
                <label for="exampleSelect" class="form-label">Select Etiketi</label>
                <select class="form-select" id="exampleSelect">
                    <option selected>Seçenek seçin</option>
                    <option value="1">Seçenek 1</option>
                    <option value="2">Seçenek 2</option>
                    <option value="3">Seçenek 3</option>
                </select>
            </div>`
        });
        
        // Checkbox
        editor.BlockManager.add('form-checkbox', {
            label: 'Form Checkbox',
            category: 'formlar',
            attributes: { class: 'fa fa-check-square' },
            content: `<div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck">
                <label class="form-check-label" for="exampleCheck">Onaylıyorum</label>
            </div>`
        });
        
        // Radio Button
        editor.BlockManager.add('form-radio', {
            label: 'Form Radio',
            category: 'formlar',
            attributes: { class: 'fa fa-dot-circle' },
            content: `<div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                    <label class="form-check-label" for="flexRadioDefault1">
                        Seçenek 1
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
                    <label class="form-check-label" for="flexRadioDefault2">
                        Seçenek 2
                    </label>
                </div>
            </div>`
        });
    }
    
    /**
     * Widget blokları ekle
     * @param {Object} editor - GrapesJS editör örneği
     * @param {Array} widgets - Widget dizisi
     */
    function registerWidgetBlocks(editor, widgets = []) {
        if (!widgets || !widgets.length) {
            console.log('Widget bloğu eklemek için widget verileri bulunamadı.');
            return;
        }
        
        widgets.forEach(widget => {
            editor.BlockManager.add(`widget-${widget.id}`, {
                label: widget.name,
                category: widget.category || 'widget',
                attributes: { class: 'fa fa-puzzle-piece' },
                content: {
                    type: 'widget',
                    widget_id: widget.id,
                    content: widget.content_html || `<div class="widget-placeholder">Widget: ${widget.name}</div>`,
                    style: widget.content_css || '',
                    script: widget.content_js || '',
                }
            });
        });
        
        console.log(`${widgets.length} widget bloğu eklendi.`);
    }
    
    return {
        setupBlockCategories: setupBlockCategories,
        registerBasicBlocks: registerBasicBlocks,
        registerWidgetBlocks: registerWidgetBlocks
    };
})();