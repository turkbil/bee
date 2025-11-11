/**
 * TENANT-SAFE ADMIN PANEL ASSETS
 * Admin panel için tenant bağımsız asset yönetimi
 */

// Import necessary libraries
import axios from 'axios';

// FilePond imports
import * as FilePond from 'filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';

// Register FilePond plugins
FilePond.registerPlugin(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImagePreview
);

// Make FilePond available globally
window.FilePond = FilePond;

/**
 * Universal FilePond Helper
 * Tüm admin sayfalarda kullanılabilir FilePond initialization
 */
window.initFilePond = function(inputSelector, options = {}) {
    const defaultOptions = {
        acceptedFileTypes: options.acceptedFileTypes || ['image/*'],
        maxFileSize: options.maxFileSize || '10MB',
        stylePanelLayout: 'compact',
        credits: false,
        allowRevert: true,
        instantUpload: false,
        labelIdle: options.labelIdle || `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 200px; padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <i class="fa fa-upload" style="font-size: 48px; color: var(--tblr-muted);"></i>
                </div>
                <h4 class="mb-1">Dosyayı Sürükle ve Bırak</h4>
                <p class="text-muted mb-2">veya <span class="filepond--label-action">tıklayarak dosya seç</span></p>
                <small class="text-muted d-block">${options.hint || 'Maksimum boyut: ' + (options.maxFileSize || '10MB')}</small>
            </div>
        `,
        labelFileProcessing: 'Yükleniyor',
        labelFileProcessingComplete: 'Yükleme tamamlandı',
        labelFileProcessingAborted: 'Yükleme iptal edildi',
        labelFileProcessingError: 'Yükleme hatası',
        labelTapToCancel: 'iptal',
        labelTapToRetry: 'tekrar dene',
        labelTapToUndo: 'geri al',
        labelButtonRemoveItem: 'Kaldır',
        labelButtonAbortItemLoad: 'İptal',
        labelButtonRetryItemLoad: 'Tekrar Dene',
        labelButtonAbortItemProcessing: 'İptal',
        labelButtonUndoItemProcessing: 'Geri Al',
        labelButtonRetryItemProcessing: 'Tekrar Dene',
        labelButtonProcessItem: 'Yükle',
        server: {
            process: (fieldName, file, metadata, load, error, progress, abort) => {
                // Livewire dosya upload'ı otomatik olarak çalışacak
                load(file.name);
            },
            revert: (uniqueFileId, load, error) => {
                // Remove file - Livewire handle eder
                load();
            }
        },
        ...options
    };

    const input = document.querySelector(inputSelector);
    if (input && typeof FilePond !== 'undefined') {
        return FilePond.create(input, defaultOptions);
    } else {
        console.warn(`⚠️ FilePond input not found: ${inputSelector}`);
        return null;
    }
};

// Admin-specific configurations
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token for admin requests
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Admin panel utilities
window.AdminUtils = {
    // Cache clearing
    clearCache: function(type = 'all') {
        return axios.post('/admin/cache/clear', { type })
            .then(response => {
                if (response.data.success) {
                    this.showNotification('Cache temizlendi', 'success');
                }
                return response.data;
            })
            .catch(error => {
                this.showNotification('Cache temizleme başarısız', 'error');
                throw error;
            });
    },

    // Notification system
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    },

    // Livewire refresh helper
    refreshComponent: function(componentId) {
        if (window.Livewire) {
            window.Livewire.emit('refresh', componentId);
        }
    },

    // Form validation helper
    validateForm: function(formElement) {
        const inputs = formElement.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    }
};

