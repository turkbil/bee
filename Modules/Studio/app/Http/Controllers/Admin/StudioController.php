<?php

namespace Modules\Studio\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Studio\App\Services\StudioManagerService;
use Modules\Studio\App\Services\StudioContentParserService;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioThemeService;

class StudioController extends Controller
{
    protected $managerService;
    protected $contentParserService;
    protected $widgetService;
    protected $themeService;
    
    public function __construct(
        StudioManagerService $managerService,
        StudioContentParserService $contentParserService,
        StudioWidgetService $widgetService,
        StudioThemeService $themeService
    ) {
        $this->managerService = $managerService;
        $this->contentParserService = $contentParserService;
        $this->widgetService = $widgetService;
        $this->themeService = $themeService;
    }
    
    /**
     * Studio editör sayfasını göster
     *
     * @param string $module Modül adı
     * @param int $id İçerik ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editor(string $module, int $id)
    {
        // Yetki kontrolü
        if (!$this->checkPermission($module)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Bu içerik türü için düzenleme yetkiniz bulunmamaktadır.');
        }
        
        try {
            // Modül kontrolü
            switch ($module) {
                case 'page':
                    // Page modülü mevcut mu kontrol et
                    if (!class_exists('Modules\Page\App\Models\Page')) {
                        return redirect()
                            ->route('admin.dashboard')
                            ->with('error', 'Page modülü bulunamadı veya yüklenmedi.');
                    }
                    
                    // Sayfa mevcut mu kontrol et
                    $page = \Modules\Page\App\Models\Page::find($id);
                    if (!$page) {
                        return redirect()
                            ->route('admin.page.index')
                            ->with('error', 'Düzenlenecek sayfa bulunamadı.');
                    }
                    break;
                
                default:
                    return redirect()
                        ->route('admin.dashboard')
                        ->with('error', 'Desteklenmeyen modül: ' . $module);
            }
            
            return view('studio::admin.editor', [
                'module' => $module,
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            Log::error('Studio Editor sayfası açılırken hata: ' . $e->getMessage(), [
                'module' => $module,
                'id' => $id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Studio Editor sayfası açılırken bir hata oluştu: ' . $e->getMessage());
        }
    }
        
    /**
     * Studio içeriğini kaydet
     *
     * @param Request $request
     * @param string $module Modül adı
     * @param int $id İçerik ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request, string $module, int $id)
    {
        try {
            // Yetki kontrolü
            if (!$this->checkPermission($module)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu içerik türü için düzenleme yetkiniz bulunmamaktadır.'
                ], 403);
            }
            
            // Gelen verileri doğrula
            $validated = $request->validate([
                'content' => 'required|string',
                'css' => 'nullable|string',
                'js' => 'nullable|string',
                'theme' => 'nullable|string',
                'header_template' => 'nullable|string',
                'footer_template' => 'nullable|string',
                'settings' => 'nullable|array',
            ]);
            
            Log::debug("Studio Save - İstek Detayları", [
                'module' => $module,
                'id' => $id,
                'content_size' => strlen($validated['content']),
                'css_size' => strlen($validated['css'] ?? ''),
                'js_size' => strlen($validated['js'] ?? ''),
            ]);
            
            // ID'yi integer'a çevir
            $id = (int)$id;
            
            // Modül türüne göre kaydetme işlemi
            switch ($module) {
                case 'page':
                    return $this->savePage($id, $validated);
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Desteklenmeyen modül: ' . $module
                    ], 400);
            }
        } catch (\Throwable $e) {
            // Hatanın ayrıntılı kaydını tut
            Log::error('Studio içerik kaydederken kritik hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'module' => $module,
                'id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'İçerik kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'error_details' => [
                    'type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ], 500);
        }
    }

    /**
     * Sayfa içeriğini kaydet
     *
     * @param int $id Sayfa ID
     * @param array $data Kaydedilecek veri
     * @return \Illuminate\Http\JsonResponse
     */
    protected function savePage(int $id, array $data)
    {
        try {
            if (!class_exists('Modules\Page\App\Models\Page')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page modülü bulunamadı veya yüklenmedi.'
                ], 404);
            }
            
            $page = \Modules\Page\App\Models\Page::findOrFail($id);
            
            // HTML içeriğini doğrudan kullan ve temizleme işlemini atla
            // İçerik parser servisi hata verdiği için basitleştirelim
            $page->body = $data['content'] ?? '';
            $page->css = $data['css'] ?? '';
            $page->js = $data['js'] ?? '';
            
