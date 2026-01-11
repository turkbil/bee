<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Config;
use Modules\Page\App\Models\Page;
use Modules\Portfolio\App\Models\Portfolio;
use Illuminate\Support\Facades\Log;

class EditorService
{
    /**
     * Editör yapılandırmasını al
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'cdn' => config('studio.editor.cdn', ['enabled' => false, 'version' => '0.21.8']),
            'panels' => config('studio.editor.panels', [
                'blocks' => true,
                'styles' => true,
                'layers' => true,
                'traits' => true,
            ]),
            'devices' => config('studio.editor.devices', [
                'desktop' => [
                    'width' => '',
                    'name' => 'Masaüstü',
                ],
                'tablet' => [
                    'width' => '768px',
                    'widthMedia' => '992px',
                    'name' => 'Tablet',
                ],
                'mobile' => [
                    'width' => '320px',
                    'widthMedia' => '480px',
                    'name' => 'Mobil',
                ],
            ]),
            'canvas' => $this->getCanvasConfig(),
        ];
    }
    
    /**
     * Canvas yapılandırmasını al
     *
     * @return array
     */
    public function getCanvasConfig(): array
    {
        return [
            'styles' => config('studio.editor.canvas.styles', [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            ]),
            'scripts' => config('studio.editor.canvas.scripts', [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js',
            ]),
        ];
    }
    
    /**
     * İçeriği yükle
     *
     * @param string $module
     * @param int $id
     * @param string|null $locale
     * @return array
     */
    public function loadContent(string $module, int $id, string $locale = null): array
    {
        $result = [
            'content' => '',
            'css' => '',
            'js' => '',
            'title' => 'Editör',
        ];
        
        // Locale değerini belirle - URL'den gelen parametre öncelikli
        $targetLocale = $locale ?: app()->getLocale();
        
        // Dinamik modül yükleme sistemi
        try {
            $model = $this->getModuleModel($module, $id);
            if ($model) {
                $result['content'] = $this->safeTranslatableGet($model->body ?? '', '', $targetLocale);
                $result['css'] = $this->safeTranslatableGet($model->css ?? '', '', $targetLocale);
                $result['js'] = $this->safeTranslatableGet($model->js ?? '', '', $targetLocale);
                $result['title'] = $this->safeTranslatableGet($model->title ?? '', ucfirst($module) . ' Düzenleyici', $targetLocale);
                
                // Debug: Log the locale and content check
                Log::info("Studio Editor Content Load", [
                    'module' => $module,
                    'id' => $id,
                    'url_locale' => $locale,
                    'target_locale' => $targetLocale,
                    'content_keys' => is_array($model->body) ? array_keys($model->body) : 'string',
                    'content_preview' => is_array($model->body) ? 
                        substr($model->body[$targetLocale] ?? 'NOT_FOUND', 0, 100) : 
                        substr($model->body ?? 'EMPTY', 0, 100)
                ]);
            }
        } catch (\Exception $e) {
            Log::error($module . ' yüklenirken hata: ' . $e->getMessage());
        }
        
        return $result;
    }
    
    /**
     * Dinamik modül modeli yükle
     *
     * @param string $module
     * @param int $id
     * @return mixed|null
     */
    protected function getModuleModel(string $module, int $id)
    {
        // Modül adına göre model class'ını belirle
        $modelClass = "Modules\\" . ucfirst($module) . "\\App\\Models\\" . ucfirst($module);
        
        // Model class'ı var mı kontrol et
        if (class_exists($modelClass)) {
            return $modelClass::find($id);
        }
        
        // Alternatif isimlendirme denemeleri
        $alternativeNames = [
            "Modules\\" . ucfirst($module) . "\\App\\Models\\" . ucfirst($module),
            "Modules\\" . ucfirst($module) . "\\Models\\" . ucfirst($module),
            "App\\Models\\" . ucfirst($module),
        ];
        
        foreach ($alternativeNames as $className) {
            if (class_exists($className)) {
                return $className::find($id);
            }
        }
        
        return null;
    }

    /**
     * Translatable array field'ını safe string'e dönüştür
     *
     * @param mixed $value
     * @param string $default
     * @param string|null $locale
     * @return string
     */
    protected function safeTranslatableGet($value, string $default = '', string $locale = null): string
    {
        if (is_array($value)) {
            // Locale belirleme: parametre > app locale > tr fallback
            $targetLocale = $locale ?: app()->getLocale();
            
            // İstenen locale'de içerik var mı kontrol et
            $content = $value[$targetLocale] ?? null;
            
            // Eğer istenen locale boş ise, TR'den fallback al
            if (empty($content)) {
                $content = $value['tr'] ?? $value['en'] ?? (string) reset($value) ?: $default;
            }
            
            return $content;
        }
        
        return (string) ($value ?? $default);
    }
    
    /**
     * İçeriği kaydet
     *
     * @param string $module
     * @param int $id
     * @param string $content
     * @param string $css
     * @param string $js
     * @return bool
     */
    public function saveContent(string $module, int $id, string $content, string $css = '', string $js = ''): bool
    {
        // Dinamik modül kaydetme sistemi
        try {
            $model = $this->getModuleModel($module, $id);
            if (!$model) {
                Log::error($module . ' modeli bulunamadı: ' . $id);
                return false;
            }
            
            // İçeriği kaydet
            $model->body = $content;
            $model->css = $css;
            $model->js = $js;
            $result = $model->save();
            
            // ContentSaved eventini tetikle
            if (class_exists('\Modules\Studio\App\Events\ContentSaved')) {
                event(new \Modules\Studio\App\Events\ContentSaved($module, $id, $content));
            }
            
            // Activity log ekle
            if (function_exists('log_activity')) {
                log_activity($model, 'düzenlendi');
            }
            
            // Log mesajı ekle
            Log::info('Log: ' . $this->safeTranslatableGet($model->title ?? $module, ucfirst($module)) . ' - studio ile düzenlendi');
            
            return $result;
        } catch (\Exception $e) {
            Log::error($module . ' kaydedilirken hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Editör JS ayarlarını oluştur
     *
     * @param string $module
     * @param int $id
     * @param array $data
     * @return array
     */
    public function prepareEditorSettings(string $module, int $id, array $data = []): array
    {
        return [
            'module' => $module,
            'moduleId' => $id,
            'content' => $data['content'] ?? '',
            'css' => $data['css'] ?? '',
            'js' => $data['js'] ?? '',
            'elementId' => 'gjs',
            'mode' => 'edit',
        ];
    }
}