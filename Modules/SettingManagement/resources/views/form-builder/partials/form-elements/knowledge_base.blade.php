@php
    $fieldName = $element['properties']['name'] ?? 'ai_knowledge_base';
    $fieldLabel = $element['properties']['label'] ?? 'AI Bilgi Bankası';
    $helpText = $element['properties']['help_text'] ?? 'Sık sorulan sorular ve cevaplarını ekleyin. Her satırda "Soru: ... | Cevap: ..." formatında yazın.';
    $width = $element['properties']['width'] ?? 12;

    // Mevcut JSON değerini text formatına çevir
    $fieldValue = $values[$fieldName] ?? '{}';

    if(is_string($fieldValue)) {
        $jsonData = json_decode($fieldValue, true);
    } else {
        $jsonData = $fieldValue;
    }

    $textValue = '';
    if(is_array($jsonData) && !empty($jsonData)) {
        foreach($jsonData as $question => $answer) {
            $textValue .= "Soru: {$question}\n";
            $textValue .= "Cevap: {$answer}\n\n";
        }
    }
@endphp

<div class="col-{{ $width }}">
    <div class="mb-3">
        <label for="{{ $fieldName }}_text" class="form-label">
            {{ $fieldLabel }}
        </label>

        <div class="alert alert-info mb-3">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Nasıl Kullanılır:</strong><br>
            Her soru-cevap çifti için aşağıdaki formatı kullanın:
            <pre class="mt-2 mb-0" style="background: rgba(255,255,255,0.5); padding: 10px; border-radius: 4px;">Soru: Çalışma saatleriniz nedir?
Cevap: Hafta içi 09:00-18:00 arası hizmet veriyoruz.

Soru: Kargo ücretsiz mi?
Cevap: 500 TL üzeri alışverişlerde kargo ücretsizdir.</pre>
        </div>

        <textarea
            id="{{ $fieldName }}_text"
            wire:model.defer="values.{{ $fieldName }}_text"
            class="form-control font-monospace @error('values.' . $fieldName) is-invalid @enderror"
            rows="15"
            style="font-size: 0.875rem;"
            placeholder="Soru: İlk sorum?\nCevap: İlk cevabım.\n\nSoru: İkinci sorum?\nCevap: İkinci cevabım.">{{ $textValue }}</textarea>

        @error('values.' . $fieldName)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text mt-2">
            <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
        </div>

        <!-- JSON formatına çevirme için hidden input -->
        <input type="hidden" wire:model.defer="values.{{ $fieldName }}" id="{{ $fieldName }}">
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('{{ $fieldName }}_text');
    const hiddenInput = document.getElementById('{{ $fieldName }}');

    if(textarea && hiddenInput) {
        // Text değiştiğinde JSON'a çevir
        textarea.addEventListener('blur', function() {
            const text = this.value;
            const lines = text.split('\n');
            const jsonData = {};

            let currentQuestion = '';
            let currentAnswer = '';
            let mode = null;

            for(let line of lines) {
                line = line.trim();

                if(line.startsWith('Soru:')) {
                    // Önceki soru-cevap çiftini kaydet
                    if(currentQuestion && currentAnswer) {
                        jsonData[currentQuestion] = currentAnswer;
                    }

                    currentQuestion = line.substring(5).trim();
                    currentAnswer = '';
                    mode = 'question';
                } else if(line.startsWith('Cevap:')) {
                    currentAnswer = line.substring(6).trim();
                    mode = 'answer';
                } else if(line && mode === 'answer') {
                    // Cevap devamı
                    currentAnswer += ' ' + line;
                }
            }

            // Son soru-cevap çiftini kaydet
            if(currentQuestion && currentAnswer) {
                jsonData[currentQuestion] = currentAnswer;
            }

            // JSON'a çevir
            hiddenInput.value = JSON.stringify(jsonData);

            // Livewire'a bildir
            @this.set('values.{{ $fieldName }}', JSON.stringify(jsonData));
        });
    }
});
</script>
@endpush
