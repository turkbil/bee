/**
 * Studio Editor - Bloklar Modülü
 * Editor için özel blok tanımlamaları
 */
window.StudioBlocks = (function() {
    /**
     * Temel blok tanımlarını ekler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBlocks(editor) {
        // Blok kategorileri tanımla
        editor.BlockManager.getCategories().reset();
        editor.BlockManager.getCategories().add([
            { id: "Temel", label: "Temel Bileşenler" },
            { id: "Bileşenler", label: "Bootstrap Bileşenleri" },
            { id: "Düzen", label: "Düzen Bileşenleri" },
            { id: "Medya", label: "Medya Bileşenleri" }
        ]);

        // 1 Sütunlu Bölüm
        editor.BlockManager.add("section-1col", {
            label: "1 Sütun",
            category: "Düzen",
            attributes: { class: "fa fa-columns" },
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
        editor.BlockManager.add("section-2col", {
            label: "2 Sütun",
            category: "Düzen",
            attributes: { class: "fa fa-columns" },
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
        editor.BlockManager.add("section-3col", {
            label: "3 Sütun",
            category: "Düzen",
            attributes: { class: "fa fa-columns" },
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

        // Header Blok
        editor.BlockManager.add("header", {
            label: "Header",
            category: "Temel",
            attributes: { class: "fa fa-header" },
            content: `<header class="bg-primary text-white py-4">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1>Şirket İsmi</h1>
                        </div>
                        <div class="col-md-6 text-end">
                            <nav>
                                <a href="#" class="btn btn-outline-light me-2">Ana Sayfa</a>
                                <a href="#" class="btn btn-outline-light me-2">Hakkımızda</a>
                                <a href="#" class="btn btn-outline-light">İletişim</a>
                            </nav>
                        </div>
                    </div>
                </div>
            </header>`
        });

        // Footer Blok
        editor.BlockManager.add("footer", {
            label: "Footer",
            category: "Temel",
            attributes: { class: "fa fa-window-minimize" },
            content: `<footer class="bg-dark text-white py-4 mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Hakkımızda</h4>
                            <p>Şirketimiz hakkında kısa bir açıklama.</p>
                        </div>
                        <div class="col-md-4">
                            <h4>Hızlı Linkler</h4>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-white">Ana Sayfa</a></li>
                                <li><a href="#" class="text-white">Hizmetler</a></li>
                                <li><a href="#" class="text-white">İletişim</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h4>İletişim</h4>
                            <address>
                                <p><i class="fas fa-map-marker-alt"></i> Adres, Şehir, Ülke</p>
                                <p><i class="fas fa-phone"></i> +90 123 456 7890</p>
                                <p><i class="fas fa-envelope"></i> info@ornek.com</p>
                            </address>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col text-center">
                            <p class="mb-0">&copy; 2025 Tüm Hakları Saklıdır.</p>
                        </div>
                    </div>
                </div>
            </footer>`
        });

        // Text Blok
        editor.BlockManager.add("text", {
            label: "Metin",
            category: "Temel",
            attributes: { class: "fa fa-text-width" },
            content: {
                type: "text",
                content: "<p>Çift tıklayarak bu metni düzenleyebilirsiniz.</p>",
                style: { padding: "10px" }
            }
        });

        // Link Blok
        editor.BlockManager.add("link", {
            label: "Link",
            category: "Temel",
            attributes: { class: "fa fa-link" },
            content: {
                type: "link",
                content: "Link Metni",
                style: { color: "#007bff" }
            }
        });

        // Image Blok
        editor.BlockManager.add("image", {
            label: "Görsel",
            category: "Medya",
            attributes: { class: "fa fa-image" },
            content: {
                type: "image",
                style: { padding: "10px" },
                attributes: { alt: "Görsel açıklaması" }
            }
        });

        // Button Blok
        editor.BlockManager.add("button", {
            label: "Buton",
            category: "Temel",
            attributes: { class: "fa fa-square" },
            content: '<a class="btn btn-primary" href="#">Butona Tıkla</a>'
        });

        // Card Blok
        editor.BlockManager.add("card", {
            label: "Kart",
            category: "Bileşenler",
            attributes: { class: "fa fa-credit-card" },
            content: `<div class="card" style="width: 18rem;">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Kart Başlığı</h5>
                    <p class="card-text">Kartın içeriği buraya gelecek. Kısa bir açıklama yazabilirsiniz.</p>
                    <a href="#" class="btn btn-primary">Detay</a>
                </div>
            </div>`
        });

        // Jumbotron Blok
        editor.BlockManager.add("jumbotron", {
            label: "Jumbotron",
            category: "Bileşenler",
            attributes: { class: "fa fa-newspaper-o" },
            content: `<div class="bg-light p-5 rounded-3 mb-4">
                <div class="container py-5">
                    <h1 class="display-5 fw-bold">Hoş Geldiniz!</h1>
                    <p class="col-md-8 fs-4">Sitenizin ana mesajı veya sloganı buraya yazılabilir. Bu alan dikkat çekmek için kullanılır.</p>
                    <a class="btn btn-primary btn-lg" href="#" role="button">Daha Fazla</a>
                </div>
            </div>`
        });

        // Navbar Blok
        editor.BlockManager.add("navbar", {
            label: "Navbar",
            category: "Bileşenler",
            attributes: { class: "fa fa-bars" },
            content: `<nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a class="navbar-brand" href="#">Şirket İsmi</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="#">Ana Sayfa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Hakkımızda</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Hizmetler</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">İletişim</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>`
        });

        // HTML blok
        editor.BlockManager.add('html', {
            label: 'HTML Kodu',
            category: 'Temel',
            attributes: { class: 'fa fa-code' },
            content: {
                type: 'text',
                content: '<div>HTML kodunuzu buraya yazın</div>',
                style: { padding: '10px' }
            }
        });
        
        // Video blok
        editor.BlockManager.add('video', {
            label: 'Video',
            category: 'Medya',
            attributes: { class: 'fa fa-film' },
            content: {
                type: 'video',
                src: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                style: { width: '100%', height: '300px' }
            }
        });
        
        // İletişim Formu
        editor.BlockManager.add('contact-form', {
            label: 'İletişim Formu',
            category: 'Bileşenler',
            attributes: { class: 'fa fa-envelope' },
            content: `<form class="container py-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Adınız</label>
                    <input type="text" class="form-control" id="name" placeholder="Adınız Soyadınız">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta Adresiniz</label>
                    <input type="email" class="form-control" id="email" placeholder="ornek@mail.com">
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Mesajınız</label>
                    <textarea class="form-control" id="message" rows="4" placeholder="Mesajınızı buraya yazınız..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gönder</button>
            </form>`
        });
    }
    
    return {
        registerBlocks: registerBlocks
    };
})();