@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Ayarları')
@section('title', 'Soru ve Token Limitleri')

@section('content')
    <div class="row">
        <div class="col-3">
            @include('ai::admin.settings.sidebar')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hourglass-half me-2"></i>
                        Soru ve Token Limitleri
                    </h3>
                </div>
                <form method="POST" action="{{ route('admin.ai.settings.limits.update') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <h4 class="alert-title">Bilgi</h4>
                            Bu ayarlar müşterilerin AI kullanımını sınırlamak ve token maliyetlerinizi kontrol etmek için kullanılır.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('max_question_length') is-invalid @enderror" 
                                           name="max_question_length" id="max_question_length" placeholder="2000"
                                           value="{{ old('max_question_length', $settings->max_question_length ?? 2000) }}">
                                    <label for="max_question_length">Maksimum Soru Uzunluğu (Karakter)</label>
                                    @error('max_question_length')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Kullanıcıların girebileceği maksimum karakter sayısı</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('max_daily_questions') is-invalid @enderror" 
                                           name="max_daily_questions" id="max_daily_questions" placeholder="50"
                                           value="{{ old('max_daily_questions', $settings->max_daily_questions ?? 50) }}">
                                    <label for="max_daily_questions">Günlük Maksimum Soru Sayısı</label>
                                    @error('max_daily_questions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Kullanıcı başına günlük soru limiti</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('max_monthly_questions') is-invalid @enderror" 
                                           name="max_monthly_questions" id="max_monthly_questions" placeholder="1000"
                                           value="{{ old('max_monthly_questions', $settings->max_monthly_questions ?? 1000) }}">
                                    <label for="max_monthly_questions">Aylık Maksimum Soru Sayısı</label>
                                    @error('max_monthly_questions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Kullanıcı başına aylık soru limiti</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('question_token_limit') is-invalid @enderror" 
                                           name="question_token_limit" id="question_token_limit" placeholder="500"
                                           value="{{ old('question_token_limit', $settings->question_token_limit ?? 500) }}">
                                    <label for="question_token_limit">Soru Token Limiti</label>
                                    @error('question_token_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Tek bir sorunun maksimum token sayısı (~4 karakter = 1 token)</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('free_question_tokens_daily') is-invalid @enderror" 
                                           name="free_question_tokens_daily" id="free_question_tokens_daily" placeholder="1000"
                                           value="{{ old('free_question_tokens_daily', $settings->free_question_tokens_daily ?? 1000) }}">
                                    <label for="free_question_tokens_daily">Günlük Ücretsiz Soru Token Kotası</label>
                                    @error('free_question_tokens_daily')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Kullanıcı başına günlük ücretsiz soru token sayısı</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" name="charge_question_tokens" value="1" 
                                           id="charge_question_tokens" {{ old('charge_question_tokens', $settings->charge_question_tokens ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="charge_question_tokens">
                                        Soru tokenları müşteriye faturalandırılsın
                                    </label>
                                </div>
                                <div class="form-hint text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Kapalıysa sadece AI yanıtları faturalandırılır (önerilen)
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <h4 class="alert-title">Maliyet Kontrolü Önerisi</h4>
                            <ul class="mb-0">
                                <li><strong>Soru Token Faturalaması:</strong> Kapalı tutmanız önerilir - sadece AI yanıtları için ücret alın</li>
                                <li><strong>Günlük Ücretsiz Kota:</strong> Müşteri deneyimi için makul bir limit belirleyin</li>
                                <li><strong>Maksimum Soru Uzunluğu:</strong> Çok uzun sorularla token israfını önleyin</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Limit Ayarlarını Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection