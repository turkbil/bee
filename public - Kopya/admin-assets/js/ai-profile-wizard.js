$(document).ready(function() {
    
    // Initialize Choices.js for select elements
    const selectElements = document.querySelectorAll('select:not([multiple])');
    selectElements.forEach(element => {
        if (!element.classList.contains('choices__input')) {
            new Choices(element, {
                searchEnabled: false,
                itemSelectText: '',
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Seçiniz...'
            });
        }
    });
    
    // Smooth scroll animations for step changes
    Livewire.on('step-changed', function() {
        $('html, body').animate({
            scrollTop: $('.wizard-card').offset().top - 100
        }, 500);
    });
    
    // Form validation feedback
    Livewire.on('validation-error', function() {
        // Scroll to first error
        const firstError = $('.invalid-feedback:visible').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 150
            }, 500);
        }
        
        // Show toast notification
        // You can add toast notification here
    });
    
    // Step circle click animations
    $('.step-item').on('click', function() {
        $(this).addClass('pulse-animation');
        setTimeout(() => {
            $(this).removeClass('pulse-animation');
        }, 300);
    });
    
    // Digital effects
    setInterval(function() {
        $('.ai-brain-icon').addClass('glow-pulse');
        setTimeout(() => {
            $('.ai-brain-icon').removeClass('glow-pulse');
        }, 1000);
    }, 3000);
    
});