// Global admin event handlers
document.addEventListener('DOMContentLoaded', function() {
    // Tenant selector - admin panelde
    const tenantSelector = document.querySelector('#tenant-selector');
    if (tenantSelector) {
        tenantSelector.addEventListener('change', function() {
            const selectedTenant = this.value;
            if (selectedTenant) {
                window.location.href = `http://${selectedTenant}`;
            }
        });
    }

    // Auto-save drafts
    const autoSaveForms = document.querySelectorAll('[data-auto-save]');
    autoSaveForms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', debounce(function() {
                const formData = new FormData(form);
                formData.append('_draft', '1');

                axios.post(form.action, formData)
                    .then(() => {
                        console.log('📝 Draft saved');
                    })
                    .catch(error => {
                        console.error('Draft save failed:', error);
                    });
            }, 2000));
        });
    });

    // Table row selection
    const selectAllCheckbox = document.querySelector('#select-all-items');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButtons();
        });
    }

    // Bulk action buttons
    function updateBulkActionButtons() {
        const selectedRows = document.querySelectorAll('.row-checkbox:checked');
        const bulkActions = document.querySelector('.bulk-actions');

        if (bulkActions) {
            bulkActions.style.display = selectedRows.length > 0 ? 'block' : 'none';
        }
    }

    // Row checkbox handlers
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox')) {
            updateBulkActionButtons();
        }
    });
});

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/* ========================================
   DUAL LISTBOX COMPONENT FUNCTIONS
   ======================================== */

// Global Dual Listbox initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeDualListbox();
});

// Initialize dual listbox for dynamically loaded content
window.initializeDualListbox = function() {
    // Initialize item selection for all dual listboxes
    document.querySelectorAll('.listbox-item').forEach(item => {
        // Single click: Toggle selection
        item.addEventListener('click', function(e) {
            // Prevent double click from triggering click
            if (e.detail === 1) {
                setTimeout(() => {
                    if (!this.classList.contains('double-clicked')) {
                        this.classList.toggle('selected');
                    }
                    this.classList.remove('double-clicked');
                }, 200);
            }
        });

        // Double click: Transfer to other side
        item.addEventListener('dblclick', function() {
            this.classList.add('double-clicked');
            const listboxParent = this.parentElement;
            const listboxId = listboxParent.id;

            // Mark as selected for transfer
            this.classList.add('selected');

            // Determine direction based on listbox ID pattern
            // Pattern: "available-XXX" → transfer right, "selected-XXX" → transfer left
            if (listboxId.startsWith('available-')) {
                const entityName = listboxId.replace('available-', '');
                const transferFunctionName = 'transfer' + capitalize(entityName) + 'Right';
                if (typeof window[transferFunctionName] === 'function') {
                    window[transferFunctionName]();
                }
            } else if (listboxId.startsWith('selected-')) {
                const entityName = listboxId.replace('selected-', '');
                const transferFunctionName = 'transfer' + capitalize(entityName) + 'Left';
                if (typeof window[transferFunctionName] === 'function') {
                    window[transferFunctionName]();
                }
            }
        });
    });
};

// Helper function to capitalize first letter
function capitalize(str) {
    return str.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join('');
}

// Reusable transfer function for dual listboxes
window.dualListboxTransfer = function(availableId, selectedId, direction, updateCallback) {
    const availableList = document.getElementById(availableId);
    const selectedList = document.getElementById(selectedId);

    if (!availableList || !selectedList) {
        console.warn(`Dual listbox elements not found: ${availableId}, ${selectedId}`);
        return;
    }

    let sourceList, targetList;

    if (direction === 'right') {
        sourceList = availableList;
        targetList = selectedList;
    } else {
        sourceList = selectedList;
        targetList = availableList;
    }

    const selectedItems = sourceList.querySelectorAll('.listbox-item.selected');

    selectedItems.forEach(item => {
        item.classList.remove('selected');
        targetList.appendChild(item);
    });

    // Call the update callback if provided
    if (typeof updateCallback === 'function') {
        updateCallback();
    }
};

// Get selected values from a listbox
window.getDualListboxValues = function(listboxId) {
    const listbox = document.getElementById(listboxId);
    if (!listbox) {
        console.warn(`Listbox not found: ${listboxId}`);
        return [];
    }

    return Array.from(listbox.querySelectorAll('.listbox-item'))
        .map(item => parseInt(item.dataset.value));
};

console.log('✅ Tenant-safe admin assets loaded');