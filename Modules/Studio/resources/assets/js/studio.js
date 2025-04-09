(function () {
    "use strict";

    // GrapesJS için Türkçe dil paketi
    if (typeof grapesjs !== "undefined") {
        grapesjs.plugins.add("grapesjs-lang-tr", (editor, opts = {}) => {
            editor.I18n.addMessages({
                tr: {
                    styleManager: {
                        properties: {
                            width: "Genişlik",
                            height: "Yükseklik",
                            "max-width": "Maks. genişlik",
                            "min-height": "Min. yükseklik",
                            margin: "Kenar boşluğu",
                            padding: "İç kenar boşluğu",
                            "font-family": "Yazı tipi",
                            "font-size": "Yazı boyutu",
                            "font-weight": "Yazı kalınlığı",
                            "letter-spacing": "Harf aralığı",
                            color: "Renk",
                            "line-height": "Satır yüksekliği",
                            "text-align": "Metin hizalama",
                            "text-decoration": "Metin dekorasyonu",
                            "text-shadow": "Metin gölgesi",
                            "background-color": "Arka plan rengi",
                            "background-image": "Arka plan resmi",
                            "background-repeat": "Arka plan tekrarı",
                            "background-position": "Arka plan konumu",
                            "background-attachment": "Arka plan eki",
                            "background-size": "Arka plan boyutu",
                            "border-radius": "Kenarlık yarıçapı",
                            border: "Kenarlık",
                            "border-width": "Kenarlık genişliği",
                            "border-style": "Kenarlık stili",
                            "border-color": "Kenarlık rengi",
                            opacity: "Opaklık",
                            "box-shadow": "Kutu gölgesi",
                            transition: "Geçiş",
                            transform: "Dönüşüm",
                            display: "Görünüm",
                            "flex-direction": "Flex yönü",
                            "flex-wrap": "Flex sarma",
                            "justify-content": "İçeriği hizala",
                            "align-items": "Öğeleri hizala",
                            "align-content": "İçeriği hizala",
                            order: "Sıralama",
                            "flex-basis": "Flex temeli",
                            "flex-grow": "Flex büyüme",
                            "flex-shrink": "Flex küçülme",
                            "align-self": "Kendini hizala",
                        },
                    },
                    panels: {
                        buttons: {
                            titles: {
                                "open-css": "CSS Düzenle",
                                "open-js": "JavaScript Düzenle",
                            },
                        },
                    },
                    commands: {
                        defaults: {
                            undo: {
                                title: "Geri Al",
                            },
                            redo: {
                                title: "İleri Al",
                            },
                            "clean-all": {
                                title: "Tümünü Temizle",
                                confirm:
                                    "Bu işlem tüm içeriği silecektir. Devam etmek istiyor musunuz?",
                            },
                        },
                    },
                    blockManager: {
                        labels: {
                            section: "Bölüm",
                            container: "Konteyner",
                            text: "Metin",
                            image: "Resim",
                            video: "Video",
                            link: "Bağlantı",
                            heading: "Başlık",
                            paragraph: "Paragraf",
                            list: "Liste",
                            card: "Kart",
                            button: "Düğme",
                            row: "Satır",
                            column: "Sütun",
                            "row-2-col": "2 Sütun",
                            "row-3-col": "3 Sütun",
                            "row-4-col": "4 Sütun",
                        },
                    },
                },
            });

            editor.I18n.setLocale("tr");
        });
    }

    // Widget bileşenini GrapesJS'e ekle
    if (typeof grapesjs !== "undefined") {
        grapesjs.plugins.add(
            "grapesjs-widget-component",
            (editor, opts = {}) => {
                const domc = editor.DomComponents;
                const defaultType = domc.getType("default");
                const defaultModel = defaultType.model;
                const defaultView = defaultType.view;

                // Widget bileşeni modeli
                domc.addType("widget", {
                    model: defaultModel.extend({
                        defaults: {
                            ...defaultModel.prototype.defaults,
                            name: "Widget",
                            widget_id: "",
                            droppable: false,
                            traits: [
                                {
                                    type: "number",
                                    name: "widget_id",
                                    label: "Widget ID",
                                    changeProp: true,
                                },
                            ],
                        },

                        init() {
                            this.on("change:widget_id", this.updateContent);
                        },

                        updateContent() {
                            // Widget ID değiştiğinde içeriği güncelle
                            const widgetId = this.get("widget_id");
                            if (widgetId) {
                                // AJAX isteği ile widget içeriğini al
                                fetch(`/admin/studio/widget/${widgetId}`)
                                    .then((response) => response.json())
                                    .then((data) => {
                                        if (data.success) {
                                            this.set("content", data.html);
                                        }
                                    })
                                    .catch((error) => {
                                        console.error(
                                            "Widget içeriği alınamadı:",
                                            error
                                        );
                                    });
                            }
                        },
                    }),

                    view: defaultView.extend({
                        render() {
                            defaultView.prototype.render.apply(this, arguments);
                            return this;
                        },
                    }),
                });
            }
        );
    }
})();
