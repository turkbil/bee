@extends('admin.layout')
@section('title', 'Modal Test 3 - Analytics Dashboard')

@section('content')
@php
    View::share('pretitle', 'Modal Test 3 - Analytics Dashboard');
@endphp

<div>
    @include('page::admin.helper')
    
    <form method="post">
        <div class="card">
            
            <x-tab-system :tabs="[
                ['name' => 'Temel Bilgiler', 'id' => '0'],
                ['name' => 'SEO', 'id' => '1'],
                ['name' => 'Kod', 'id' => '2']
            ]" :tab-completion="[
                '0' => true,
                '1' => false,
                '2' => false
            ]" storage-key="page_active_tab">
                <x-manage.language.switcher :current-language="'tr'" />
            </x-tab-system>
            
            <div class="card-body">
                <div class="tab-content" id="contentTabContent">
                    <div class="tab-pane fade show active" id="0" role="tabpanel">
                        <div class="language-content" data-language="tr" style="display: block;">
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" 
                                               placeholder="Test Sayfa Başlığı"
                                               value="Test Modal 3 - Analytics Dashboard">
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
                                               value="test-modal-3-analytics">
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

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label">
                                        İçerik
                                        <span class="required-star">★</span>
                                    </label>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openAnalyticsTranslationModal()">
                                        <i class="fa-solid fa-language me-1"></i>
                                        AI Çeviri
                                    </button>
                                </div>
                                
                                @include('admin.components.content-editor', [
                                    'lang' => 'tr',
                                    'langName' => 'Türkçe',
                                    'langData' => [
                                        'body' => '<h2>Test İçeriği - Analytics Dashboard</h2><p>Bu test sayfası Analytics Dashboard modal tasarımını gösterir.</p><p>Özellikler:</p><ul><li>Fullscreen modal</li><li>Real-time metrics</li><li>Performance analytics</li><li>Translation history</li></ul>'
                                    ],
                                    'fieldName' => 'body',
                                    'label' => '',
                                    'placeholder' => 'Sayfa içeriğinizi buraya yazın...'
                                ])
                            </div>
                        </div>

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

                    <div class="tab-pane fade" id="1" role="tabpanel">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" placeholder="Meta başlık" value="Test Modal 3 - Analytics Dashboard">
                            <label>Meta Başlık</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" style="height: 100px;" placeholder="Meta açıklama">Analytics dashboard AI translation modal test sayfası.</textarea>
                            <label>Meta Açıklama</label>
                        </div>
                    </div>

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

            <x-form-footer route="admin.page" :model-id="null" />

        </div>
    </form>

</div>

@include('admin.modal-tests.modals.analytics-dashboard')
@endsection

@push('scripts')
<script>
    window.currentPageId = null;
    window.currentLanguage = 'tr';
    
    console.log('🔍 Modal Test 3 - Analytics Dashboard:', {
        currentPageId: window.currentPageId,
        currentLanguage: window.currentLanguage
    });

    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 Page-style modal test 3 loaded');
    });
</script>
@endpush