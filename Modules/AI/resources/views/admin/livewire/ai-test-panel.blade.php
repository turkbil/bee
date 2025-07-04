<div>
    <style>
        .feature-box {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .feature-box.selected {
            border-color: #4c6ef5;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .feature-box.selected .text-muted {
            color: rgba(255,255,255,0.8) !important;
        }
        .token-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .result-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .processing-animation {
            display: inline-block;
            width: 80px;
            height: 80px;
        }
        .processing-animation:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 8px;
            border-radius: 50%;
            border: 6px solid #667eea;
            border-color: #667eea transparent #667eea transparent;
            animation: processing-animation 1.2s linear infinite;
        }
        @keyframes processing-animation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    {{-- Token Durumu --}}
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <div class="token-badge">
                <h2 class="mb-3">Token Bakiyeniz</h2>
                @if($remainingTokens > 0)
                    <div style="font-size: 60px; font-weight: 700;">
                        {{ number_format($remainingTokens) }}
                    </div>
                    <p class="mb-0 mt-2">AI özelliklerini kullanmaya hazırsınız!</p>
                @else
                    <div style="font-size: 60px; font-weight: 700;">0</div>
                    <p class="mb-3">Token satın almanız gerekiyor.</p>
                    <a href="{{ route('admin.ai.tokens.packages') }}" class="btn btn-light btn-lg">
                        <i class="ti ti-shopping-cart"></i> Token Satın Al
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Hata Mesajları --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">Hata!</h4>
        @foreach($errors->all() as $error)
            <p class="mb-0">• {{ $error }}</p>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- İşlem Alanı --}}
    @if($selectedFeature)
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-3">
                <i class="{{ $features[$selectedFeature]['icon'] ?? 'ti-sparkles' }}"></i> 
                {{ $selectedFeature }}
            </h4>
            
            <div class="mb-4">
                <label class="form-label fs-5">Ne yapmak istiyorsunuz?</label>
                <textarea 
                    wire:model="inputText" 
                    class="form-control form-control-lg" 
                    rows="4"
                    placeholder="Örnek: {{ $features[$selectedFeature]['example'] }}"
                    @if($isProcessing) disabled @endif
                ></textarea>
            </div>
            
            <div class="d-flex gap-3">
                <button 
                    wire:click="testAI" 
                    class="btn btn-primary btn-lg px-5"
                    @if($isProcessing || !$inputText) disabled @endif
                >
                    @if($isProcessing)
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        İşleniyor...
                    @else
                        <i class="ti ti-sparkles"></i> AI ile Test Et
                    @endif
                </button>
                
                <button 
                    wire:click="clearAll" 
                    class="btn btn-outline-secondary btn-lg"
                >
                    <i class="ti ti-x"></i> İptal
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- İşlem Animasyonu --}}
    @if($isProcessing)
    <div class="text-center my-5">
        <div class="processing-animation"></div>
        <h4 class="mt-3">AI düşünüyor...</h4>
        <p class="text-muted">Lütfen bekleyin, bu birkaç saniye sürebilir.</p>
    </div>
    @endif

    {{-- Sonuç --}}
    @if($showResult && $result)
    <div class="result-box mb-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h4 class="mb-0">
                <i class="ti ti-check-circle text-success"></i> AI Yanıtı
            </h4>
            <span class="badge bg-primary">{{ $tokensUsed }} token kullanıldı</span>
        </div>
        
        <div class="mb-4">
            <h6 class="text-muted">Sorunuz:</h6>
            <div class="bg-white p-3 rounded border">
                {{ $inputText }}
            </div>
        </div>
        
        <div>
            <h6 class="text-muted">AI Yanıtı:</h6>
            <div class="bg-white p-4 rounded border" style="white-space: pre-wrap; line-height: 1.6;">{{ $result }}</div>
        </div>
        
        <div class="mt-3 text-end">
            <button wire:click="clearAll" class="btn btn-secondary">
                <i class="ti ti-refresh"></i> Yeni Test
            </button>
        </div>
    </div>
    @endif

    {{-- Özellik Listesi --}}
    @if(!$selectedFeature)
    <h3 class="mb-4 text-center">Ne yapmak istersiniz?</h3>
    <div class="row">
        @foreach($features as $name => $info)
        <div class="col-md-6">
            <div 
                class="feature-box"
                wire:click="selectFeature('{{ $name }}')"
            >
                <h4 class="mb-2">
                    <i class="{{ $info['icon'] ?? 'ti-sparkles' }}"></i> 
                    {{ $name }}
                </h4>
                <p class="text-muted mb-0">{{ $info['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- JavaScript Event Handlers --}}
    <script>
        window.addEventListener('ai-test-success', event => {
            // Success notification veya başka bir işlem
            console.log('AI test completed:', event.detail);
        });
    </script>
</div>