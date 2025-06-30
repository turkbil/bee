{{-- Page Module Admin Helper - Scripts Only --}}
@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tab manager with custom key for Page module
    TabManager.init('pageEditActiveTab');
    
    // Initialize multi-language form switcher
    MultiLangFormSwitcher.init();
    
    // Initialize TinyMCE for multi-language editors
    if (typeof tinymce !== 'undefined') {
        TinyMCEMultiLang.initAll();
    }
    
    // Language switcher for underline style
    $('.language-switch-btn').on('click', function(e) {
        e.preventDefault();
        const selectedLang = $(this).data('language');
        
        // Remove active from all siblings
        $('.language-switch-btn').each(function() {
            $(this).removeClass('text-primary').addClass('text-muted');
            $(this).css('border-bottom', '2px solid transparent');
        });
        
        // Add active to clicked button
        $(this).removeClass('text-muted').addClass('text-primary');
        
        // Get primary color from theme builder variable
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color') ||
                             getComputedStyle(document.documentElement).getPropertyValue('--tblr-primary') ||
                             '#066fd1';
        
        $(this).css('border-bottom', `2px solid ${primaryColor}`);
        
        // Switch language content
        MultiLangFormSwitcher.switchLanguage(selectedLang);
    });
});

// Re-initialize on Livewire updates
document.addEventListener('livewire:updated', function() {
    if (typeof tinymce !== 'undefined') {
        TinyMCEMultiLang.initAll();
    }
    
    // Re-bind language switcher events
    $('.language-switch-btn').off('click').on('click', function(e) {
        e.preventDefault();
        const selectedLang = $(this).data('language');
        
        // Remove active from all siblings
        $('.language-switch-btn').each(function() {
            $(this).removeClass('text-primary').addClass('text-muted');
            $(this).css('border-bottom', '2px solid transparent');
        });
        
        // Add active to clicked button
        $(this).removeClass('text-muted').addClass('text-primary');
        
        // Get primary color from theme builder variable
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color') ||
                             getComputedStyle(document.documentElement).getPropertyValue('--tblr-primary') ||
                             '#066fd1';
        
        $(this).css('border-bottom', `2px solid ${primaryColor}`);
        
        // Switch language content
        MultiLangFormSwitcher.switchLanguage(selectedLang);
    });
});
</script>
@endpush