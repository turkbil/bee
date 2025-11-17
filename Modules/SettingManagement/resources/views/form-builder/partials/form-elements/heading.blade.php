<div class="col-12">
    @php
        $headingLevel = isset($element['properties']['size']) ? $element['properties']['size'] : 'h3';
        $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';

        // Auto-load content from Setting model if setting_id exists
        $content = 'Başlık'; // Default
        if (isset($element['properties']['setting_id'])) {
            $setting = \Modules\SettingManagement\App\Models\Setting::find($element['properties']['setting_id']);
            if ($setting && $setting->default_value) {
                $content = $setting->default_value;
            }
        } elseif (isset($element['properties']['content'])) {
            $content = $element['properties']['content'];
        }
    @endphp
    
    @switch($headingLevel)
        @case('h1')
            <{{ $headingLevel }} class="page-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h2')
            <{{ $headingLevel }} class="section-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h3')
            <{{ $headingLevel }} class="card-title text-{{ $align }} mb-0">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h4')
            <{{ $headingLevel }} class="section-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h5')
            <{{ $headingLevel }} class="modal-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @default
            <{{ $headingLevel }} class="text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
    @endswitch
</div>