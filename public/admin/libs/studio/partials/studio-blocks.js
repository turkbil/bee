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
        console.log("Başlangıç blokları yükleniyor...");
        // Reset mevcut kategoriler ve bloklar
        editor.BlockManager.getAll().reset();
        
        // Blok kategorileri tanımla - sadece bir kez
        editor.BlockManager.getCategories().reset();
        editor.BlockManager.getCategories().add([
            { id: "layout", label: "Düzen Bileşenleri" },
            { id: "content", label: "İçerik Bileşenleri" },
            { id: "form", label: "Form Bileşenleri" },
            { id: "media", label: "Medya Bileşenleri" },
            { id: "widget", label: "Widgetlar" }
        ]);

        // 1 Sütunlu Bölüm
        editor.BlockManager.add("section-1col", {
            label: "1 Sütun",
            category: "layout",
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
            category: "layout",
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
            category: "layout",
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
            category: "layout",
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

        // Text
        editor.BlockManager.add("text", {
            label: "Metin",
            category: "content",
            attributes: { class: "fa fa-font" },
            content: `<div class="my-3">
                <h3>Başlık</h3>
                <p>Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit velit id diam ultrices, at facilisis dui tincidunt.</p>
            </div>`
        });

        // Button
        editor.BlockManager.add("button", {
            label: "Buton",
            category: "content",
            attributes: { class: "fa fa-square" },
            content: `<button class="btn btn-primary">Tıkla</button>`
        });

        // Image
        editor.BlockManager.add("image", {
            label: "Görsel",
            category: "media",
            attributes: { class: "fa fa-image" },
            content: `<img src="https://via.placeholder.com/800x400" class="img-fluid rounded" alt="Görsel açıklaması">`
        });

        // Contact Form
        editor.BlockManager.add("contact-form", {
            label: "İletişim Formu",
            category: "form",
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
        
        // Hero Bileşeni
        editor.BlockManager.add("hero", {
            label: "Hero Bölümü",
            category: "content",
            attributes: { class: "fa fa-star" },
            content: `<div class="px-4 py-5 my-5 text-center">
                <h1 class="display-5 fw-bold">Hero Başlık</h1>
                <div class="col-lg-6 mx-auto">
                    <p class="lead mb-4">
                        Hızlı ve etkili bir şekilde içeriklerinizi tasarlayın. Özel bileşenlerle
                        web sitenizin görünümünü kolayca değiştirin ve ziyaretçilerinize etkileyici deneyimler sunun.
                    </p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <button type="button" class="btn btn-primary btn-lg px-4 gap-3">Ana Buton</button>
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4">İkincil Buton</button>
                    </div>
                </div>
            </div>`
        });
        
        // Özellikler Bileşeni
        editor.BlockManager.add("features", {
            label: "Özellikler",
            category: "content",
            attributes: { class: "fa fa-list" },
            content: `<div class="container px-4 py-5">
                <h2 class="pb-2 border-bottom">Özellikler</h2>
                <div class="row g-4 py-5 row-cols-1 row-cols-md-3">
                    <div class="col d-flex align-items-start">
                        <div class="icon-square bg-light text-dark flex-shrink-0 me-3 p-3 rounded">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h2>Özellik 1</h2>
                            <p>Bu özelliğin detaylı açıklaması burada yer alacak.</p>
                        </div>
                    </div>
                    <div class="col d-flex align-items-start">
                        <div class="icon-square bg-light text-dark flex-shrink-0 me-3 p-3 rounded">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h2>Özellik 2</h2>
                            <p>Bu özelliğin detaylı açıklaması burada yer alacak.</p>
                        </div>
                    </div>
                    <div class="col d-flex align-items-start">
                        <div class="icon-square bg-light text-dark flex-shrink-0 me-3 p-3 rounded">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div>
                            <h2>Özellik 3</h2>
                            <p>Bu özelliğin detaylı açıklaması burada yer alacak.</p>
                        </div>
                    </div>
                </div>
            </div>`
        });
        
        // Footer
        editor.BlockManager.add("footer", {
            label: "Footer",
            category: "layout",
            attributes: { class: "fa fa-window-minimize" },
            content: `<footer class="py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h5>Hakkımızda</h5>
                            <p class="text-muted">
                                Kısa şirket açıklaması burada yer alacak. İşletmenizin
                                amacını ve değerlerini burada belirtebilirsiniz.
                            </p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h5>Hızlı Linkler</h5>
                            <ul class="nav flex-column">
                                <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Ana Sayfa</a></li>
                                <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Hakkımızda</a></li>
                                <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Hizmetler</a></li>
                                <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">İletişim</a></li>
                            </ul>
                        </div>
                        <div class="col-md-5 mb-3">
                            <form>
                                <h5>Bültenimize Abone Olun</h5>
                                <p class="text-muted">Aylık güncellemeler ve teklifler için kaydolun.</p>
                                <div class="d-flex w-100 gap-2">
                                    <input type="email" class="form-control" placeholder="E-posta adresiniz">
                                    <button class="btn btn-primary" type="button">Abone Ol</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between py-4 my-4 border-top">
                        <p>© 2025 Şirket Adı. Tüm hakları saklıdır.</p>
                        <ul class="list-unstyled d-flex">
                            <li class="ms-3"><a class="link-dark" href="#"><i class="fab fa-facebook"></i></a></li>
                            <li class="ms-3"><a class="link-dark" href="#"><i class="fab fa-instagram"></i></a></li>
                            <li class="ms-3"><a class="link-dark" href="#"><i class="fab fa-twitter"></i></a></li>
                        </ul>
                    </div>
                </div>
            </footer>`
        });
        
        console.log("Bloklar başarıyla yüklendi");
    }
    
    return {
        registerBlocks: registerBlocks
    };
})();