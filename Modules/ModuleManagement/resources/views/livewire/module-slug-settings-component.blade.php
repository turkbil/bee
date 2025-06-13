@include('modulemanagement::helper')

<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Sol Kolon - Başlık ve Açıklama -->
            <div class="col">
                <h3 class="card-title mb-2">
                    <i class="fas fa-link me-2"></i>
                    {{ $moduleDisplayName }} URL Ayarları
                </h3>
                <div class="text-muted">
                    Web sitenizin URL yapısını özelleştirin. Değişiklikler anında kaydedilir.
                </div>
            </div>
            <!-- Orta Kolon - Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="updateSlug, resetSlug, resetAllSlugs"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Kolon - Tümünü Sıfırla Butonu -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end">
                    <button wire:click="resetAllSlugs" class="btn btn-outline-danger">
                        <i class="fas fa-undo me-1"></i>
                        Tümünü Sıfırla
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-12">
                        @if(empty($defaultSlugs))
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <div>
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">Yapılandırma Bulunamadı</h4>
                                    Bu modül için slug yapılandırması tanımlanmamış.
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="row g-3">
                            @foreach($defaultSlugs as $key => $defaultValue)
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label class="form-label mb-1">
                                                        <strong>{{ ucfirst($key) }} Sayfası URL'i</strong>
                                                    </label>
                                                    <div class="text-muted small">
                                                        @switch($key)
                                                            @case('index')
                                                                Liste sayfası için kullanılacak URL
                                                                @break
                                                            @case('show')
                                                                Detay sayfaları için kullanılacak URL öneki
                                                                @break
                                                            @case('category')
                                                                Kategori sayfaları için kullanılacak URL öneki
                                                                @break
                                                            @default
                                                                {{ ucfirst($key) }} sayfaları için kullanılacak URL
                                                        @endswitch
                                                    </div>
                                                </div>
                                                
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-globe text-muted"></i>
                                                    </span>
                                                    <span class="input-group-text text-muted">/</span>
                                                    <input 
                                                        type="text" 
                                                        class="form-control @error('slugs.'.$key) is-invalid @enderror"
                                                        value="{{ $slugs[$key] ?? $defaultValue }}"
                                                        wire:blur="updateSlug('{{ $key }}', $event.target.value)"
                                                        wire:keydown.enter="updateSlug('{{ $key }}', $event.target.value)"
                                                        placeholder="{{ $defaultValue }}"
                                                    >
                                                    @if($key === 'show' || $key === 'category')
                                                    <span class="input-group-text text-muted">/{slug}</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <small class="text-muted">
                                                            <i class="fas fa-eye me-1"></i>
                                                            Önizleme: 
                                                            <code class="text-primary">
                                                                /{{ $slugs[$key] ?? $defaultValue }}{{ in_array($key, ['show', 'category']) ? '/ornek-sayfa' : '' }}
                                                            </code>
                                                        </small>
                                                        
                                                        @if(($slugs[$key] ?? $defaultValue) !== $defaultValue)
                                                        <button 
                                                            wire:click="resetSlug('{{ $key }}')" 
                                                            class="btn btn-sm btn-outline-secondary"
                                                            title="Varsayılana döndür: {{ $defaultValue }}"
                                                        >
                                                            <i class="fas fa-undo fa-xs"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-info-circle me-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">URL Özelleştirme Hakkında</h4>
                                        <ul class="mb-0">
                                            <li>URL değişiklikleri anında kaydedilir ve web sitenizde görünür</li>
                                            <li>Özel URL yapınızı kullanıcı dostu hale getirebilirsiniz</li>
                                            <li>Geçersiz karakterler otomatik olarak temizlenir</li>
                                            <li>Boş bırakılan alanlar varsayılan değeri kullanır</li>
                                            <li>SEO uyumlu URL'ler oluşturarak arama motoru sıralamanızı iyileştirebilirsiniz</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($defaultSlugs))
                        <div class="mt-4">
                            <div class="alert alert-light">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-code me-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Geliştiriciler için</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Template'lerde kullanım örneği:</label>
                                            <div class="bg-dark text-light p-3 rounded">
                                                <code class="text-success">
                                                    &lt;!-- Liste sayfası linki --&gt;<br>
                                                    &lt;a href="&#123;&#123; href('{{ strtolower($moduleName) }}', 'index') &#125;&#125;"&gt;{{ ucfirst($moduleName) }} Listesi&lt;/a&gt;<br><br>
                                                    &lt;!-- Detay sayfası linki --&gt;<br>
                                                    &lt;a href="&#123;&#123; href('{{ strtolower($moduleName) }}', 'show', $item-&gt;slug) &#125;&#125;"&gt;Detay Görüntüle&lt;/a&gt;
                                                </code>
                                            </div>
                                        </div>
                                        
                                        <div class="small text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            href() fonksiyonu özelleştirdiğiniz URL'leri otomatik olarak kullanır.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    // Input alanlarında sadece URL uyumlu karakterlere izin ver
    document.addEventListener('input', function(e) {
        if (e.target.type === 'text' && e.target.closest('.input-group')) {
            let value = e.target.value;
            // Türkçe karakterleri dönüştür ve sadece URL uyumlu karakterlere izin ver
            value = value
                .toLowerCase()
                .replace(/[çÇ]/g, 'c')
                .replace(/[ğĞ]/g, 'g')
                .replace(/[ıİ]/g, 'i')
                .replace(/[öÖ]/g, 'o')
                .replace(/[şŞ]/g, 's')
                .replace(/[üÜ]/g, 'u')
                .replace(/[^a-z0-9\-_]/g, '');
            
            if (value !== e.target.value) {
                e.target.value = value;
            }
        }
    });
});
</script>