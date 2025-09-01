@extends('admin.layout')

@section('title', 'Modal Test 1 - Modern Premium')

@section('content')
@php
    View::share('pretitle', 'Modal Test 1 - Modern Premium Design');
@endphp

<div>
    {{-- Page Helper - Birebir aynı --}}
    @include('page::admin.helper')
    
    <form method="post">
        <div class="card">
            
            {{-- Tab System - Page ile birebir aynı --}}
            <x-tab-system :tabs="[
                ['name' => 'Temel Bilgiler', 'id' => '0'],
                ['name' => 'SEO', 'id' => '1'],
                ['name' => 'Kod', 'id' => '2']
            ]" :tab-completion="[
                '0' => true,
                '1' => false,
                '2' => false
            ]" storage-key="page_active_tab">
                {{-- Language Switcher - Page ile aynı --}}
                <x-manage.language.switcher :current-language="'tr'" />
            </x-tab-system>
            
            <div class="card-body">
                <div class="tab-content" id="contentTabContent">
                    {{-- Temel Bilgiler Tab --}}
                    <div class="tab-pane fade show active" id="0" role="tabpanel">
                        {{-- Turkish Content --}}
                        <div class="language-content" data-language="tr" style="display: block;">
                            
                            {{-- Başlık ve Slug alanları - Page ile aynı --}}
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" 
                                               placeholder="Test Sayfa Başlığı"
                                               value="Test Modal 1 - Modern Premium Design">
                                        <label>
                                            Başlık
                                            <span class="required-star">★</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                               maxlength="255" 
                                               placeholder="sayfa-url-slug"
                                               value="test-modal-1-premium">
                                        <label>
                                            Sayfa URL Slug
                                            <small class="text-muted ms-2">- Otomatik oluşturulur</small>
                                        </label>
                                        <div class="form-text">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>URL dostu adres
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Content Editor with AI Translation Button - Page ile birebir aynı yapı --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label">
                                        İçerik
                                        <span class="required-star">★</span>
                                    </label>
                                    {{-- AI Translation Button - gerçek Page'deki gibi --}}
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openModernTranslationModal()">
                                        <i class="fa-solid fa-language me-1"></i>
                                        AI Çeviri
                                    </button>
                                </div>
                                
                                @include('admin.components.content-editor', [
                                    'lang' => 'tr',
                                    'langName' => 'Türkçe',
                                    'langData' => [
                                        'body' => '<h2>Test İçeriği - Modern Premium Modal</h2><p>Bu test sayfası gerçek Page yönetim sayfasının birebir aynısıdır. AI çeviri modal testleri için kullanılmaktadır.</p><p>Modern Premium tasarım özellikleri:</p><ul><li>Glass morphism effects</li><li>Premium gradient backgrounds</li><li>Brain pulse animations</li><li>Real-time progress tracking</li></ul>'
                                    ],
                                    'fieldName' => 'body',
                                    'label' => '', // Label'ı üstte gösterdik
                                    'placeholder' => 'Sayfa içeriğinizi buraya yazın...'
                                ])
                            </div>
                        </div>

                        {{-- Aktif/Pasif - Page ile aynı --}}
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" checked />
                                <div class="state p-success p-on ms-2">
                                    <label>Aktif</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>Pasif</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SEO Tab - Page ile aynı --}}
                    <div class="tab-pane fade" id="1" role="tabpanel">
                        {{-- SEO Management Component burada olur --}}
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" placeholder="Meta başlık" value="Test Modal 1 - Modern Premium AI Translation">
                            <label>Meta Başlık</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" style="height: 100px;" placeholder="Meta açıklama">Modern premium AI translation modal test sayfası. Glass morphism ve premium tasarım özellikleri.</textarea>
                            <label>Meta Açıklama</label>
                        </div>
                    </div>

                    {{-- Code Tab - Page ile aynı --}}
                    <div class="tab-pane fade" id="2" role="tabpanel">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" data-bs-toggle="autosize" placeholder="CSS kodu"></textarea>
                            <label>CSS</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" data-bs-toggle="autosize" placeholder="JavaScript kodu"></textarea>
                            <label>JavaScript</label>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Form Footer - Page ile aynı --}}
            <x-form-footer route="admin.page" :model-id="null" />

        </div>
    </form>

</div>

{{-- Test Modal 1: Modern Premium --}}
@include('admin.modal-tests.modals.modern-premium')
@endsection

@push('scripts')
<script>
    // Page yönetim sayfasıyla aynı JS yapısı
    window.currentPageId = null;
    window.currentLanguage = 'tr';
    
    console.log('🔍 Modal Test 1 - Page Layout:', {
        currentPageId: window.currentPageId,
        currentLanguage: window.currentLanguage
    });

    // Livewire benzeri event listener simulation
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 Page-style modal test 1 loaded');
    });
</script>
@endpush