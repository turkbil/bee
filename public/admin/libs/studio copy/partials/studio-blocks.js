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
        // Reset mevcut kategoriler ve bloklar
        editor.BlockManager.getAll().reset();
        
        // Blok kategorileri tanımla - sadece bir kez
        editor.BlockManager.getCategories().reset();
        editor.BlockManager.getCategories().add([
            { id: "düzen", label: "Düzen Bileşenleri" },
            { id: "temel", label: "Temel Bileşenler" },
            { id: "medya", label: "Medya Bileşenleri" },
            { id: "bootstrap", label: "Bootstrap Bileşenleri" }
        ]);

        // 1 Sütunlu Bölüm
        editor.BlockManager.add("section-1col", {
            label: "1 Sütun",
            category: "düzen",
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
            category: "düzen",
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
            category: "düzen",
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

        // Header
        editor.BlockManager.add("header", {
            label: "Header",
            category: "temel",
            attributes: { class: "fa fa-heading" },
            content: `<header class="py-3 mb-4 border-bottom">
                <div class="container d-flex flex-wrap justify-content-center">
                    <a href="/" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
                        <span class="fs-4">Şirket Adı</span>
                    </a>
                    <ul class="nav">
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2 active">Ana Sayfa</a></li>
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Hakkımızda</a></li>
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Hizmetler</a></li>
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2">İletişim</a></li>
                    </ul>
                </div>
            </header>`
        });

        // Footer
        editor.BlockManager.add("footer", {
            label: "Footer",
            category: "temel",
            attributes: { class: "fa fa-window-minimize" },
            content: `<footer class="py-3 my-4">
                <div class="container">
                    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Ana Sayfa</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Hakkımızda</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Hizmetler</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">SSS</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">İletişim</a></li>
                    </ul>
                    <p class="text-center text-muted">© 2025 Şirket, Inc</p>
                </div>
            </footer>`
        });

        // Text
        editor.BlockManager.add("text", {
            label: "Metin",
            category: "temel",
            attributes: { class: "fa fa-font" },
            content: `<div class="my-3">
                <h3>Başlık</h3>
                <p>Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit velit id diam ultrices, at facilisis dui tincidunt.</p>
            </div>`
        });

        // Link
        editor.BlockManager.add("link", {
            label: "Link",
            category: "temel",
            attributes: { class: "fa fa-link" },
            content: `<a href="#" class="btn btn-link">Daha Fazla</a>`
        });

        // Button
        editor.BlockManager.add("button", {
            label: "Buton",
            category: "temel",
            attributes: { class: "fa fa-square" },
            content: `<button class="btn btn-primary">Tıkla</button>`
        });

        // HTML
        editor.BlockManager.add("html", {
            label: "HTML Kodu",
            category: "temel",
            attributes: { class: "fa fa-code" },
            content: `<div data-gjs-type="custom-code">
                <!-- Buraya özel HTML kodları yazabilirsiniz -->
                <div class="custom-html">Özel HTML İçeriği</div>
            </div>`
        });

        // Image
        editor.BlockManager.add("image", {
            label: "Görsel",
            category: "medya",
            attributes: { class: "fa fa-image" },
            content: `<img src="https://via.placeholder.com/800x400" class="img-fluid rounded" alt="Görsel açıklaması">`
        });

        // Video
        editor.BlockManager.add("video", {
            label: "Video",
            category: "medya",
            attributes: { class: "fa fa-film" },
            content: `<div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0" title="Video başlığı" allowfullscreen></iframe>
            </div>`
        });

        // Card
        editor.BlockManager.add("card", {
            label: "Kart",
            category: "bootstrap",
            attributes: { class: "fa fa-credit-card" },
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
        editor.BlockManager.add("jumbotron", {
            label: "Jumbotron",
            category: "bootstrap",
            attributes: { class: "fa fa-bullhorn" },
            content: `<div class="p-5 mb-4 bg-light rounded-3">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold">Özel başlık</h1>
                    <p class="col-md-8 fs-4">Bu alanda daha büyük içerik veya tanıtım metni ekleyebilirsiniz. Büyük yazı tipi ve geniş boşluklar kullanarak daha çekici bir görünüm sağlar.</p>
                    <button class="btn btn-primary btn-lg" type="button">Örnek buton</button>
                </div>
            </div>`
        });

        // Navbar
        editor.BlockManager.add("navbar", {
            label: "Navbar",
            category: "bootstrap",
            attributes: { class: "fa fa-bars" },
            content: `<nav class="navbar navbar-expand-lg navbar-light bg-light">
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
            </nav>`
        });

        // Contact Form
        editor.BlockManager.add("contact-form", {
            label: "İletişim Formu",
            category: "bootstrap",
            attributes: { class: "fa fa-envelope" },
            content: `<div class="container py-4">
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Adınız</label>
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
                </form>
            </div>`
        });
    }
    
    return {
        registerBlocks: registerBlocks
    };
})();