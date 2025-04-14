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
            // Boş içerik kontrolü
            if (empty($html) || $html === '<body></body>' || strlen($html) < 20) {
                return $this->getDefaultHtml();
            }
            
            // İçeriği doğrudan döndür, temizleme yapmadan
            return $html;
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
            // Şablonları geri yükle ancak diğer sanitize işlemlerini atla
            $html = $this->restoreTemplates($html);
            
            // Boş içerik kontrolü
            if (empty($html) || $html === '<body></body>' || strlen($html) < 20) {
                $html = $this->getDefaultHtml();
            }
            
            return [
                'html' => $html,
                'css' => $css ?? '',
                'js' => $js ?? ''
            ];
        } catch (\Throwable $e) {
            Log::error('İçerik kaydetme hatası: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'html' => $this->getDefaultHtml(),
                'css' => '',
                'js' => ''
            ];
        }
    }

    /**
     * HTML içeriğini ön işlemden geçir
     * 
     * @param string $html HTML içeriği
     * @return string İşlenmiş HTML
     */
    protected function preprocessHtml(string $html): string
    {
        // Tehlikeli etiketleri temizle
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        
        // URL'leri temizle
        $html = preg_replace('/\bon\w+\s*=\s*(["\']).*?\1/i', '', $html);
        $html = preg_replace('/\bhref\s*=\s*(["\'])javascript:.*?\1/i', 'href="javascript:void(0)"', $html);
        
        // Mustache/Handlebars şablonlarını işle
        $html = $this->hideTemplates($html);
        
        return $html;
    }

    /**
     * Şablon ifadelerini gizle (HTML editör tarafından işlenmemeleri için)
     * 
     * @param string $html HTML içeriği
     * @return string İşlenmiş HTML
     */
    protected function hideTemplates(string $html): string
    {
        // Tüm {{...}} ifadelerini koru
        $html = preg_replace_callback('/\{\{([^\}]*)\}\}/m', function($matches) {
            $content = $matches[1];
            $safeContent = htmlspecialchars($content);
            return '<span class="studio-template" data-tpl="' . base64_encode($matches[0]) . '">TEMPLATE:' . $safeContent . '</span>';
        }, $html);
        
        // Tüm {{{...}}} ifadelerini koru
        $html = preg_replace_callback('/\{\{\{([^\}]*)\}\}\}/m', function($matches) {
            $content = $matches[1];
            $safeContent = htmlspecialchars($content);
            return '<span class="studio-raw-template" data-tpl="' . base64_encode($matches[0]) . '">RAW_TEMPLATE:' . $safeContent . '</span>';
        }, $html);
        
        return $html;
    }
    
    /**
     * Gizlenen şablon ifadelerini geri yükle
     * 
     * @param string $html HTML içeriği
     * @return string İşlenmiş HTML
     */
    protected function restoreTemplates(string $html): string
    {
        // Span etiketlerini orijinal şablonlarla değiştir
        $html = preg_replace_callback('/<span class="studio-template" data-tpl="([^"]+)">[^<]*<\/span>/m', function($matches) {
            return base64_decode($matches[1]);
        }, $html);
        
        $html = preg_replace_callback('/<span class="studio-raw-template" data-tpl="([^"]+)">[^<]*<\/span>/m', function($matches) {
            return base64_decode($matches[1]);
        }, $html);
        
        // Herhangi bir veri özniteliğinden kaçış yapmış şablonları düzelt
        $html = preg_replace('/data-tpl="([^"]+)"/m', 'data-tpl="{{TEMPLATE}}"', $html);
        
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
        
        // HTML'i doğrudan döndür, temizleme işlemini atla
        return $html;
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
        
        // CSS'i doğrudan döndür, temizleme işlemini atla
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
        
        // JS'i doğrudan döndür, temizleme işlemini atla
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