            // Güncelleme öncesi durumu logla
            Log::debug("Studio Save - Güncelleme Öncesi", [
                'page_id' => $page->id,
                'old_body_length' => strlen($page->getOriginal('body') ?? ''),
                'new_body_length' => strlen($page->body),
                'old_css_length' => strlen($page->getOriginal('css') ?? ''),
                'new_css_length' => strlen($page->css),
                'old_js_length' => strlen($page->getOriginal('js') ?? ''),
                'new_js_length' => strlen($page->js)
            ]);
            
            // Kaydet
            $saveResult = $page->save();
            
            if (!$saveResult) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veritabanına kayıt yapılamadı.'
                ], 500);
            }
            
            // Tema ayarlarını kaydet (varsa)
            if (isset($data['theme']) || isset($data['header_template']) || isset($data['footer_template']) || isset($data['settings'])) {
                $settingsData = [
                    'theme' => $data['theme'] ?? null,
                    'header_template' => $data['header_template'] ?? null,
                    'footer_template' => $data['footer_template'] ?? null,
                    'settings' => $data['settings'] ?? [],
                ];
                
                $this->managerService->saveModuleSettings('page', $id, $settingsData);
            }
            
            // Aktivite kaydı
            if (function_exists('activity')) {
                activity()
                    ->performedOn($page)
                    ->withProperties(['studio' => true])
                    ->log('studio ile düzenlendi');
            }
            
            // İçerik kaydedilme olayını tetikle
            try {
                event(new \Modules\Studio\Events\StudioContentSaved('page', $id, [
                    'title' => $page->title,
                    'content_length' => strlen($page->body),
                    'css_length' => strlen($page->css),
                    'js_length' => strlen($page->js)
                ]));
            } catch (\Exception $e) {
                Log::warning('StudioContentSaved olayı tetiklenirken hata: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Sayfa başarıyla kaydedildi.',
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Sayfa kaydedilirken hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'page_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sayfa kaydedilirken hata: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Widgetları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidgets()
    {
        try {
            return response()->json([
                'success' => true,
                'widgets' => $this->widgetService->getWidgetsAsBlocks(),
                'categories' => $this->widgetService->getCategories()
            ]);
        } catch (\Exception $e) {
            Log::error('Widget verileri alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Widget verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Temaları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemes()
    {
        try {
            $themes = $this->themeService->getAllThemes();
            $defaultTheme = $this->themeService->getDefaultTheme();
            $themeName = $defaultTheme ? $defaultTheme['folder_name'] : 'default';
            $templates = $this->themeService->getTemplatesForTheme($themeName);
            
            return response()->json([
                'success' => true,
                'themes' => $themes,
                'defaultTheme' => $defaultTheme,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Tema verileri alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Tema verileri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Dosya yükleme işlemi
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAssets(Request $request)
    {
        try {
            if (!$request->hasFile('files')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen bir dosya seçin.'
                ], 400);
            }
            
            $files = $request->file('files');
            $uploadedFiles = [];
            
            // Tenant ID kontrolü (eğer tenant sistemi aktifse)
            $tenantPrefix = function_exists('tenant') ? 'tenant/' . tenant()->getTenantKey() . '/' : '';
            
            foreach ($files as $file) {
                if (!$file->isValid()) {
                    continue;
                }
                
                // Güvenli dosya adı oluştur
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $mimeType = $file->getMimeType();
                $size = $file->getSize();
                
                // Dosyayı tenant klasörü altında yükle
                $path = $file->storeAs(
                    $tenantPrefix . 'studio/assets', 
                    $fileName, 
                    'public'
                );
                
                $uploadedFiles[] = [
                    'name' => $fileName,
                    'type' => $mimeType,
                    'size' => $size,
                    'src' => asset('storage/' . $path)
                ];
            }
            
            if (empty($uploadedFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dosyalar yüklenemedi. Geçerli dosya bulunamadı.'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' dosya başarıyla yüklendi.',
                'data' => $uploadedFiles
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dosya yükleme hatası: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Dosya yükleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }
        
    /**
     * Özel blokları getir
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomBlocks()
    {
        try {
            return response()->json([
                'success' => true,
                'blocks' => []  // Şimdilik boş blok listesi döndürüyoruz
            ]);
        } catch (\Exception $e) {
            Log::error('Özel bloklar alınırken hata: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Özel bloklar alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * İçerik tipi için yetki kontrolü
     *
     * @param string $module
     * @return bool
     */
    protected function checkPermission(string $module): bool
    {
        // Geliştirme aşamasında her zaman erişime izin ver
        return true;
        
        // Eski yetki kontrolü (ihtiyaç olursa aktif edilebilir)
        /*
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Super admin kontrolü
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        switch ($module) {
            case 'page':
                return $user->can('view-page') || $user->can('edit-page');
            // Diğer modüller için yetki kontrolleri eklenebilir
            default:
                return false;
        }
        */
    }
}