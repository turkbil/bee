@if(isset($group->layout) && !empty($group->layout) && is_array($group->layout))
    <div class="form-renderer">
        <div class="form-container">
            @if(isset($group->layout['title']))
                <h3 class="form-title mb-4">{{ $group->layout['title'] }}</h3>
            @endif
            <div class="row g-3">

            @if(isset($group->layout['elements']) && is_array($group->layout['elements']))
                @foreach($group->layout['elements'] as $element)
                    @include('settingmanagement::livewire.partials.form-elements.' . $element['type'], [
                        'element' => $element,
                        'values' => $values,
                        'settings' => $settings,
                        'temporaryImages' => $temporaryImages ?? [],
                        'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                        'multipleImagesArrays' => $multipleImagesArrays ?? [],
                        'originalValues' => $originalValues ?? []
                    ])
                @endforeach
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Form yapısı bulunamadı veya geçersiz. Lütfen Form Builder'ı kullanarak form yapısını düzenleyin.
                </div>
            @endif
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Bu grup için henüz form yapısı oluşturulmamış. 
        <a href="{{ route('admin.settingmanagement.form-builder.edit', $group->id) }}" class="alert-link">
            Form Builder'ı kullanarak bir form yapısı oluşturun.
        </a>
    </div>
@endif