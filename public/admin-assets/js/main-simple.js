// Normal Selectbox Sistem - Choices.js TAMAMEN KALDIRILDI
function initializeSelectboxes() {
    const selectElements = document.querySelectorAll('select[data-choices]');
    console.log('🎯 INITIALIZE NORMAL SELECTS - Found elements:', selectElements.length);
    
    selectElements.forEach(function(element) {
        if (element.dataset.selectInitialized) {
            console.log('⏭️ Skipping already initialized element');
            return;
        }
        
        element.dataset.selectInitialized = 'true';
        console.log('🎯 Initializing normal select for:', element);
        
        // data-choices attribute'unu kaldır (artık normal select)
        element.removeAttribute('data-choices');
        element.removeAttribute('data-choices-search');
        element.removeAttribute('data-choices-filter');
        
        // Normal select class'ı ekle - Choices.js benzeri stil için
        element.classList.add('choices-style-select');
        
        // Livewire entegrasyonu
        if (element.hasAttribute('wire:model')) {
            // Değişiklik dinleme
            element.addEventListener('change', function(e) {
                // Livewire'a input event'i gönder
                this.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }
    });
}

// Tags Input Sistemi
function initializeTagsInput() {
    const tagInputs = document.querySelectorAll('.tags-input');
    
    tagInputs.forEach(function(container) {
        if (container.dataset.initialized) return;
        container.dataset.initialized = 'true';
        
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const tagsContainer = container.querySelector('.tags-container');
        const tagInput = container.querySelector('.tag-input');
        
        if (!hiddenInput || !tagsContainer || !tagInput) return;
        
        // Mevcut değerleri yükle
        const currentValue = hiddenInput.value;
        const tags = currentValue ? currentValue.split(',').filter(tag => tag.trim()) : [];
        
        function renderTags() {
            tagsContainer.innerHTML = '';
            tags.forEach((tag, index) => {
                const tagElement = document.createElement('span');
                tagElement.className = 'badge bg-primary me-1 mb-1';
                tagElement.innerHTML = `
                    ${tag.trim()}
                    <button type="button" class="btn-close btn-close-white ms-1" data-index="${index}"></button>
                `;
                tagsContainer.appendChild(tagElement);
            });
            hiddenInput.value = tags.join(',');
        }
        
        function addTag(tagText) {
            const trimmedTag = tagText.trim();
            if (trimmedTag && !tags.includes(trimmedTag)) {
                tags.push(trimmedTag);
                renderTags();
                tagInput.value = '';
            }
        }
        
        function removeTag(index) {
            tags.splice(index, 1);
            renderTags();
        }
        
        // Event listeners
        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(this.value);
            }
        });
        
        tagInput.addEventListener('blur', function() {
            if (this.value.trim()) {
                addTag(this.value);
            }
        });
        
        tagsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-close')) {
                const index = parseInt(e.target.dataset.index);
                removeTag(index);
            }
        });
        
        // İlk render
        renderTags();
    });
}

// Sistem başlatma
document.addEventListener('DOMContentLoaded', function() {
    initializeSelectboxes();
    initializeTagsInput();
});

// Livewire güncellemelerinde yeniden başlat
document.addEventListener('livewire:updated', function() {
    initializeSelectboxes();
    initializeTagsInput();
});

document.addEventListener('livewire:morph.updated', function() {
    initializeSelectboxes();
    initializeTagsInput();
});