/**
 * TENANT-SAFE ADMIN PANEL ASSETS
 * Admin panel için tenant bağımsız asset yönetimi
 */

// Import necessary libraries
import axios from 'axios';

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

console.log('✅ Tenant-safe admin assets loaded');