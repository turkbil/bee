{{-- Progress Circle Component Kullanım Örnekleri --}}

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progress Circle Component Örnekleri</h3>
                </div>
                <div class="card-body">
                    
                    {{-- Büyük Circle --}}
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <h5>Büyük (Large)</h5>
                            <x-progress-circle 
                                :total-questions="20" 
                                :answered-questions="5" 
                                size="large" />
                            <p class="mt-2">25% tamamlandı (5/20)</p>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <h5>Orta (Medium)</h5>
                            <x-progress-circle 
                                :total-questions="15" 
                                :answered-questions="10" 
                                size="medium" />
                            <p class="mt-2">67% tamamlandı (10/15)</p>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <h5>Küçük (Small)</h5>
                            <x-progress-circle 
                                :total-questions="8" 
                                :answered-questions="8" 
                                size="small" />
                            <p class="mt-2">100% tamamlandı (8/8)</p>
                        </div>
                    </div>
                    
                    {{-- Kullanım Kodu Örnekleri --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Kullanım Kodu Örnekleri</h4>
                                </div>
                                <div class="card-body">
                                    <h6>Büyük Progress Circle:</h6>
                                    <pre><code>&lt;x-progress-circle 
    :total-questions="20" 
    :answered-questions="5" 
    size="large" /&gt;</code></pre>
                                    
                                    <h6 class="mt-3">Orta Progress Circle:</h6>
                                    <pre><code>&lt;x-progress-circle 
    :total-questions="15" 
    :answered-questions="10" 
    size="medium" /&gt;</code></pre>
                                    
                                    <h6 class="mt-3">Küçük Progress Circle:</h6>
                                    <pre><code>&lt;x-progress-circle 
    :total-questions="8" 
    :answered-questions="8" 
    size="small" /&gt;</code></pre>
                                    
                                    <h6 class="mt-3">PHP'den Veri Gönderme:</h6>
                                    <pre><code>// Controller/Livewire Component'te
$totalQuestions = 20;
$answeredQuestions = 5;

// Blade template'te
&lt;x-progress-circle 
    :total-questions="$totalQuestions" 
    :answered-questions="$answeredQuestions" 
    size="large" /&gt;</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>