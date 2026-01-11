<?php

namespace Modules\Studio\App\Parsers;

class HtmlParser
{
    /**
     * HTML içeriğini temizler ve düzeltmeler yapar
     *
     * @param string|null $htmlString
     * @return string
     */
    public function parseAndFixHtml(?string $htmlString): string
    {
        if (!$htmlString || !is_string($htmlString)) {
            return $this->getDefaultContent();
        }
        
        // HTML içeriğini temizle
        $cleanHtml = trim($htmlString);
        
        // Sadece <body></body> gibi boş bir yapı mı kontrol et
        if ($cleanHtml === '<body></body>' || 
            $cleanHtml === '<body> </body>' ||
            strlen($cleanHtml) < 20) {
            return $this->getDefaultContent();
        }
        
        // Body içeriğini al
        $bodyContent = $cleanHtml;
        $bodyMatchRegex = '/<body[^>]*>([\s\S]*?)<\/body>/';
        $bodyMatch = preg_match($bodyMatchRegex, $cleanHtml, $matches);
        
        if ($bodyMatch && isset($matches[1])) {
            $bodyContent = trim($matches[1]);
        }
        
        // Eğer içerik hala boşsa, varsayılan içerik ver
        if (!$bodyContent || strlen($bodyContent) < 10) {
            return $this->getDefaultContent();
        }
        
        return $bodyContent;
    }
    
    /**
     * HTML içeriği kaydetmeye hazırla
     *
     * @param string $htmlContent
     * @return string
     */
    public function prepareContentForSave(string $htmlContent): string
    {
        // Güvenlik için HTML içeriğini sanitize et
        if (config('studio.security.sanitize_html', true)) {
            $htmlContent = $this->sanitizeHtml($htmlContent);
        }
        
        return $htmlContent;
    }
    
    /**
     * HTML içeriğini güvenli hale getirir
     *
     * @param string $html
     * @return string
     */
    protected function sanitizeHtml(string $html): string
    {
        // İzin verilen HTML etiketleri
        $allowedTags = config('studio.security.allowed_tags', 
            '<p><div><span><h1><h2><h3><h4><h5><h6><ul><ol><li><img><a><br><table><tr><td><th><blockquote><b><i><strong><em>');
        
        // strip_tags ile sadece izin verilen etiketleri bırak
        $sanitized = strip_tags($html, $allowedTags);
        
        return $sanitized;
    }
    
    /**
     * Varsayılan içerik
     *
     * @return string
     */
    public function getDefaultContent(): string
    {
        return '
        <div class="container py-4">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Yeni Sayfa</h1>
                    <p class="lead">Bu sayfayı düzenlemek için sol taraftaki bileşenleri kullanabilirsiniz.</p>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Studio Editor ile görsel düzenleme yapabilirsiniz.
                        Düzenlemelerinizi kaydetmek için sağ üstteki Kaydet butonunu kullanın.
                    </div>
                </div>
            </div>
        </div>';
    }
}