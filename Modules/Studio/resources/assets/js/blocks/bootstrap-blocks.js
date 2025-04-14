/**
 * Studio Bootstrap Blocks
 * Bootstrap bloklarını yöneten modül
 */
const StudioBootstrapBlocks = (function() {
    /**
     * Bootstrap bileşenlerini kaydet
     * @param {Object} editor GrapesJS editor örneği
     */
    function registerBootstrapComponents(editor) {
        // Kart bileşeni
        editor.DomComponents.addType('card', {
            isComponent: el => el.classList && el.classList.contains('card'),
            model: {
                defaults: {
                    tagName: 'div',
                    attributes: { class: 'card' },
                    traits: [
                        {
                            type: 'checkbox',
                            name: 'header',
                            label: 'Kart Başlığı',
                            changeProp: true
                        },
                        {
                            type: 'checkbox',
                            name: 'footer',
                            label: 'Kart Altbilgisi',
                            changeProp: true
                        },
                        {
                            type: 'checkbox',
                            name: 'image',
                            label: 'Kart Görseli',
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'border-color',
                            label: 'Kenarlık Rengi',
                            options: [
                                { value: '', name: 'Varsayılan' },
                                { value: 'border-primary', name: 'Primary' },
                                { value: 'border-secondary', name: 'Secondary' },
                                { value: 'border-success', name: 'Success' },
                                { value: 'border-danger', name: 'Danger' },
                                { value: 'border-warning', name: 'Warning' },
                                { value: 'border-info', name: 'Info' },
                                { value: 'border-light', name: 'Light' },
                                { value: 'border-dark', name: 'Dark' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'text-color',
                            label: 'Metin Rengi',
                            options: [
                                { value: '', name: 'Varsayılan' },
                                { value: 'text-primary', name: 'Primary' },
                                { value: 'text-secondary', name: 'Secondary' },
                                { value: 'text-success', name: 'Success' },
                                { value: 'text-danger', name: 'Danger' },
                                { value: 'text-warning', name: 'Warning' },
                                { value: 'text-info', name: 'Info' },
                                { value: 'text-light', name: 'Light' },
                                { value: 'text-dark', name: 'Dark' },
                                { value: 'text-muted', name: 'Muted' },
                                { value: 'text-white', name: 'White' }
                            ],
                            changeProp: true
                        }
                    ],
                    header: true,
                    footer: false,
                    image: true,
                    'border-color': '',
                    'text-color': '',
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:header change:footer change:image change:border-color change:text-color', this.updateCard);
                    this.updateCard();
                },
                updateCard() {
                    const model = this.model;
                    const hasHeader = model.get('header');
                    const hasFooter = model.get('footer');
                    const hasImage = model.get('image');
                    const borderColor = model.get('border-color');
                    const textColor = model.get('text-color');
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Renkle ilgili sınıfları kaldır
                    const filteredClasses = classes.filter(cls => 
                        !cls.startsWith('border-') && !cls.startsWith('text-')
                    );
                    
                    // Yeni sınıfları ekle
                    if (borderColor) filteredClasses.push(borderColor);
                    if (textColor) filteredClasses.push(textColor);
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                    
                    // İçeriği güncelle
                    let content = '';
                    
                    if (hasImage) {
                        content += `<img src="https://placehold.co/800x400" class="card-img-top" alt="Kart görseli">`;
                    }
                    
                    if (hasHeader) {
                        content += `<div class="card-header">Kart Başlığı</div>`;
                    }
                    
                    content += `
                    <div class="card-body">
                        <h5 class="card-title">Kart başlığı</h5>
                        <p class="card-text">Kart içeriği buraya gelecek. Kısa bir açıklama metni yazabilirsiniz.</p>
                        <a href="#" class="btn btn-primary">Detaylar</a>
                    </div>`;
                    
                    if (hasFooter) {
                        content += `<div class="card-footer text-muted">Kart Altbilgisi</div>`;
                    }
                    
                    model.components(content);
                }
            }
        });
        
        // Navbar bileşeni
        editor.DomComponents.addType('navbar', {
            isComponent: el => el.classList && el.classList.contains('navbar'),
            model: {
                defaults: {
                    tagName: 'nav',
                    attributes: { class: 'navbar navbar-expand-lg navbar-light bg-light' },
                    traits: [
                        {
                            type: 'select',
                            name: 'navbar-color',
                            label: 'Renk Şeması',
                            options: [
                                { value: 'navbar-light bg-light', name: 'Light' },
                                { value: 'navbar-dark bg-dark', name: 'Dark' },
                                { value: 'navbar-dark bg-primary', name: 'Primary' },
                                { value: 'navbar-dark bg-secondary', name: 'Secondary' },
                                { value: 'navbar-dark bg-success', name: 'Success' },
                                { value: 'navbar-dark bg-danger', name: 'Danger' },
                                { value: 'navbar-dark bg-warning', name: 'Warning' },
                                { value: 'navbar-dark bg-info', name: 'Info' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'checkbox',
                            name: 'navbar-fixed',
                            label: 'Sabit Menü',
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'navbar-position',
                            label: 'Pozisyon',
                            options: [
                                { value: '', name: 'Varsayılan' },
                                { value: 'fixed-top', name: 'Üstte Sabit' },
                                { value: 'fixed-bottom', name: 'Altta Sabit' },
                                { value: 'sticky-top', name: 'Yapışkan Üst' }
                            ],
                            changeProp: true
                        }
                    ],
                    'navbar-color': 'navbar-light bg-light',
                    'navbar-fixed': false,
                    'navbar-position': '',
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:navbar-color change:navbar-fixed change:navbar-position', this.updateNavbar);
                },
                updateNavbar() {
                    const model = this.model;
                    const navbarColor = model.get('navbar-color');
                    const navbarFixed = model.get('navbar-fixed');
                    const navbarPosition = model.get('navbar-position');
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Navbar renk ve pozisyon sınıflarını kaldır
                    const filteredClasses = classes.filter(cls => 
                        !cls.startsWith('navbar-light') && 
                        !cls.startsWith('navbar-dark') && 
                        !cls.startsWith('bg-') && 
                        !cls.startsWith('fixed-') && 
                        !cls.startsWith('sticky-')
                    );
                    
                    // Temel navbar sınıfları
                    filteredClasses.push('navbar');
                    filteredClasses.push('navbar-expand-lg');
                    
                    // Renk şeması
                    const colorClasses = navbarColor.split(' ');
                    colorClasses.forEach(cls => filteredClasses.push(cls));
                    
                    // Pozisyon
                    if (navbarPosition) {
                        filteredClasses.push(navbarPosition);
                    }
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                }
            }
        });
        
        // Alert bileşeni
        editor.DomComponents.addType('alert', {
            isComponent: el => el.classList && el.classList.contains('alert'),
            model: {
                defaults: {
                    tagName: 'div',
                    attributes: { class: 'alert alert-primary', role: 'alert' },
                    traits: [
                        {
                            type: 'select',
                            name: 'alert-type',
                            label: 'Tip',
                            options: [
                                { value: 'alert-primary', name: 'Primary' },
                                { value: 'alert-secondary', name: 'Secondary' },
                                { value: 'alert-success', name: 'Success' },
                                { value: 'alert-danger', name: 'Danger' },
                                { value: 'alert-warning', name: 'Warning' },
                                { value: 'alert-info', name: 'Info' },
                                { value: 'alert-light', name: 'Light' },
                                { value: 'alert-dark', name: 'Dark' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'checkbox',
                            name: 'alert-dismissible',
                            label: 'Kapatılabilir',
                            changeProp: true
                        }
                    ],
                    'alert-type': 'alert-primary',
                    'alert-dismissible': false,
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:alert-type change:alert-dismissible', this.updateAlert);
                },
                updateAlert() {
                    const model = this.model;
                    const alertType = model.get('alert-type');
                    const alertDismissible = model.get('alert-dismissible');
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Alert tip sınıflarını kaldır
                    const filteredClasses = classes.filter(cls => 
                        !cls.startsWith('alert-')
                    );
                    
                    // Temel alert sınıfı
                    filteredClasses.push('alert');
                    
                    // Tip sınıfı
                    filteredClasses.push(alertType);
                    
                    // Kapatılabilir
                    if (alertDismissible) {
                        filteredClasses.push('alert-dismissible');
                        filteredClasses.push('fade');
                        filteredClasses.push('show');
                        
                        // İçeriği güncelle
                        let content = model.get('content');
                        
                        // Kapatma butonu ekle
                        if (!content.includes('btn-close')) {
                            content += `
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                            `;
                            model.set('content', content);
                        }
                    } else {
                        // Kapatma butonunu kaldır
                        let content = model.get('content');
                        content = content.replace(/<button.*btn-close.*<\/button>/g, '');
                        model.set('content', content);
                    }
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                }
            }
        });
        
        console.log('Bootstrap bileşenleri kaydedildi');
    }
    
    /**
     * Bootstrap bloklarını kaydet
     * @param {Object} editor GrapesJS editor örneği
     */
    function registerBootstrapBlocks(editor) {
        // Önce bileşenleri kaydet
        registerBootstrapComponents(editor);
        
        // Blok yöneticisini al
        const blockManager = editor.BlockManager;
        
        // Kart bloğu
        blockManager.add('card', {
            label: 'Kart',
            category: 'bootstrap',
            attributes: { class: 'fa fa-credit-card' },
            content: {
                type: 'card'
            }
        });
        
        // Jumbotron bloğu
        blockManager.add('jumbotron', {
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
        
        // Navbar bloğu
        blockManager.add('navbar', {
            label: 'Navbar',
            category: 'bootstrap',
            attributes: { class: 'fa fa-bars' },
            content: {
                type: 'navbar',
                content: `
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
                `
            }
        });
        
        // Form bloğu
        blockManager.add('form', {
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
        
        // Alert bloğu
        blockManager.add('alert', {
            label: 'Alert',
            category: 'bootstrap',
            attributes: { class: 'fa fa-exclamation-triangle' },
            content: {
                type: 'alert',
                content: 'Bu bir bildirim mesajıdır. Önemli bir bilgi içerir.'
            }
        });
        
        // Accordion bloğu
        blockManager.add('accordion', {
            label: 'Accordion',
            category: 'bootstrap',
            attributes: { class: 'fa fa-bars' },
            content: `
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Accordion Öğesi #1
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>Bu birinci öğenin içeriğidir.</strong> Daha fazla metin buraya eklenebilir. İçeriği istediğiniz gibi düzenleyebilirsiniz.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Accordion Öğesi #2
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>Bu ikinci öğenin içeriğidir.</strong> Daha fazla metin buraya eklenebilir. İçeriği istediğiniz gibi düzenleyebilirsiniz.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Accordion Öğesi #3
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <strong>Bu üçüncü öğenin içeriğidir.</strong> Daha fazla metin buraya eklenebilir. İçeriği istediğiniz gibi düzenleyebilirsiniz.
                            </div>
                        </div>
                    </div>
                </div>
            `
        });
        
        // Modal bloğu
        blockManager.add('modal', {
            label: 'Modal',
            category: 'bootstrap',
            attributes: { class: 'fa fa-window-maximize' },
            content: `
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Modalı Aç
                </button>
                
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Modal Başlığı</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                            </div>
                            <div class="modal-body">
                                Modal içeriği buraya gelecek.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                <button type="button" class="btn btn-primary">Kaydet</button>
                            </div>
                        </div>
                    </div>
                </div>
            `
        });
        
        console.log('Bootstrap blokları kaydedildi');
    }
    
    /**
     * Bootstrap blokları için varsayılan ayarları al
     * @returns {Object} Varsayılan ayarlar
     */
    function getBootstrapBlockDefaults() {
        return {
            card: {
                header: true,
                footer: false,
                image: true,
                'border-color': '',
                'text-color': ''
            },
            navbar: {
                'navbar-color': 'navbar-light bg-light',
                'navbar-fixed': false,
                'navbar-position': ''
            },
            alert: {
                'alert-type': 'alert-primary',
                'alert-dismissible': false
            }
        };
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        registerBootstrapComponents: registerBootstrapComponents,
        registerBootstrapBlocks: registerBootstrapBlocks,
        getBootstrapBlockDefaults: getBootstrapBlockDefaults
    };
})();

// Global olarak kullanılabilir yap
window.StudioBootstrapBlocks = StudioBootstrapBlocks;