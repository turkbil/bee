<div class="wizard-step-content">
    <h3>Adım {{ $step }}</h3>
    <p>Form içeriği adım {{ $step }} için yüklenecek</p>
    
    @if($step == 1 && $sectors->count() > 0)
        <div class="sectors-grid row">
            @foreach($sectors as $sector)
                <div class="col-md-4 mb-3">
                    <div class="sector-card card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $sector->name }}</h5>
                            <p class="card-text small flex-grow-1">{{ Str::limit($sector->description, 80) }}</p>
                            <div class="text-center mt-auto">
                                <button type="button" class="btn btn-outline-primary btn-sm w-100">Seç</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    <div class="form-footer mt-4">
        <div class="btn-group w-100 justify-content-between d-flex">
            @if($step > 1)
                <a href="{{ route('admin.ai.profile.edit', ['step' => $step - 1]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Önceki
                </a>
            @else
                <div></div>
            @endif
            
            @if($step < 5)
                <a href="{{ route('admin.ai.profile.edit', ['step' => $step + 1]) }}" class="btn btn-primary">
                    Sonraki <i class="fas fa-arrow-right"></i>
                </a>
            @else
                <button type="button" class="btn btn-success">
                    <i class="fas fa-check"></i> Tamamla
                </button>
            @endif
        </div>
    </div>
</div>