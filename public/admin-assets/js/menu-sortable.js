/**
 * Menu Sortable JavaScript
 */

document.addEventListener('livewire:initialized', function() {
    initMenuSortable();

    // Livewire event listener - sadece component refresh'te çağrılır
    Livewire.on('refresh-sortable', () => {
        console.log('🔄 Manual menu sortable refresh');
        setTimeout(() => initMenuSortable(), 100);
    });

    function initMenuSortable() {
        const container = document.getElementById('menu-sortable-list');
        if (!container) {
            console.error('❌ menu-sortable-list container bulunamadı!');
            return;
        }

        const menuItems = container.querySelectorAll('.menu-item');
        console.log('📋 Bulunan menu item sayısı:', menuItems.length);

        if (menuItems.length === 0) {
            console.warn('⚠️ Hiç menu item bulunamadı - liste boş olabilir');
        }

        // Mevcut sortable'ı temizle
        if (window.menuSortable) {
            window.menuSortable.destroy();
            window.menuSortable = null;
        }

        // Helper: Tüm child'ları recursive bul
        const findAllChildren = (parentId) => {
            const children = [];
            const allItems = Array.from(container.querySelectorAll('.menu-item'));

            allItems.forEach(itm => {
                const itemParentId = itm.getAttribute('data-parent-id');
                if (itemParentId === parentId) {
                    children.push(itm);
                    // Bu child'ın da child'larını bul (recursive)
                    const grandChildren = findAllChildren(itm.getAttribute('data-id'));
                    children.push(...grandChildren);
                }
            });

            return children;
        };

        // Yeni sortable oluştur - MenuManagement Pattern
        window.menuSortable = new Sortable(container, {
            animation: 150,
            ghostClass: 'menu-sortable-ghost',
            dragClass: 'menu-sortable-drag',
            handle: '.menu-drag-handle',
            group: 'menu-items',

            onStart: function(evt) {
                const item = evt.item;
                item._indentLevel = item.classList.contains('ps-5') ? 1 : 0;
                item._originalParentId = item.getAttribute('data-parent-id');

                // Child'ları bul ve görsel olarak işaretle
                const draggedId = item.getAttribute('data-id');
                const allChildren = findAllChildren(draggedId);

                // Child'lara class ekle ki sürüklerken birlikte hareket ediyor gibi gözüksün
                allChildren.forEach(child => {
                    child.classList.add('dragging-with-parent');
                });

                // Parent'ın kendisine de işaret koy
                item.classList.add('dragging-parent');

                console.log('Dragging started:', draggedId, 'with', allChildren.length, 'children');
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
                        item.classList.add('menu-drop-indicator');
                    } else {
                        item.classList.remove('menu-drop-indicator');
                    }
                } else {
                    item.classList.remove('menu-drop-indicator');
                }
            },

            onEnd: function(evt) {
                const item = evt.item;
                const previousItem = item.previousElementSibling;

                item.classList.remove('menu-drop-indicator');

                const MAX_DEPTH = 6;
                const INDENT_PX = 30;

                // Depth hesaplama helper
                const calculateDepth = (parentId) => {
                    if (!parentId) return 0;
                    const parent = container.querySelector(`[data-id="${parentId}"]`);
                    if (!parent) return 0;
                    const parentDepth = parseInt(parent.getAttribute('data-depth') || '0');
                    return parentDepth + 1;
                };

                let parentId = null;
                let depth = 0;

                if (previousItem) {
                    const dragOffset = evt.originalEvent?.clientX || 0;
                    const itemRect = item.getBoundingClientRect();
                    const itemLeft = itemRect.left;
                    const prevDepth = parseInt(previousItem.getAttribute('data-depth') || '0');
                    const prevId = previousItem.getAttribute('data-id');
                    let prevParentId = previousItem.getAttribute('data-parent-id');

                    // Clean prev parent (empty string to null)
                    prevParentId = (prevParentId === '' || prevParentId === null || prevParentId === 'null') ? null : prevParentId;

                    console.log('Drag Info:', dragOffset, itemLeft, prevDepth, prevParentId);

                    // Sağa sürüklediyse (indent) - önceki item'ın child'ı ol
                    if (dragOffset > itemLeft + 30 && prevDepth < MAX_DEPTH - 1) {
                        console.log('INDENT: Previous item child');
                        parentId = prevId;
                        depth = calculateDepth(parentId);
                    }
                    // Sola sürüklediyse (outdent) - bir seviye yukarı çık
                    else if (dragOffset < itemLeft - 30 && prevDepth > 0) {
                        console.log('OUTDENT: One level up');
                        if (prevParentId) {
                            const prevParent = container.querySelector(`[data-id="${prevParentId}"]`);
                            if (prevParent) {
                                parentId = prevParent.getAttribute('data-parent-id');
                                parentId = (parentId === '' || parentId === null || parentId === 'null') ? null : parentId;
                            } else {
                                parentId = null;
                            }
                        } else {
                            parentId = null;
                        }
                        depth = calculateDepth(parentId);
                    }
                    // Normal sürükleme - önceki item ile aynı seviyede
                    else {
                        console.log('NORMAL: Same level');
                        parentId = prevParentId;
                        depth = prevDepth;
                    }
                } else {
                    // İlk eleman her zaman ana kategori
                    console.log('FIRST: Root menu');
                    parentId = null;
                    depth = 0;
                }

                // CIRCULAR REFERENCE KONTROLÜ
                const draggedId = item.getAttribute('data-id');

                if (parentId && parentId !== 'null') {
                    // 1. Kendine referans engelle
                    if (parentId === draggedId) {
                        console.log('CIRCULAR: Self-reference blocked');
                        parentId = null;
                        depth = 0;
                    } else {
                        // 2. Kendi child'larından birine referans engelle
                        const tempChildren = findAllChildren(draggedId);
                        const childIds = tempChildren.map(c => c.getAttribute('data-id'));

                        if (childIds.includes(parentId)) {
                            console.log('CIRCULAR: Cannot move under own child');
                            parentId = null;
                            depth = 0;
                        }
                    }
                }

                // Max depth kontrolü
                if (depth >= MAX_DEPTH) {
                    depth = MAX_DEPTH - 1;
                    let tempParent = parentId;
                    let tempDepth = depth;
                    while (tempDepth >= MAX_DEPTH && tempParent) {
                        const parent = container.querySelector(`[data-id="${tempParent}"]`);
                        if (parent) {
                            tempParent = parent.getAttribute('data-parent-id') || null;
                            tempDepth--;
                        } else {
                            break;
                        }
                    }
                    parentId = tempParent;
                    depth = tempDepth;
                }

                // Sürüklenen item'ı güncelle
                item.setAttribute('data-depth', depth);
                item.setAttribute('data-parent-id', parentId || '');
                item.style.paddingLeft = (8 + (depth * INDENT_PX)) + 'px';

                console.log('Dragged item updated:', draggedId, 'depth:', depth, 'parent:', parentId);

                // Child'ları bul ve taşı
                const allChildren = findAllChildren(draggedId);
                console.log('Found children:', allChildren.length);

                if (allChildren.length > 0) {
                    let insertAfter = item;

                    allChildren.forEach(child => {
                        // Child'ı sürüklenen item'dan hemen sonra taşı
                        insertAfter.parentNode.insertBefore(child, insertAfter.nextSibling);
                        insertAfter = child;

                        // Child'ın depth'ini yeniden hesapla
                        // Child'ın parent_id DEĞİŞMEDİ ama parent'ın depth'i değişti
                        const childParentId = child.getAttribute('data-parent-id');
                        const childDepth = calculateDepth(childParentId);
                        child.setAttribute('data-depth', childDepth);
                        child.style.paddingLeft = (8 + (childDepth * INDENT_PX)) + 'px';

                        console.log('Child updated:', child.getAttribute('data-id'), 'depth:', childDepth);
                    });
                }

                // Dragging class'larını temizle
                item.classList.remove('dragging-parent');
                const allDraggingChildren = container.querySelectorAll('.dragging-with-parent');
                allDraggingChildren.forEach(child => {
                    child.classList.remove('dragging-with-parent');
                });

                // Tüm öğeleri güncelle
                const items = [];
                const allItems = Array.from(container.querySelectorAll('.menu-item'));

                allItems.forEach((itm, index) => {
                    const id = itm.getAttribute('data-id');
                    const dataParentId = itm.getAttribute('data-parent-id');
                    const itemDepth = itm.getAttribute('data-depth');

                    // Boş string veya null kontrolü
                    const parentId = (dataParentId === '' || dataParentId === null || dataParentId === 'null') ? null : dataParentId;

                    console.log('Item:', index + 1, 'ID:', id, 'Depth:', itemDepth, 'Parent:', parentId);

                    items.push({
                        id: id,
                        order: index + 1,
                        parentId: parentId
                    });
                });

                // DEBUG
                console.log('🚀 Menu order changed:', {
                    items: items,
                    itemsLength: items.length,
                    timestamp: new Date().toLocaleTimeString()
                });

                // Livewire'a sıralama verilerini gönder - MenuManagement Pattern
                if (items.length > 0) {
                    console.log('📤 Livewire dispatch gönderiliyor', { list: items });

                    Livewire.dispatch('updateOrder', { list: items });

                    console.log('✅ Livewire dispatch gönderildi');
                } else {
                    console.error('❌ Items listesi boş!');
                }
            }
        });
    }
});

// MenuManagement Pattern - clientSideLanguageSwitch fonksiyonu
window.clientSideLanguageSwitch = function(language) {
    console.log('🔄 Client-side language switch:', language);

    // Tüm language-content divlerini gizle
    document.querySelectorAll('.language-content').forEach(function(el) {
        el.style.display = 'none';
    });

    // Seçilen dili göster
    const selectedLangDiv = document.querySelector(`.language-content[data-language="${language}"]`);
    if (selectedLangDiv) {
        selectedLangDiv.style.display = 'block';
    }

    // Dil butonlarını güncelle
    document.querySelectorAll('.language-switch-btn').forEach(function(btn) {
        const btnLang = btn.getAttribute('data-language');
        if (btnLang === language) {
            btn.classList.remove('text-muted');
            btn.classList.add('text-primary');
            btn.style.borderBottom = '2px solid var(--primary-color)';
            btn.setAttribute('disabled', 'disabled');
        } else {
            btn.classList.remove('text-primary');
            btn.classList.add('text-muted');
            btn.style.borderBottom = '2px solid transparent';
            btn.removeAttribute('disabled');
        }
    });
};
