@extends('admin.layout')

@include('ai::admin.helper')

@section('pretitle', 'AI Ayarları')
@section('title', 'Genel Ayarlar')

@section('content')
    <div class="row">
        <div class="col-3">
            @include('ai::admin.settings.sidebar')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog me-2"></i>
                        Genel AI Ayarları
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h4 class="alert-title">Bilgi</h4>
                        Bu sayfada AI sisteminin genel davranış ayarlarını yapılandırabilirsiniz.
                    </div>

                    <form method="POST" action="{{ route('admin.ai.settings.general.update') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-12 mb-4">
                                <h4>Sistem Davranışı</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-control @error('default_language') is-invalid @enderror" 
                                            name="default_language" id="default_language">
                                        <option value="tr" {{ ($settings->default_language ?? 'tr') == 'tr' ? 'selected' : '' }}>
                                            Türkçe
                                        </option>
                                        <option value="en" {{ ($settings->default_language ?? '') == 'en' ? 'selected' : '' }}>
                                            English
                                        </option>
                                    </select>
                                    <label for="default_language">Varsayılan Dil</label>
                                    @error('default_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-control @error('response_format') is-invalid @enderror" 
                                            name="response_format" id="response_format">
                                        <option value="markdown" {{ ($settings->response_format ?? 'markdown') == 'markdown' ? 'selected' : '' }}>
                                            Markdown
                                        </option>
                                        <option value="plain" {{ ($settings->response_format ?? '') == 'plain' ? 'selected' : '' }}>
                                            Düz Metin
                                        </option>
                                        <option value="html" {{ ($settings->response_format ?? '') == 'html' ? 'selected' : '' }}>
                                            HTML
                                        </option>
                                    </select>
                                    <label for="response_format">Yanıt Formatı</label>
                                    @error('response_format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <h4>Performans Ayarları</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('cache_duration') is-invalid @enderror" 
                                           name="cache_duration" id="cache_duration" placeholder="60"
                                           value="{{ old('cache_duration', $settings->cache_duration ?? 60) }}" 
                                           min="0">
                                    <label for="cache_duration">Cache Süresi (dakika)</label>
                                    @error('cache_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">0 = Cache kullanma</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('concurrent_requests') is-invalid @enderror" 
                                           name="concurrent_requests" id="concurrent_requests" placeholder="5"
                                           value="{{ old('concurrent_requests', $settings->concurrent_requests ?? 5) }}" 
                                           min="1">
                                    <label for="concurrent_requests">Eş Zamanlı İstek Limiti</label>
                                    @error('concurrent_requests')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Aynı anda kaç AI isteği işlenebilir</div>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <h4>Güvenlik Ayarları</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="content_filtering" value="1" 
                                           id="content_filtering" {{ old('content_filtering', $settings->content_filtering ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="content_filtering">
                                        Zararlı içerik filtrelemesi aktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="rate_limiting" value="1" 
                                           id="rate_limiting" {{ old('rate_limiting', $settings->rate_limiting ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rate_limiting">
                                        İstek sınırlaması aktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <h4>Loglama & İzleme</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="detailed_logging" value="1" 
                                           id="detailed_logging" {{ old('detailed_logging', $settings->detailed_logging ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="detailed_logging">
                                        Tüm AI isteklerini kaydet
                                    </label>
                                </div>
                                <div class="form-hint text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Disk alanı kullanımını artırır
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="performance_monitoring" value="1" 
                                           id="performance_monitoring" {{ old('performance_monitoring', $settings->performance_monitoring ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="performance_monitoring">
                                        Yanıt sürelerini takip et
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Genel Ayarları Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection