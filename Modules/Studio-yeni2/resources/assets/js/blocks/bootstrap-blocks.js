/**
 * Studio Editor - Bootstrap Blocks
 * Bootstrap bloklarını tanımlar ve kaydeder
 */
const StudioBootstrapBlocks = (function() {
    /**
     * Bootstrap bloklarını kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBootstrapBlocks(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return;
        }
        
        console.log('Bootstrap blokları kaydediliyor...');
        
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
        
        // Alert
        editor.BlockManager.add('alert', {
            label: 'Uyarı',
            category: 'bootstrap',
            attributes: { class: 'fa fa-exclamation-triangle' },
            content: `
                <div class="alert alert-primary" role="alert">
                    Bu bir bilgi uyarısıdır. Önemli bilgileri burada gösterebilirsiniz!
                </div>
            `
        });
        
        // Açılır Menü
        editor.BlockManager.add('dropdown', {
            label: 'Açılır Menü',
            category: 'bootstrap',
            attributes: { class: 'fa fa-caret-down' },
            content: `
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Açılır Menü
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="#">Seçenek 1</a></li>
                        <li><a class="dropdown-item" href="#">Seçenek 2</a></li>
                        <li><a class="dropdown-item" href="#">Seçenek 3</a></li>
                    </ul>
                </div>
            `
        });
        
        console.log('Bootstrap blokları kaydedildi');
    }
    
    /**
     * Bootstrap bileşenlerini kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBootstrapComponents(editor) {
        // Alert bileşeni
        editor.DomComponents.addType('alert', {
            isComponent: el => el.classList && el.classList.contains('alert'),
            model: {
                defaults: {
                    tagName: 'div',
                    attributes: { 
                        role: 'alert',
                        class: 'alert alert-primary'
                    },
                    traits: [
                        {
                            type: 'select',
                            name: 'class',
                            label: 'Tür',
                            options: [
                                { value: 'alert alert-primary', name: 'Birincil' },
                                { value: 'alert alert-secondary', name: 'İkincil' },
                                { value: 'alert alert-success', name: 'Başarılı' },
                                { value: 'alert alert-danger', name: 'Tehlike' },
                                { value: 'alert alert-warning', name: 'Uyarı' },
                                { value: 'alert alert-info', name: 'Bilgi' },
                                { value: 'alert alert-light', name: 'Açık' },
                                { value: 'alert alert-dark', name: 'Koyu' }
                            ]
                        },
                        {
                            type: 'checkbox',
                            name: 'dismissible',
                            label: 'Kapatılabilir',
                            valueTrue: 'alert-dismissible fade show',
                            valueFalse: ''
                        }
                    ]
                }
            }
        });
        
        // Kart bileşeni
        editor.DomComponents.addType('card', {
            isComponent: el => el.classList && el.classList.contains('card'),
            model: {
                defaults: {
                    tagName: 'div',
                    attributes: { class: 'card' },
                    traits: [
                        {
                            type: 'select',
                            name: 'class',
                            label: 'Hizalama',
                            options: [
                                { value: 'card', name: 'Normal' },
                                { value: 'card text-center', name: 'Ortalanmış' },
                                { value: 'card text-end', name: 'Sağa Yaslı' }
                            ]
                        },
                        {
                            type: 'select',
                            name: 'border',
                            label: 'Kenarlık',
                            options: [
                                { value: '', name: 'Normal' },
                                { value: 'border-primary', name: 'Birincil' },
                                { value: 'border-secondary', name: 'İkincil' },
                                { value: 'border-success', name: 'Başarılı' },
                                { value: 'border-danger', name: 'Tehlike' },
                                { value: 'border-warning', name: 'Uyarı' },
                                { value: 'border-info', name: 'Bilgi' }
                            ]
                        }
                    ]
                }
            }
        });
        
        console.log('Bootstrap bileşenleri kaydedildi');
    }
    
    /**
     * Bootstrap blokları için varsayılan ayarları döndürür
     * @returns {Object} - Varsayılan ayarlar
     */
    function getBootstrapBlockDefaults() {
        return {
            card: {
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
            },
            jumbotron: {
                content: `
                    <div class="p-5 mb-4 bg-light rounded-3">
                        <div class="container-fluid py-5">
                            <h1 class="display-5 fw-bold">Özel başlık</h1>
                            <p class="col-md-8 fs-4">Bu alanda daha büyük içerik veya tanıtım metni ekleyebilirsiniz.</p>
                            <button class="btn btn-primary btn-lg" type="button">Örnek buton</button>
                        </div>
                    </div>
                `
            },
            navbar: {
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
            },
            form: {
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
            },
            alert: {
                content: `
                    <div class="alert alert-primary" role="alert">
                        Bu bir bilgi uyarısıdır. Önemli bilgileri burada gösterebilirsiniz!
                    </div>
                `
            }
        };
    }
    
    // Dışa aktarılan API
    return {
        registerBootstrapBlocks,
        registerBootstrapComponents,
        getBootstrapBlockDefaults
    };
})();

// Global olarak kullanılabilir yap
window.StudioBootstrapBlocks = StudioBootstrapBlocks;