// AI Cache Manager - BACKUP VERSION
// Bu kod 2025-01-29 tarihinde kaldırıldı - Sadece AI önerileri bırakıldı

window.aiSeoManager = {

    checkAnalysisStatus: function() {
        const hasAnalysis = {{ isset($hasAiAnalysis) && $hasAiAnalysis ? 'true' : 'false' }};
        console.log('🤖 AI Analysis Status:', hasAnalysis);
        return hasAnalysis;
    },

    triggerAnalysis: function() {
        console.log('🚀 AI SEO analizi başlatılıyor...');

        // Loading state göster
        document.querySelector('.ai-waiting-state')?.classList.add('d-none');
        document.querySelector('.ai-analysis-loading')?.style.setProperty('display', 'block');

        // AI API çağrısını yap
        this.performAiAnalysis();
    },

    performAiAnalysis: async function() {
        try {
            // AI API endpoint'e istek gönder
            const response = await fetch('/admin/seo/ai/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    page_id: {{ $pageId ?? 'null' }},
                    language: '{{ $currentLanguage ?? "tr" }}',
                    feature_slug: 'comprehensive-seo-audit',
                    form_content: {
                        // Minimal form content for validation
                        page_id: {{ $pageId ?? 'null' }},
                        current_language: '{{ $currentLanguage ?? "tr" }}'
                    }
                })
            });

            const result = await response.json();

            if (result.success) {
                // Sayfayı yenile - AI sonuçları veritabanına kaydedildi
                window.location.reload();
            } else {
                throw new Error(result.message || 'AI analizi başarısız');
            }

        } catch (error) {
            console.error('❌ AI analizi hatası:', error);
            this.showError(error.message);
        }
    },

    showError: function(message) {
        document.querySelector('.ai-analysis-loading')?.style.setProperty('display', 'none');
        const errorDiv = document.querySelector('.ai-analysis-error');
        if (errorDiv) {
            errorDiv.style.display = 'block';
            errorDiv.querySelector('p').textContent = message;
        }
    }
};

// AI butonlarına event listener ekle
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ai-seo-comprehensive-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            window.aiSeoManager.triggerAnalysis();
        });
    });

    document.querySelectorAll('.ai-retry-analysis').forEach(btn => {
        btn.addEventListener('click', function() {
            window.aiSeoManager.triggerAnalysis();
        });
    });
});