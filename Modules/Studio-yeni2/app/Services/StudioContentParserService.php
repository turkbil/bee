<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Log;

class StudioContentParserService
{
    /**
     * HTML içeriğini temizle ve düzelt
     *
     * @param string|null $html HTML içeriği
     * @return string
     */
    public function parseHtml(?string $html): string
    {
        if (!$html) {
            return $this->getDefaultHtml();
        }
        
        try {
            // HTML içeriğini temizle
            $html = $this->sanitizeHtml($html);
            
            // Boş içerik kontrolü
            if (empty($html) || $html === '<body></body>' || strlen($html) < 20) {
                return $this->getDefaultHtml();
            }
            
            // Body içeriğini al
            $bodyContent = $this->extractBodyContent($html);
            
            // Temizlenmiş içeriği döndür
            return $bodyContent;
        } catch (\Exception $e) {
            Log::error('HTML ayrıştırma hatası: ' . $e->getMessage());
            return $this->getDefaultHtml();
        }
    }
    
    /**
     * HTML içeriğini kaydetmek için hazırla
     *
     * @param string $html HTML içeriği
     * @param string|null $css CSS içeriği
     * @param string|null $js JavaScript içeriği
     * @return array
     */
    public function prepareContentForSave(string $html, ?string $css = null, ?string $js = null): array
    {
        try {
            // HTML içeriğini temizle
            $html = $this->sanitizeHtml($html);
            
            // Boş içerik kontrolü
            if (empty($html) || $html === '<body></body>' || strlen($html) < 20) {
                $html = $this->getDefaultHtml();
            }
            
            // CSS ve JS içeriklerini temizle
            $css = $this->sanitizeCss($css);
            $js = $this->sanitizeJs($js);
            
            return [
                'html' => $html,
                'css' => $css,
                'js' => $js
            ];
        } catch (\Exception $e) {
            Log::error('İçerik kaydetme hatası: ' . $e->getMessage());
            
            return [
                'html' => $this->getDefaultHtml(),
                'css' => '',
                'js' => ''
            ];
        }
    }
    
    /**
     * HTML içeriğinden body kısmını çıkar
     *
     * @param string $html HTML içeriği
     * @return string
     */
    protected function extractBodyContent(string $html): string
    {
        $bodyMatchRegex = '/<body[^>]*>([\s\S]*?)<\/body>/i';
        
        if (preg_match($bodyMatchRegex, $html, $matches)) {
            return trim($matches[1]);
        }
        
        return $html;
    }
    
    /**
     * HTML içeriğini temizle
     *
     * @param string|null $html HTML içeriği
     * @return string
     */
    protected function sanitizeHtml(?string $html): string
    {
        if (!$html) {
            return '';
        }
        
        // Tehlikeli etiketleri temizle
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        
        // URL'leri temizle
        $html = preg_replace('/\bon\w+\s*=\s*(["\']).*?\1/i', '', $html);
        $html = preg_replace('/\bhref\s*=\s*(["\'])javascript:.*?\1/i', 'href="javascript:void(0)"', $html);
        
        return $html;
    }
    
    /**
     * CSS içeriğini temizle
     *
     * @param string|null $css CSS içeriği
     * @return string
     */
    protected function sanitizeCss(?string $css): string
    {
        if (!$css) {
            return '';
        }
        
        // Tehlikeli CSS özelliklerini temizle
        $css = preg_replace('/expression\s*\(.*?\)/i', '', $css);
        $css = preg_replace('/behavior\s*:.*?;/i', '', $css);
        $css = preg_replace('/-moz-binding\s*:.*?;/i', '', $css);
        
        return $css;
    }
    
    /**
     * JavaScript içeriğini temizle
     *
     * @param string|null $js JavaScript içeriği
     * @return string
     */
    protected function sanitizeJs(?string $js): string
    {
        if (!$js) {
            return '';
        }
        
        // Tehlikeli JS fonksiyonlarını temizle
        // Not: Bu basit bir temizleme olup, JS'in tam olarak temizlenmesi için daha kapsamlı çözümler gerekir
        $js = preg_replace('/eval\s*\(.*?\)/i', '', $js);
        
        return $js;
    }
    
    /**
     * Varsayılan HTML içeriği
     *
     * @return string
     */
    public function getDefaultHtml(): string
    {
        return '<div class="container py-5">
            <div class="row">
                <div class="col-md-12">
                    <h2>Hoş Geldiniz</h2>
                    <p>Studio Editör ile sayfanızı düzenlemeye başlayabilirsiniz. Sol taraftaki bileşenleri sürükleyip bırakarak içerik ekleyebilirsiniz.</p>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Düzenlemelerinizi kaydetmek için sağ üstteki <strong>Kaydet</strong> butonunu kullanın.
                    </div>
                </div>
            </div>
        </div>';
    }
    
    /**
     * Widget içeriklerini HTML'e çevir
     *
     * @param array $widgets Widgetlar
     * @return string
     */
    public function renderWidgetsToHtml(array $widgets): string
    {
        $html = '';
        
        foreach ($widgets as $widget) {
            $html .= $this->renderWidgetToHtml($widget);
        }
        
        return $html;
    }
    
    /**
     * Widget içeriğini HTML'e çevir
     *
     * @param array $widget Widget
     * @return string
     */
    protected function renderWidgetToHtml(array $widget): string
    {
        $html = $widget['content_html'] ?? '';
        
        // Widget ID ve sınıfı ekle
        $html = '<div class="studio-widget" data-widget-id="' . ($widget['id'] ?? 0) . '">' . $html . '</div>';
        
        return $html;
    }
}