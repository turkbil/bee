<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\LanguageManagement\app\Services\SystemLanguageService;
use Modules\LanguageManagement\app\Services\SiteLanguageService;
use Modules\LanguageManagement\app\Models\SystemLanguage;
use Modules\LanguageManagement\app\Models\SiteLanguage;

#[Layout('admin.layout')]
class LanguageSettingsComponent extends Component
{
    public $systemLanguagesCount = 0;
    public $siteLanguagesCount = 0;
    public $currentAdminLanguage = 'tr';
    public $currentSiteLanguage = 'tr';
    public $recentSystemLanguages = [];
    public $recentSiteLanguages = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $systemLanguageService = app(SystemLanguageService::class);
        $siteLanguageService = app(SiteLanguageService::class);

        // Sistem dilleri istatistikleri
        $this->systemLanguagesCount = SystemLanguage::where('is_active', true)->count();
        $this->currentAdminLanguage = $systemLanguageService->getTenantAdminLanguage() ?: 'tr';
        $this->recentSystemLanguages = SystemLanguage::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Site dilleri istatistikleri
        $this->siteLanguagesCount = SiteLanguage::where('is_active', true)->count();
        $this->currentSiteLanguage = $siteLanguageService->getTenantDefaultSiteLanguage() ?: 'tr';
        $this->recentSiteLanguages = SiteLanguage::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('languagemanagement::admin.livewire.language-settings-component');
    }
}