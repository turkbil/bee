document.addEventListener("livewire:initialized", () => {
    const sortableList = document.getElementById("sortable-list");
    if (sortableList) {
        new Sortable(sortableList, {
            animation: 250,
            delay: 50,
            delayOnTouchOnly: true,
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            forceFallback: false,
            onStart: function () {
                document.body.style.cursor = "grabbing";
            },
            onEnd: function (evt) {
                document.body.style.cursor = "default";
                const items = Array.from(sortableList.children).map(
                    (item, index) => ({
                        value: parseInt(item.dataset.id),
                        order: index + 1,
                    })
                );

                // Sayı animasyonu
                sortableList
                    .querySelectorAll(".order-number")
                    .forEach((el, index) => {
                        const oldNumber = parseInt(el.textContent);
                        const newNumber = index + 1;

                        if (oldNumber !== newNumber) {
                            el.classList.add("animate");
                            setTimeout(() => {
                                el.textContent = newNumber;
                            }, 250);
                            setTimeout(() => {
                                el.classList.remove("animate");
                            }, 500);
                        }
                    });

                Livewire.dispatch("updateOrder", { list: items });
            },
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const sortableList = document.getElementById("sortable-list");

    if (sortableList) {
        const sortable = new Sortable(sortableList, {
            animation: 150,
            handle: ".widget-drag-handle",
            ghostClass: "sortable-ghost",
            onEnd: function () {
                const items = [];
                const nodes = sortableList.querySelectorAll(".widget-item");

                nodes.forEach((node, index) => {
                    items.push({
                        value: node.getAttribute("data-id"),
                        order: index + 1,
                    });
                });

                if (window.Livewire) {
                    window.Livewire.dispatch("updateOrder", items);
                }
            },
        });
    }
});

document.addEventListener('livewire:initialized', function() {
    initCategorySortable();
    
    Livewire.hook('morph.updated', () => {
        initCategorySortable();
    });
    
    function initCategorySortable() {
        const container = document.getElementById('category-sortable-list');
        if (!container) {
            console.log('Sortable listesi bulunamadı');
            return;
        }
        
        // Mevcut sortable'ı temizle
        if (window.categorySortable) {
            window.categorySortable.destroy();
            window.categorySortable = null;
        }
        
        // Yeni sortable oluştur
        window.categorySortable = new Sortable(container, {
            animation: 150,
            ghostClass: 'category-sortable-ghost',
            dragClass: 'category-sortable-drag',
            handle: '.category-drag-handle',
            group: 'categories',
            
            onStart: function(evt) {
                const item = evt.item;
                item._indentLevel = item.classList.contains('ps-5') ? 1 : 0;
                item._originalParentId = item.getAttribute('data-parent-id');
            },
            
            onMove: function(evt) {
                return true;
            },
            
            onChange: function(evt) {
                // Sürükleme sırasında alt kategori görsel göstergesini ayarla
                const item = evt.item;
                const previousItem = item.previousElementSibling;
                
                if (previousItem) {
                    const dragOffset = evt.originalEvent?.clientX || 0;
                    const itemRect = item.getBoundingClientRect();
                    const itemLeft = itemRect.left;
                    
                    // Sağa doğru sürüklendiyse alt kategori olarak göster
                    if (dragOffset > itemLeft + 50) {
                        item.classList.add('category-drop-indicator');
                    } else {
                        item.classList.remove('category-drop-indicator');
                    }
                } else {
                    item.classList.remove('category-drop-indicator');
                }
            },
            
            onEnd: function(evt) {
                // Sürükleme sona erdiğinde
                const item = evt.item;
                const previousItem = item.previousElementSibling;
                const nextItem = item.nextElementSibling;
                
                // Görsel göstergeyi kaldır
                item.classList.remove('category-drop-indicator');
                
                // Alt kategori veya ana kategori belirleme
                let isSubcategory = false;
                let parentId = null;
                
                // Gerçek konumu ve parent-child ilişkisini belirle
                if (previousItem) {
                    const dragOffset = evt.originalEvent?.clientX || 0;
                    const itemRect = item.getBoundingClientRect();
                    const itemLeft = itemRect.left;
                    
                    // Eğer önceki öğe ana kategori ise VE sağa doğru sürüklendiyse
                    if (!previousItem.classList.contains('ps-5') && dragOffset > itemLeft + 50) {
                        isSubcategory = true;
                        parentId = previousItem.getAttribute('data-id');
                        item.classList.add('ps-5');
                        item.setAttribute('data-parent-id', parentId);
                    } 
                    // Eğer önceki öğe zaten alt kategori ise
                    else if (previousItem.classList.contains('ps-5')) {
                        // Önceki öğenin parent ID'sini al
                        const prevParentId = previousItem.getAttribute('data-parent-id');
                        
                        // Sağa doğru sürüklendiyse önceki öğe ile aynı seviyede alt kategori olarak ekle
                        if (dragOffset > itemLeft + 50) {
                            isSubcategory = true;
                            parentId = prevParentId;
                            item.classList.add('ps-5');
                            item.setAttribute('data-parent-id', parentId);
                        } else {
                            // Ana kategori olarak ekle
                            item.classList.remove('ps-5');
                            item.removeAttribute('data-parent-id');
                        }
                    } else {
                        // Ana kategori olarak ekle
                        item.classList.remove('ps-5');
                        item.removeAttribute('data-parent-id');
                    }
                } else {
                    // Listedeki ilk öğe her zaman ana kategori olmalı
                    item.classList.remove('ps-5');
                    item.removeAttribute('data-parent-id');
                }
                
                // Tüm kategorileri dolaşıp sıralama ve parent-child ilişkilerini güncelle
                const items = [];
                const allItems = Array.from(container.querySelectorAll('.category-item'));
                
                // İlk önce ana kategorileri işlem
                let currentParentId = null;
                let currentOrder = 1;
                
                allItems.forEach((item, index) => {
                    if (!item) return;
                    
                    const id = item.getAttribute('data-id');
                    if (!id) return;
                    
                    // Alt kategori mi ana kategori mi belirle
                    const isChild = item.classList.contains('ps-5');
                    
                    // Alt kategoriyse parent'ını bul
                    let itemParentId = null;
                    if (isChild) {
                        itemParentId = item.getAttribute('data-parent-id');
                        
                        // Eğer data-parent-id yoksa, önceki ana kategoriyi bul
                        if (!itemParentId) {
                            let prevSibling = item.previousElementSibling;
                            while (prevSibling) {
                                if (!prevSibling.classList.contains('ps-5')) {
                                    itemParentId = prevSibling.getAttribute('data-id');
                                    break;
                                }
                                prevSibling = prevSibling.previousElementSibling;
                            }
                            // parent-id'yi güncelle
                            if (itemParentId) {
                                item.setAttribute('data-parent-id', itemParentId);
                            }
                        }
                    }
                    
                    items.push({
                        id: id,
                        order: index + 1,
                        parentId: itemParentId
                    });
                });
                
                // Livewire'a sıralama verilerini gönder
                if (items.length > 0) {
                    Livewire.dispatch('updateOrder', { list: items });
                    
                    // Sürüklemeyi tamamladıktan sonra DOM değiştiğinden tekrar Sortable'ı başlat
                    setTimeout(() => {
                        initCategorySortable();
                    }, 500);
                }
            }
        });
    }
});