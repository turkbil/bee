{{--
    UNIVERSAL TAB SYSTEM COMPONENT VIEW
    Pattern: A1 CMS Universal System

    Bu component sadece tab state management yapar, UI render etmez
    Tab UI için mevcut tab-system component'ini kullanır
--}}

<div>
    {{-- Hidden state keeper - Tab state management için --}}
    <input type="hidden" id="tab-module" value="{{ $module }}">
    <input type="hidden" id="tab-active" value="{{ $activeTab }}">

    {{-- JavaScript variables --}}
    @push('scripts')
    <script>
        // Universal Tab System State
        window.tabModule = '{{ $module }}';
        window.tabActiveTab = '{{ $activeTab }}';
        window.tabConfig = @json($tabConfig);
        window.tabCompletionStatus = @json($tabCompletionStatus);
        window.tabJsConfig = @json($jsConfig);

        console.log('📑 Universal Tab System initialized', {
            module: window.tabModule,
            activeTab: window.tabActiveTab,
            tabCount: window.tabConfig.length,
            completionStatus: window.tabCompletionStatus
        });
    </script>
    @endpush
</div>